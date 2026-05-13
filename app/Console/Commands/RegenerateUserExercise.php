<?php

namespace App\Console\Commands;

use App\Models\ExercisePlan;
use App\Models\User;
use App\Services\Ai\AiServiceException;
use App\Services\Ai\ExerciseService;
use Illuminate\Console\Command;

class RegenerateUserExercise extends Command
{
    protected $signature = 'exercise:regenerate {user_id : Database id of the user whose workout plan to regenerate}';

    protected $description = 'Regenerate the weekly exercise plan for a user using their saved profile + last-used preferences.';

    public function handle(ExerciseService $exercise): int
    {
        $userId = (int) $this->argument('user_id');
        $user = User::find($userId);
        if (!$user) {
            $this->error("User {$userId} not found.");
            return self::FAILURE;
        }

        // Mirror the payload shape /api/ai/exercise/generate builds — pull
        // profile from User, then layer last-used preferences from the
        // previous plan so the new plan keeps the user's goal/equipment.
        $profile = [
            'body_weight' => $user->weight !== null ? (string) $user->weight : null,
            'height' => $user->height !== null ? (string) $user->height : null,
            'age' => $user->age !== null ? (string) $user->age : null,
            'sex' => $user->gender ?? null,
            'fitness_goals' => null,
            'exercise_preference' => null,
            'time_available' => null,
            'available_days' => null,
            'equipment_available' => null,
            'body_part_focus' => null,
            'limitations' => null,
        ];
        $lastPlan = ExercisePlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if ($lastPlan && is_array($lastPlan->request_payload)) {
            $lastProfile = $lastPlan->request_payload['profile'] ?? [];
            foreach (['fitness_goals', 'exercise_preference', 'time_available',
                      'available_days', 'equipment_available', 'body_part_focus',
                      'limitations'] as $k) {
                if (!empty($lastProfile[$k])) {
                    $profile[$k] = $lastProfile[$k];
                }
            }
        }

        $payload = [
            'profile' => $profile,
            'query' => 'Create a fresh weekly exercise routine',
        ];

        $this->info("[" . now()->toIso8601String() . "] regen exercise for user={$userId}");

        try {
            $result = $exercise->generatePlan($payload);
        } catch (AiServiceException $e) {
            $this->error('Exercise service error: ' . $e->getMessage());
            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return self::FAILURE;
        }

        try {
            $plan = ExercisePlan::create([
                'user_id' => $userId,
                'title' => \Illuminate\Support\Str::limit(
                    (string) ($result['assessment'] ?? 'Exercise plan'), 80
                ),
                'fitness_goals' => $profile['fitness_goals'] ?? null,
                'available_days' => $profile['available_days'] ?? null,
                'request_payload' => $payload,
                'payload' => $result,
            ]);
        } catch (\Throwable $e) {
            $this->error('Persist failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("[" . now()->toIso8601String() . "] regen done — exercise_plan id={$plan->id}");
        return self::SUCCESS;
    }
}
