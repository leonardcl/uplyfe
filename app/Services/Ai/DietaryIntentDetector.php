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
        $msg = ' ' . strtolower($message) . ' ';
        // Strip punctuation that would break word boundaries.
        $msg = preg_replace('/[\.,!\?]/', ' ', $msg);

        $exclude = [];
        $include = [];

        // EXCLUSION patterns. Each capture group 1 is the food phrase.
        $excludePatterns = [
            // "I can't eat fish", "I cannot eat dairy", "I can not eat nuts"
            '/\bi\s+(?:can\'?t|cannot|can\s+not|do\s*n\'?t|do\s*not)\s+eat\s+([a-z][a-z\s\-]{1,40}?)(?:\s+(?:please|anymore|right\s+now)|\s*$|\s+(?:because|since|as|so|but|and|;))/i',
            // "I'm allergic to peanuts", "I am allergic to shellfish"
            '/\bi[\'a]*m?\s+(?:am\s+)?allergic\s+to\s+([a-z][a-z\s\-]{1,40}?)(?:\s*$|\s+(?:because|since|as|so|but|and|;))/i',
            // "no fish in my meals", "without dairy", "remove pork"
            '/\b(?:no|without|remove|skip|avoid|exclude)\s+([a-z][a-z\s\-]{1,40}?)(?:\s+(?:in|from|for)\b|\s*$|\s+(?:please|anymore|right\s+now))/i',
            // "I don't like fish" — soft preference
            '/\bi\s+(?:do\s*n\'?t|don\'?t|do\s+not)\s+(?:like|want)\s+([a-z][a-z\s\-]{1,40}?)(?:\s*$|\s+(?:in|because|since|so))/i',
        ];

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
        foreach ($parts as $part) {
            $part = trim($part);
            // Strip leading articles / qualifiers.
            $part = preg_replace('/^(?:any|all|much|too\s+much|a|an|the|some|lots\s+of|a\s+lot\s+of|kinds?\s+of)\s+/', '', $part);
            $part = trim($part);
            if ($part === '' || in_array($part, $stopwords, true)) continue;
            if (mb_strlen($part) > 32) continue; // probably swept up too much
            if (mb_strlen($part) < 3) continue;  // single-letter / "ok"
            $out[] = $part;
        }
        return $out;
    }
}
