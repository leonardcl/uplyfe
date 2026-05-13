<?php

namespace App\Services\Ai;

/**
 * Light intent classifier for chat messages. Decides whether the user is
 * asking for a recipe / meal suggestion or a workout suggestion, and (for
 * recipes) which meal slot they care about.
 *
 * Deliberately regex-based: false positives are worse than false negatives.
 * If a request isn't matched, the chat answers as normal text.
 */
class RecommendationIntentDetector
{
    /**
     * Returns:
     *   [
     *     'recipe'    => bool,
     *     'meal_type' => 'breakfast'|'lunch'|'dinner'|'snack'|null,
     *     'exercise'  => bool,
     *   ]
     */
    public function detect(string $message): array
    {
        $msg = ' ' . mb_strtolower($message) . ' ';
        $msg = preg_replace('/[\.,!\?]/', ' ', $msg);

        // "Give me a…" verbs.
        $ask = '(?:suggest|recommend|propose|show|give|find|need|want|pick|share|build|make|check|tell)';
        // "What is / what's my…" lookups.
        $lookup = '(?:what\s+(?:is|are|s)|what\'s|tell\s+me|show\s+me|check)\s+(?:my\s+|the\s+|todays\s+|today\'s\s+)?';
        $maybe = '(?:can\s+you\s+|could\s+you\s+|please\s+|i\s+(?:want|need|would\s+like)\s+)?';

        $slotWord = '(?P<slot>breakfast|lunch|dinner|snack|meal|recipe|dish)';

        $recipePatterns = [
            // "Suggest me a dinner" / "Give me a recipe"
            '/\b' . $maybe . $ask . '\s+(?:me\s+)?(?:a\s+|an\s+|some\s+|the\s+|my\s+)?(?:new\s+|fresh\s+|healthy\s+|quick\s+|todays\s+|today\'s\s+)?' . $slotWord . '/i',
            // "What's my dinner" / "Check my lunch" / "Show today's breakfast"
            '/\b' . $lookup . '(?:available\s+(?:for\s+)?(?:my\s+)?)?' . $slotWord . '/i',
            // "My dinner today" / "My recipe for today"
            '/\bmy\s+' . $slotWord . '\b/i',
        ];

        $exercisePatterns = [
            '/\b' . $maybe . $ask . '\s+(?:me\s+)?(?:a\s+|an\s+|some\s+|the\s+|my\s+)?(?:new\s+|fresh\s+|quick\s+|short\s+|todays\s+|today\'s\s+)?(?:workout|exercise|routine|training|fitness\s+plan)/i',
            '/\b' . $lookup . '(?:workout|exercise|routine|training)/i',
            '/\bmy\s+(?:workout|exercise|routine)\b/i',
        ];

        $recipe = false;
        $mealType = null;
        foreach ($recipePatterns as $rx) {
            if (preg_match($rx, $msg, $m)) {
                $recipe = true;
                $slot = mb_strtolower($m['slot'] ?? '');
                if (in_array($slot, ['breakfast', 'lunch', 'dinner', 'snack'], true)) {
                    $mealType = $slot;
                    break;
                }
            }
        }
        // Standalone "for dinner / for lunch / tonight" hints when the
        // recipe verb was matched without a slot (e.g. "give me a recipe
        // for my dinner" — captures 'recipe', then we look for "dinner").
        if ($recipe && !$mealType) {
            // Catches: "for dinner", "for my dinner", "for the dinner",
            // "at dinner", "as my dinner", "as a dinner".
            if (preg_match('/\b(?:for|at|as)\s+(?:my\s+|the\s+|a\s+|an\s+)?(breakfast|lunch|dinner|snack)\b/i', $msg, $m2)) {
                $mealType = mb_strtolower($m2[1]);
            } elseif (preg_match('/\b(breakfast|lunch|dinner|snack)\b/i', $msg, $m2)) {
                // Last resort: any occurrence of a meal name anywhere in the
                // message. Safe because we only reach here if a recipe verb
                // already matched.
                $mealType = mb_strtolower($m2[1]);
            } elseif (preg_match('/\btonight\b/i', $msg)) {
                $mealType = 'dinner';
            } elseif (preg_match('/\bthis\s+morning\b/i', $msg)) {
                $mealType = 'breakfast';
            }
        }
        // Catch "what should I eat tonight / for dinner" style too.
        if (!$recipe && preg_match('/\bwhat\s+(?:should|can|could|do)\s+i\s+(?:eat|have|cook|make)\b/i', $msg)) {
            $recipe = true;
            if (preg_match('/\b(breakfast|lunch|dinner|snack)\b/i', $msg, $m2)) {
                $mealType = mb_strtolower($m2[1]);
            }
        }

        $exercise = false;
        foreach ($exercisePatterns as $rx) {
            if (preg_match($rx, $msg)) { $exercise = true; break; }
        }
        if (!$exercise && preg_match('/\bwhat\s+(?:should|can|could)\s+i\s+(?:do|train|work\s+out)\b/i', $msg)) {
            $exercise = true;
        }

        return [
            'recipe' => $recipe,
            'meal_type' => $mealType,
            'exercise' => $exercise,
        ];
    }
}
