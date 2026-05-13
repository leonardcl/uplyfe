<?php

namespace App\Console\Commands;

use App\Models\MealPlan;
use App\Models\User;
use App\Services\Ai\AiServiceException;
use App\Services\Ai\RecipeService;
use Illuminate\Console\Command;

class RegenerateUserMenu extends Command
{
    protected $signature = 'menu:regenerate {user_id : Database id of the user whose weekly menu to regenerate}';

    protected $description = 'Regenerate the active weekly meal plan for a user using their saved exclusions / preferences.';

    public function handle(RecipeService $recipe): int
    {
        $userId = (int) $this->argument('user_id');
        $user = User::find($userId);
        if (!$user) {
            $this->error("User {$userId} not found.");
            return self::FAILURE;
        }

        // Build a request that mirrors what the /api/ai/recipe/weekly-menu
        // controller would have sent, but pulls exclusions from the user.
        $request = [
            'target_calories' => 2000,
            'servings' => 1,
            'diet' => is_array($user->dietary_preferences) && !empty($user->dietary_preferences)
                ? (string) ($user->dietary_preferences[0] ?? 'none')
                : (string) ($user->dietary_preferences ?? 'none'),
            'allergies' => is_array($user->food_exclusions) ? $user->food_exclusions : [],
            'days' => 7,
        ];

        $this->info("[" . now()->toIso8601String() . "] regen for user={$userId} exclusions=" . json_encode($request['allergies']));

        try {
            $payload = $recipe->generateWeeklyMenu($request);
        } catch (AiServiceException $e) {
            $this->error('Recipe service error: ' . $e->getMessage());
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return self::FAILURE;
        }

        try {
            $plan = MealPlan::create([
                'user_id' => $userId,
                'span' => 'weekly',
                'target_calories' => $request['target_calories'],
                'diet' => $request['diet'],
                'request_payload' => $request,
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            $this->error('Persist failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("[" . now()->toIso8601String() . "] regen done — meal_plan id={$plan->id}");
        return self::SUCCESS;
    }
}
