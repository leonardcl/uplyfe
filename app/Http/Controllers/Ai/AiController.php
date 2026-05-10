<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Services\Ai\AiClient;
use App\Services\Ai\AiServiceException;
use App\Services\Ai\ExerciseService;
use App\Services\Ai\HealthCheckupService;
use App\Services\Ai\RecipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(
        protected AiClient $client,
        protected HealthCheckupService $healthCheckup,
        protected ExerciseService $exercise,
        protected RecipeService $recipe,
    ) {}

    public function health(): JsonResponse
    {
        try {
            return response()->json($this->client->get('/healthz'));
        } catch (AiServiceException $e) {
            return response()->json(['status' => 'unreachable', 'error' => $e->getMessage()], 502);
        }
    }

    public function healthCheckupSample(Request $request): JsonResponse
    {
        $useLlm = filter_var($request->query('use_llm', 'true'), FILTER_VALIDATE_BOOLEAN);
        $useRag = filter_var($request->query('use_rag', 'true'), FILTER_VALIDATE_BOOLEAN);

        return $this->call(fn () => $this->healthCheckup->sample($useLlm, $useRag));
    }

    public function healthCheckupSchema(): JsonResponse
    {
        return $this->call(fn () => $this->healthCheckup->schema());
    }

    public function healthCheckupProbe(): JsonResponse
    {
        return $this->call(fn () => $this->healthCheckup->probe());
    }

    public function healthCheckupManual(Request $request): JsonResponse
    {
        $data = $request->validate([
            'panel' => 'required|array',
            'use_llm' => 'sometimes|boolean',
            'use_rag' => 'sometimes|boolean',
        ]);

        return $this->call(fn () => $this->healthCheckup->analyzeManual(
            panel: $data['panel'],
            useLlm: $data['use_llm'] ?? true,
            useRag: $data['use_rag'] ?? true,
        ));
    }

    public function healthCheckupUpload(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg,tif,tiff|max:20480',
            'use_llm' => 'sometimes|boolean',
            'use_rag' => 'sometimes|boolean',
        ]);

        $upload = $data['file'];

        return $this->call(fn () => $this->healthCheckup->analyzeUpload(
            absolutePath: $upload->getRealPath(),
            filename: $upload->getClientOriginalName(),
            useLlm: $data['use_llm'] ?? true,
            useRag: $data['use_rag'] ?? true,
        ));
    }

    public function exerciseGenerate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'goal' => 'sometimes|in:lose_weight,build_muscle,improve_endurance,general_fitness',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'days_per_week' => 'sometimes|integer|min:1|max:7',
            'minutes_per_session' => 'sometimes|integer|min:10|max:180',
            'equipment' => 'sometimes|array',
            'equipment.*' => 'string',
            'limitations' => 'sometimes|array',
            'limitations.*' => 'string',
        ]);

        return $this->call(fn () => $this->exercise->generatePlan($data));
    }

    public function recipeDailyMenu(Request $request): JsonResponse
    {
        $data = $request->validate([
            'target_calories' => 'sometimes|integer|min:1000|max:4000',
            'diet' => 'sometimes|in:none,vegetarian,vegan,pescatarian,halal,kosher,keto,low_carb',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'string',
            'cuisine_preferences' => 'sometimes|array',
            'cuisine_preferences.*' => 'string',
            'servings' => 'sometimes|integer|min:1|max:10',
            'notes' => 'sometimes|nullable|string|max:500',
        ]);

        return $this->call(fn () => $this->recipe->generateDailyMenu($data));
    }

    protected function call(\Closure $fn): JsonResponse
    {
        // The AI service can take 30–90s for LLM calls. PHP's default
        // max_execution_time of 30s would kill the request mid-flight and
        // return ERR_EMPTY_RESPONSE to the browser. Lift the cap for AI calls
        // only — every other request keeps the default protection.
        @set_time_limit(180);

        try {
            return response()->json($fn());
        } catch (AiServiceException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 502);
        }
    }
}
