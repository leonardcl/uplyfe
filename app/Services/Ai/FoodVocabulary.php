<?php

namespace App\Services\Ai;

/**
 * Allow-list of plausible food terms. Used to sanity-check anything the
 * LLM or regex detector wants to save as a `food_exclusion`. If neither
 * the exact term nor its synonyms appear here, we reject it — that's
 * what stops "workout routine" from being saved as a food.
 *
 * Deliberately broad (categories AND common species), but strictly NOT
 * including verbs, scheduling words, or app-vocabulary terms.
 */
class FoodVocabulary
{
    /** Canonical food categories + common foods. */
    public const FOODS = [
        // Animal proteins / categories
        'meat', 'red meat', 'beef', 'pork', 'lamb', 'veal', 'venison', 'goat',
        'chicken', 'turkey', 'duck', 'poultry', 'game',
        'bacon', 'ham', 'sausage', 'salami', 'prosciutto', 'chorizo',
        // Fish & seafood
        'fish', 'seafood', 'shellfish',
        'salmon', 'tuna', 'trout', 'cod', 'halibut', 'mackerel', 'sardine',
        'sardines', 'anchovy', 'anchovies', 'tilapia', 'haddock', 'snapper',
        'bass', 'catfish', 'pollock', 'flounder', 'sole', 'swordfish', 'mahi',
        'shrimp', 'prawn', 'prawns', 'lobster', 'crab', 'clam', 'clams',
        'mussel', 'mussels', 'oyster', 'oysters', 'scallop', 'scallops',
        'squid', 'octopus', 'calamari', 'crayfish',
        // Dairy & eggs
        'dairy', 'milk', 'cheese', 'butter', 'cream', 'yogurt', 'yoghurt',
        'whey', 'casein', 'ghee', 'paneer', 'cottage cheese', 'feta',
        'parmesan', 'mozzarella', 'cheddar',
        'egg', 'eggs',
        // Grains / gluten
        'gluten', 'wheat', 'barley', 'rye', 'oats', 'oat',
        'bread', 'pasta', 'flour', 'farro', 'rice', 'quinoa', 'corn',
        // Legumes
        'beans', 'lentils', 'chickpeas', 'soy', 'soya', 'tofu', 'tempeh',
        'edamame', 'miso', 'soybean', 'peanut', 'peanuts', 'peanut butter',
        // Nuts & seeds
        'nuts', 'almond', 'almonds', 'walnut', 'walnuts', 'pecan', 'pecans',
        'cashew', 'cashews', 'hazelnut', 'hazelnuts', 'pistachio', 'pistachios',
        'brazil nut', 'brazil nuts', 'macadamia', 'pine nut', 'pine nuts',
        'sesame', 'sunflower seeds', 'pumpkin seeds', 'chia', 'flax', 'flaxseed',
        // Veg / fruit categories (rarely excluded but valid)
        'mushroom', 'mushrooms', 'onion', 'onions', 'garlic', 'tomato',
        'tomatoes', 'pepper', 'avocado', 'banana', 'apple', 'strawberry',
        'strawberries', 'pineapple', 'mango', 'kiwi', 'citrus',
        // Other common allergens / preferences
        'alcohol', 'wine', 'beer', 'coffee', 'caffeine', 'sugar', 'salt',
        'spicy', 'spicy food', 'chocolate', 'honey',
        'pork products', 'beef products',
    ];

    /** Set lookup for O(1) checks. */
    protected static ?array $set = null;

    public static function set(): array
    {
        if (self::$set === null) {
            self::$set = array_flip(array_map('mb_strtolower', self::FOODS));
        }
        return self::$set;
    }

    /**
     * Is the given term a plausible food? Accepts exact matches AND any
     * multi-word phrase where every token is itself a known food (so
     * "salmon fillet" is rejected — "fillet" isn't a food — but
     * "chicken thigh" is rejected for the same reason, by design we'd
     * rather under-accept than save garbage).
     */
    public static function isFood(string $term): bool
    {
        $term = mb_strtolower(trim($term));
        if ($term === '') return false;
        $set = self::set();
        if (isset($set[$term])) return true;
        // Accept hyphenated joiners ("low-fat dairy" → reject; "peanut-butter" → accept).
        if (strpos($term, '-') !== false) {
            $joined = str_replace('-', ' ', $term);
            if (isset($set[$joined])) return true;
        }
        return false;
    }

    /**
     * Filter a list of candidate food terms down to ones we recognise.
     */
    public static function filter(array $terms): array
    {
        $out = [];
        $seen = [];
        foreach ($terms as $t) {
            $low = mb_strtolower(trim((string) $t));
            if ($low === '' || isset($seen[$low])) continue;
            if (self::isFood($low)) {
                $out[] = $low;
                $seen[$low] = true;
            }
        }
        return $out;
    }
}
