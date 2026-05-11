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
        // Accept the kebab-case names the frontend form posts, plus their
        // snake_case equivalents for direct API callers. All fields optional;
        // we fall back to the session user's profile (weight/height/age) when
        // the form doesn't supply them.
        $data = $request->validate([
            'equipment'           => 'sometimes',           // array OR free-text string
            'equipment_available' => 'sometimes|string',
            'fitness_goals'       => 'sometimes|string',
            'fitness-goals'       => 'sometimes|string',
            'body_part_focus'     => 'sometimes',
            'body-focus'          => 'sometimes',           // array OR string from checkboxes
            'available_days'      => 'sometimes|string',
            'available-days'      => 'sometimes|string',
            'time_available'      => 'sometimes|string',
            'time-available'      => 'sometimes|string',
            'exercise_preference' => 'sometimes|string',
            'exercise-preference' => 'sometimes|string',
            'limitations'         => 'sometimes',           // array OR string
            'query'               => 'sometimes|string',
        ]);

        // Pull profile fields from the session user (set by AuthController)
        // so we always have body_weight/height/age/sex without asking the
        // frontend to re-send them.
        $sessionUser = $request->session()->get('user');
        $bodyWeight = $sessionUser?->weight !== null ? (string) $sessionUser->weight : null;
        $height = $sessionUser?->height !== null ? (string) $sessionUser->height : null;
        $age = $sessionUser?->age !== null ? (string) $sessionUser->age : null;
        $sex = $sessionUser?->gender ?? null;

        $profile = [
            'body_weight'         => $bodyWeight,
            'height'              => $height,
            'age'                 => $age,
            'sex'                 => $sex,
            'fitness_goals'       => $data['fitness_goals'] ?? $data['fitness-goals'] ?? null,
            'exercise_preference' => $data['exercise_preference'] ?? $data['exercise-preference'] ?? null,
            'time_available'      => $data['time_available'] ?? $data['time-available'] ?? null,
            'available_days'      => $data['available_days'] ?? $data['available-days'] ?? null,
            'equipment_available' => $this->coerceList($data['equipment_available'] ?? $data['equipment'] ?? null),
            'body_part_focus'     => $this->coerceList($data['body_part_focus'] ?? $data['body-focus'] ?? null),
            'limitations'         => $this->coerceList($data['limitations'] ?? null),
        ];

        $payload = [
            'profile' => $profile,
            'query'   => $data['query'] ?? 'Create a weekly exercise routine',
        ];

        // Lift PHP timeout — LLM stage 3 can take 30-90s.
        @set_time_limit(300);

        return $this->call(fn () => $this->exercise->generatePlan($payload));
    }

    /**
     * The gateway accepts free-text equipment / body-focus / limitations.
     * Convert arrays (from form checkboxes) into a comma-separated string
     * so a single call signature works for both.
     */
    protected function coerceList($v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_array($v)) {
            return implode(', ', array_filter(array_map('strval', $v), fn ($s) => $s !== ''));
        }
        return (string) $v;
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
