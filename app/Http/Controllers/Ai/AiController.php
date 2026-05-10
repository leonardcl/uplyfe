<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\HealthReport;
use App\Models\User;
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

        // Cap PHP execution for AI calls (LLM step takes 30–90s).
        @set_time_limit(180);

        try {
            $payload = $this->healthCheckup->analyzeUpload(
                absolutePath: $upload->getRealPath(),
                filename: $upload->getClientOriginalName(),
                useLlm: $data['use_llm'] ?? true,
                useRag: $data['use_rag'] ?? true,
            );
        } catch (AiServiceException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 502);
        }

        // Persist the report so the user can see their history later. user_id
        // is null for anonymous uploads — saves don't fail when the session
        // isn't carrying a user.
        $userId = $this->sessionUserId($request);
        try {
            $report = HealthReport::fromGatewayResponse(
                userId: $userId,
                filename: $upload->getClientOriginalName() ?? 'upload.pdf',
                sizeBytes: (int) ($upload->getSize() ?? 0),
                payload: $payload,
            );
            $report->save();
            // Surface the new id back so the frontend can link to /health-reports/{id}.
            $payload['_report_id'] = $report->id;
        } catch (\Throwable $e) {
            // Persistence failures must NOT block the analysis from being
            // shown to the user. Log via Laravel and continue.
            \Log::warning('HealthReport save failed: ' . $e->getMessage());
        }

        return response()->json($payload);
    }

    public function listReports(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['reports' => []]);
        }
        $reports = HealthReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get([
                'id', 'original_filename', 'original_size_bytes',
                'overall_severity', 'biomarker_count', 'abnormal_count',
                'critical_count', 'summary', 'created_at',
            ]);
        return response()->json(['reports' => $reports]);
    }

    public function showReport(Request $request, int $id): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        $report = HealthReport::find($id);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }
        // Only the report's owner can view it. Anonymous reports (user_id null)
        // are visible to no one — they exist only as orphaned audit records.
        if ($report->user_id === null || $report->user_id !== $userId) {
            return response()->json(['error' => 'Not authorized to view this report'], 403);
        }
        return response()->json($report->payload + ['_report_id' => $report->id]);
    }

    /**
     * Pull the logged-in user's id out of the session, if any.
     * Returns null when the session has no user (anonymous request).
     */
    protected function sessionUserId(Request $request): ?int
    {
        try {
            $user = $request->session()->get('user');
        } catch (\Throwable $e) {
            return null;
        }
        if ($user instanceof User) {
            return $user->id;
        }
        if (is_array($user) && isset($user['id'])) {
            return (int) $user['id'];
        }
        return null;
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
