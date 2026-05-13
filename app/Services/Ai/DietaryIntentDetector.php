<?php

namespace App\Services\Ai;

/**
 * Cheap, rule-based detector that extracts dietary exclusions / additions
 * from a free-text chat message. Runs BEFORE the LLM so the assistant's
 * reply can acknowledge a change the user has just made.
 *
 * This is deliberately conservative — false positives are worse than false
 * negatives. If the regex doesn't catch it, the LLM still answers normally
 * and the user can rephrase.
 */
class DietaryIntentDetector
{
    /**
     * Returns:
     *   [
     *     'exclude' => ['fish', 'shellfish'],   // foods to add to exclusions
     *     'include' => ['eggs'],                // foods to remove from exclusions
     *   ]
     * Either list may be empty.
     */
    public function detect(string $message): array
    {
        $raw = trim($message);
        $msg = ' ' . strtolower($message) . ' ';
        // Strip punctuation that would break word boundaries.
        $msg = preg_replace('/[\.,!\?]/', ' ', $msg);

        // Hard guard: questions are NOT declarations. "Why no workout today?"
        // must not match "no <food>" patterns. Detect by leading question
        // word or a question mark anywhere in the original message.
        $firstWord = strtolower(preg_split('/\s+/', ltrim($raw))[0] ?? '');
        $questionStarters = [
            'why', 'what', 'whats', "what's", 'where', 'when', 'how', 'who',
            'which', 'is', 'are', 'was', 'were', 'do', 'does', 'did', 'can',
            'could', 'should', 'would', 'will', 'may', 'might',
        ];
        $isQuestion = in_array($firstWord, $questionStarters, true)
            || str_contains($raw, '?');
        // Exception: "i'm allergic to X" inside a longer question-ish text
        // is still a declaration, so we DON'T early-return — we just skip
        // the looser "no X" branch later.

        $exclude = [];
        $include = [];

        // EXCLUSION patterns. Each capture group 1 is the food phrase.
        // Boundary tokens that terminate a captured food phrase. Keeping
        // them in one place + reusing in every pattern avoids the bug
        // where "I cannot eat peanuts either" was captured as
        // "peanuts either".
        $stop = '(?:please|anymore|right\s+now|either|too|as\s+well|though|now|today|tomorrow|tonight|because|since|as|so|but|and|or|;|,)';

        // High-precision patterns — these are declarations of dietary intent
        // (subject + verb), so they're safe to run even inside a question.
        $strictPatterns = [
            // "I can't eat fish", "I cannot eat dairy", "I can not eat nuts"
            '/\bi\s+(?:can\'?t|cannot|can\s+not|do\s*n\'?t|do\s*not)\s+eat\s+([a-z][a-z\s\-]{1,40}?)(?:\s+' . $stop . '|\s*$)/i',
            // "I'm allergic to peanuts", "I am allergic to shellfish"
            '/\bi[\'a]*m?\s+(?:am\s+)?allergic\s+to\s+([a-z][a-z\s\-]{1,40}?)(?:\s+' . $stop . '|\s*$)/i',
            // "I don't like fish" — soft preference
            '/\bi\s+(?:do\s*n\'?t|don\'?t|do\s+not)\s+(?:like|want)\s+([a-z][a-z\s\-]{1,40}?)(?:\s+' . $stop . '|\s*$)/i',
        ];
        // Loose patterns — these can over-match on questions like
        // "why no workout today?" so we only run them on declarations.
        $loosePatterns = [
            // "no fish in my meals", "without dairy", "remove pork"
            '/\b(?:no|without|remove|skip|avoid|exclude)\s+([a-z][a-z\s\-]{1,40}?)(?:\s+(?:in|from|for)\b|\s+' . $stop . '|\s*$)/i',
        ];

        $excludePatterns = $isQuestion ? $strictPatterns : array_merge($strictPatterns, $loosePatterns);

        foreach ($excludePatterns as $rx) {
            if (preg_match_all($rx, $msg, $matches)) {
                foreach ($matches[1] as $phrase) {
                    foreach ($this->normalizeFoods($phrase) as $food) {
                        $exclude[] = $food;
                    }
                }
            }
        }

        // INCLUSION patterns (undo a previous exclusion).
        $includePatterns = [
            '/\bi\s+can\s+eat\s+([a-z][a-z\s\-]{1,40}?)(?:\s+now|\s*$|\s+again)/i',
            '/\b(?:add\s+back|allow|include)\s+([a-z][a-z\s\-]{1,40}?)(?:\s*$|\s+(?:please|again))/i',
            '/\bi\s+(?:can\s+now\s+eat|am\s+okay\s+with|am\s+fine\s+with)\s+([a-z][a-z\s\-]{1,40}?)(?:\s*$|\s+(?:again|now))/i',
        ];
        foreach ($includePatterns as $rx) {
            if (preg_match_all($rx, $msg, $matches)) {
                foreach ($matches[1] as $phrase) {
                    foreach ($this->normalizeFoods($phrase) as $food) {
                        $include[] = $food;
                    }
                }
            }
        }

        return [
            'exclude' => array_values(array_unique($exclude)),
            'include' => array_values(array_unique($include)),
        ];
    }

    /**
     * A captured phrase like "fish and shellfish" or "any seafood" becomes
     * a list of canonical food keywords. Drops obvious non-foods so we
     * don't save garbage like "anymore" or "everything".
     */
    protected function normalizeFoods(string $phrase): array
    {
        $phrase = trim(preg_replace('/\s+/', ' ', strtolower($phrase)));
        if ($phrase === '') return [];

        // Split on "and", "or", commas.
        $parts = preg_split('/\s*(?:,|\band\b|\bor\b)\s*/', $phrase);
        $out = [];
        $stopwords = [
            'anything', 'everything', 'all', 'any', 'something', 'much', 'lots',
            'right', 'really', 'just', 'food', 'foods', 'stuff', 'things',
            'a lot of', 'too much', 'the', 'my', 'your', 'his', 'her',
            'them', 'it', 'meat from', 'this', 'that', 'these', 'those',
        ];
        // Words that mean it's NOT a food — covers the "no workout today"
        // class of bug where the regex captured "workout routine". If the
        // phrase contains any of these, we reject the whole capture.
        $nonFoodMarkers = [
            'workout', 'workouts', 'exercise', 'exercises', 'routine', 'routines',
            'training', 'session', 'sessions', 'class', 'classes',
            'plan', 'plans', 'schedule', 'schedules', 'reminder', 'reminders',
            'recipe', 'recipes', 'menu', 'menus', 'breakfast', 'lunch', 'dinner', 'snack',
            'time', 'energy', 'idea', 'ideas', 'tip', 'tips', 'advice', 'help',
            'change', 'changes', 'update', 'updates', 'option', 'options',
            'one', 'ones', 'thing', 'message', 'reply', 'chat',
            'today', 'tomorrow', 'tonight', 'yesterday', 'morning', 'afternoon', 'evening',
            'problem', 'issue', 'bug', 'error',
        ];
        foreach ($parts as $part) {
            $part = trim($part);
            // Strip leading articles / qualifiers.
            $part = preg_replace('/^(?:any|all|much|too\s+much|a|an|the|some|lots\s+of|a\s+lot\s+of|kinds?\s+of)\s+/', '', $part);
            $part = trim($part);
            if ($part === '' || in_array($part, $stopwords, true)) continue;
            if (mb_strlen($part) > 32) continue; // probably swept up too much
            if (mb_strlen($part) < 3) continue;  // single-letter / "ok"
            // Reject anything containing a non-food marker word.
            $tokens = preg_split('/\s+/', $part) ?: [];
            $isNonFood = false;
            foreach ($tokens as $tok) {
                if (in_array($tok, $nonFoodMarkers, true)) { $isNonFood = true; break; }
            }
            if ($isNonFood) continue;
            $out[] = $part;
        }
        return $out;
    }
}
