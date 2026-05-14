<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ExercisePlan;
use App\Models\HealthReport;
use App\Models\LikedMeal;
use App\Models\MealPlan;
use App\Models\User;
use App\Services\Ai\AiClient;
use App\Services\Ai\AiServiceException;
use App\Services\Ai\ChatService;
use App\Services\Ai\DietaryIntentDetector;
use App\Services\Ai\ExerciseService;
use App\Services\Ai\FoodVocabulary;
use App\Services\Ai\HealthCheckupService;
use App\Services\Ai\RecipeService;
use App\Services\Ai\RecommendationIntentDetector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(
        protected AiClient $client,
        protected HealthCheckupService $healthCheckup,
        protected ExerciseService $exercise,
        protected RecipeService $recipe,
        protected ChatService $chat,
        protected DietaryIntentDetector $dietaryIntent,
        protected RecommendationIntentDetector $recommendIntent,
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
     * Serve an exercise image or animated demo from the local dataset.
     *
     * Files are named `<id>-<hash>.<ext>` under
     *   ai-service/exercise-routine-generator/rag_sources/exercises-dataset/{images|videos}/
     * where IDs are 4-digit zero-padded strings. We glob on the prefix because
     * the random hash suffix isn't predictable from the id alone.
     *
     * The `videos/` folder confusingly contains `.gif` files (animated
     * demos), while `images/` has the static `.jpg`. Endpoint serves the
     * GIF by default since it's much more useful for "how do I do this?".
     */
    public function exerciseImage(string $id, string $kind = 'gif'): \Symfony\Component\HttpFoundation\Response
    {
        $id = preg_replace('/[^0-9]/', '', $id);
        if ($id === '' || strlen($id) > 6) {
            abort(404);
        }
        $padded = str_pad($id, 4, '0', STR_PAD_LEFT);
        $base = base_path('ai-service/exercise-routine-generator/rag_sources/exercises-dataset');
        $folder = $kind === 'jpg' ? '/images' : '/videos';
        $ext = $kind === 'jpg' ? 'jpg' : 'gif';
        // Glob with the id prefix; pick the first match (there should only be one).
        $matches = glob("{$base}{$folder}/{$padded}-*.{$ext}");
        if (!$matches) {
            abort(404);
        }
        return response()->file($matches[0], [
            'Content-Type' => $ext === 'gif' ? 'image/gif' : 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400, immutable',
        ]);
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

        try {
            $result = $this->exercise->generatePlan($payload);
        } catch (AiServiceException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 502);
        }

        $userId = $this->sessionUserId($request);
        if ($userId !== null) {
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
                $result['_plan_id'] = $plan->id;
                $result['_created_at'] = optional($plan->created_at)->toISOString();
                $result['_request_payload'] = $payload;
            } catch (\Throwable $e) {
                \Log::warning('ExercisePlan save failed: ' . $e->getMessage());
            }
        }

        return response()->json($result);
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
            'target_calories' => 'sometimes|integer|min:800|max:5000',
            'diet' => 'sometimes|in:none,vegetarian,vegan,pescatarian,halal,kosher,keto,low_carb',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'string',
            'cuisine_preferences' => 'sometimes|array',
            'cuisine_preferences.*' => 'string',
            'servings' => 'sometimes|integer|min:1|max:10',
            'notes' => 'sometimes|nullable|string|max:500',
            // New fields supported by the recipe-generator package:
            'query' => 'sometimes|nullable|string|max:300',
            'days' => 'sometimes|integer|min:1|max:7',
        ]);

        // Pull user profile from session to personalize when fields aren't sent.
        $sessionUser = $request->session()->get('user');
        if ($sessionUser) {
            // Default dietary_preferences from the user's profile if the
            // request didn't override them.
            if (empty($data['diet']) && !empty($sessionUser->dietary_preferences)) {
                // dietary_preferences may be an array; pick a sensible single value.
                $first = is_array($sessionUser->dietary_preferences)
                    ? ($sessionUser->dietary_preferences[0] ?? null)
                    : $sessionUser->dietary_preferences;
                if (is_string($first)) {
                    $data['diet'] = $first;
                }
            }
        }

        // LLM stages can take 60-180s for a weekly plan.
        @set_time_limit(300);

        $data['allergies'] = $this->mergeUserExclusions($request, $data['allergies'] ?? []);

        return $this->saveMealPlan(
            request: $request,
            data: $data,
            span: 'daily',
            generator: fn () => $this->recipe->generateDailyMenu($data),
        );
    }

    public function recipeWeeklyMenu(Request $request): JsonResponse
    {
        $data = $request->validate([
            'target_calories' => 'sometimes|integer|min:800|max:5000',
            'diet' => 'sometimes|in:none,vegetarian,vegan,pescatarian,halal,kosher,keto,low_carb',
            'allergies' => 'sometimes|array',
            'allergies.*' => 'string',
            'cuisine_preferences' => 'sometimes|array',
            'cuisine_preferences.*' => 'string',
            'servings' => 'sometimes|integer|min:1|max:10',
            'notes' => 'sometimes|nullable|string|max:500',
            'query' => 'sometimes|nullable|string|max:300',
        ]);
        $data['days'] = 7;
        $data['allergies'] = $this->mergeUserExclusions($request, $data['allergies'] ?? []);

        $sessionUser = $request->session()->get('user');
        if ($sessionUser && empty($data['diet']) && !empty($sessionUser->dietary_preferences)) {
            $first = is_array($sessionUser->dietary_preferences)
                ? ($sessionUser->dietary_preferences[0] ?? null)
                : $sessionUser->dietary_preferences;
            if (is_string($first)) {
                $data['diet'] = $first;
            }
        }

        // 7 days × 4 meals × LLM call can take 5-15 minutes.
        @set_time_limit(900);

        return $this->saveMealPlan(
            request: $request,
            data: $data,
            span: 'weekly',
            generator: fn () => $this->recipe->generateWeeklyMenu($data),
        );
    }

    /**
     * Run the meal-plan generator, persist the result for logged-in users,
     * and return the merged payload (with `_plan_id` when saved).
     */
    protected function saveMealPlan(Request $request, array $data, string $span, \Closure $generator): JsonResponse
    {
        try {
            $result = $generator();
        } catch (AiServiceException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 502);
        }

        $userId = $this->sessionUserId($request);
        if ($userId !== null) {
            try {
                $plan = MealPlan::create([
                    'user_id' => $userId,
                    'span' => $span,
                    'target_calories' => $data['target_calories'] ?? null,
                    'diet' => $data['diet'] ?? null,
                    'request_payload' => $data,
                    'payload' => $result,
                ]);
                $result['_plan_id'] = $plan->id;
                $result['span'] = $span;
            } catch (\Throwable $e) {
                \Log::warning('MealPlan save failed: ' . $e->getMessage());
            }
        }

        return response()->json($result);
    }

    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'sometimes|nullable|integer',
            // Browser-side history is still accepted as a fallback for
            // anonymous chats; persisted conversations override it.
            'history' => 'sometimes|array|max:20',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:4000',
        ]);

        $userId = $this->sessionUserId($request);
        $conversation = $this->resolveConversation($userId, $data['conversation_id'] ?? null);

        // Build a compact "what we know about this user" block the gateway
        // injects into the system prompt. Without this the chat can only
        // answer in the abstract — it has no idea about the user's profile
        // or their saved health-checkup data.
        $userContext = $this->buildUserContext($userId);

        // Persist the USER turn before the LLM call. If the browser disconnects
        // mid-generation, the user message is still in the DB and the next
        // poll/page-load will see a "waiting for assistant" tail.
        if ($conversation) {
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'user',
                'content' => $data['message'],
            ]);
            if (empty($conversation->title)) {
                $conversation->title = \Illuminate\Support\Str::limit($data['message'], 60);
            }
            $conversation->last_message_at = now();
            $conversation->save();
        }

        // Build LLM history from the persisted conversation (10 most recent
        // turns, including the user message we just inserted). Fall back to
        // the client-supplied history when we have no conversation.
        // When an assistant turn had attached cards (recipe / exercise /
        // dietary), append a brief textual summary so the LLM "remembers"
        // what it actually showed and can answer follow-ups about it.
        $history = $conversation
            ? $conversation->messages()->latest('id')->limit(11)->get()
                ->reverse()
                ->slice(0, -1) // drop the just-inserted user message; it's $data['message']
                ->map(function ($m) {
                    $content = (string) $m->content;
                    if ($m->role === 'assistant' && is_array($m->cards) && $m->cards) {
                        $content .= "\n\n" . $this->cardsToHistorySummary($m->cards);
                    }
                    return ['role' => $m->role, 'content' => $content];
                })
                ->values()->all()
            : ($data['history'] ?? []);

        // Keep the PHP process alive even if the browser disconnects so the
        // LLM call finishes and the assistant reply gets persisted. Without
        // this, navigating away would orphan the user turn.
        @ignore_user_abort(true);
        @set_time_limit(180);

        $reply = '';
        $error = null;
        try {
            $result = $this->chat->send(
                message: $data['message'],
                history: $history,
                userContext: $userContext,
            );
            $reply = (string) ($result['reply'] ?? '');
        } catch (AiServiceException $e) {
            $error = $e->getMessage();
            $result = ['error' => $error];
            // Persist a synthetic assistant message so the UI can recover
            // gracefully on the next poll instead of waiting forever.
            $reply = "Sorry — something went wrong: {$error}";
        }

        // LLM-FIRST DIETARY ROUTING. The chat LLM emits structured intent
        // (intent.dietary_change.exclude / include). We trust that first;
        // the regex detector runs as a backup for misses. Every candidate
        // is sanity-checked against the FoodVocabulary allow-list so we
        // never save "workout routine" or other non-food captures.
        $llmIntent = is_array($result['intent'] ?? null) ? $result['intent'] : null;
        $dietaryChange = $this->applyDietaryIntent($userId, $data['message'], $llmIntent);

        // Detect "I'm vegan/vegetarian/etc." declarations and persist them
        // to dietary_preferences so the meal plan generator picks them up.
        if ($userId !== null) {
            $this->applyDietTypeDeclaration($userId, $data['message'], $llmIntent);
        }

        // LLM-routed: regenerate the workout plan when the user asks for a
        // fresh routine. The chat reply mentions the regen so the user
        // doesn't think we ignored them.
        $workoutRegen = false;
        if ($userId !== null && ($llmIntent['regenerate_workout'] ?? false)) {
            $spawned = $this->spawnWorkoutRegen($userId);
            $workoutRegen = $spawned;
            $reply = $spawned
                ? "✓ Got it — I'm generating a fresh weekly workout for you in the background (takes a couple of minutes). Open /exercise once it's ready.\n\n" . $reply
                : "⚠ Background jobs are disabled on this server — please ask your admin to enable shell_exec.\n\n" . $reply;
            $result['reply'] = $reply;
            if ($spawned) $result['workout_regen'] = true;
        }

        // Same path for meal plans — "give me a fresh week of meals".
        // LLM-first, with a regex fallback for phrasings it missed.
        $wantsMenuRegen = (bool) ($llmIntent['regenerate_menu'] ?? false);
        if (!$wantsMenuRegen) {
            $wantsMenuRegen = (bool) preg_match(
                '/\b(?:regenerate|recreate|redo|refresh|rebuild|new|fresh)\s+(?:my\s+|this\s+)?(?:weekly\s+)?(?:meal\s+plan|menu|meals|recipes|week(?:\s+meal)?)\b/i',
                $data['message']
            );
        }
        // Same for workout — catch missed LLM emissions.
        if (!($llmIntent['regenerate_workout'] ?? false)) {
            $wantsWorkoutRegen = (bool) preg_match(
                '/\b(?:regenerate|redo|refresh|rebuild|new|fresh|change)\s+(?:my\s+)?(?:weekly\s+)?(?:workout|exercise|routine|training|fitness\s+plan)\b/i',
                $data['message']
            );
            if ($wantsWorkoutRegen && $userId !== null && !$workoutRegen) {
                $spawned = $this->spawnWorkoutRegen($userId);
                $reply = $spawned
                    ? "✓ Got it — I'm generating a fresh weekly workout for you in the background (takes a couple of minutes). Open /exercise once it's ready.\n\n" . $reply
                    : "⚠ Background jobs are disabled on this server — cannot start workout regen.\n\n" . $reply;
                $result['reply'] = $reply;
                if ($spawned) $result['workout_regen'] = true;
                $workoutRegen = $spawned;
            }
        }
        if ($userId !== null && $wantsMenuRegen && !$dietaryChange['acted']) {
            $spawned = $this->spawnWeeklyMenuRegen($userId);
            $reply = $spawned
                ? "✓ Regenerating your weekly meal plan in the background (takes 5-7 minutes). Open /recipe once it's ready.\n\n" . $reply
                : "⚠ Background jobs are disabled on this server — cannot start meal plan regen.\n\n" . $reply;
            $result['reply'] = $reply;
            if ($spawned) $result['menu_regen'] = true;
        }

        // If we acted on a dietary change, prepend a short confirmation so
        // the user knows the system actually did something — not just that
        // the assistant talked about it.
        if ($dietaryChange['acted']) {
            $reply = $dietaryChange['notice'] . "\n\n" . $reply;
            $result['reply'] = $reply;
            $result['dietary_update'] = $dietaryChange;
        }

        // Recommendation cards. When the user asked "give me a recipe" or
        // "suggest a workout", we attach functional cards so they can see
        // (and like / open) a real item from their saved plans instead of
        // just reading a paragraph about it. The LLM's own intent
        // detection (returned in the chat response) acts as a fallback
        // for phrasings the regex detector missed.
        // Skip cards when regen was triggered WITHOUT an explicit show-card
        // request in the same message. If the user said "give me a new workout
        // AND show today's dinner", we still show the dinner card even though
        // workout regen is in flight (the dinner card is from the meal plan,
        // which didn't change).
        $llmIntent = is_array($result['intent'] ?? null) ? $result['intent'] : null;
        $justRegenned = !empty($result['menu_regen']) || !empty($result['workout_regen']);
        $alsoWantsCard = ($llmIntent['wants_recipe'] ?? false) || ($llmIntent['wants_workout'] ?? false)
            || ($llmIntent['show_week'] ?? false);
        $cards = ($justRegenned && !$alsoWantsCard) ? [] : $this->buildRecommendationCards($userId, $data['message'], $llmIntent);

        // When we just updated the user's exclusions, prepend a small
        // confirmation card showing the current list + a link to the recipe
        // page (which will refresh on its own once the regen finishes).
        if ($dietaryChange['acted']) {
            array_unshift($cards, $this->buildExclusionsCard($dietaryChange));
        }

        if ($cards) {
            $result['cards'] = $cards;
        }

        if ($conversation) {
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $reply,
                'cards' => !empty($cards) ? $cards : null,
            ]);
            $conversation->last_message_at = now();
            $conversation->save();
            $result['conversation_id'] = $conversation->id;
        }

        // Kick off the regen LAST so the response goes back to the user
        // before we tie up the gateway with another long LLM call.
        if ($dietaryChange['regenerate']) {
            $this->spawnWeeklyMenuRegen($userId);
        }

        if ($error !== null) {
            return response()->json(['error' => $error, 'conversation_id' => $conversation?->id], 502);
        }
        return response()->json($result);
    }

    /**
     * Apply a dietary intent (if any) to the user's saved food_exclusions.
     * Returns:
     *   [
     *     'acted'      => bool,       // did we change anything?
     *     'regenerate' => bool,       // should we spawn a weekly-menu regen?
     *     'added'      => string[],
     *     'removed'    => string[],
     *     'notice'     => string,     // human-readable confirmation
     *   ]
     */
    protected function applyDietaryIntent(?int $userId, string $message, ?array $llmIntent = null): array
    {
        $result = ['acted' => false, 'regenerate' => false, 'added' => [], 'removed' => [], 'notice' => '', 'source' => null];
        if ($userId === null) return $result;

        // LLM-first: take the structured dietary_change from the chat reply.
        $llmExclude = [];
        $llmInclude = [];
        if (is_array($llmIntent['dietary_change'] ?? null)) {
            $llmExclude = is_array($llmIntent['dietary_change']['exclude'] ?? null)
                ? $llmIntent['dietary_change']['exclude'] : [];
            $llmInclude = is_array($llmIntent['dietary_change']['include'] ?? null)
                ? $llmIntent['dietary_change']['include'] : [];
        }
        // Regex fallback: only run when the LLM didn't catch anything.
        $regexExclude = [];
        $regexInclude = [];
        if (!$llmExclude && !$llmInclude) {
            $regex = $this->dietaryIntent->detect($message);
            $regexExclude = $regex['exclude'];
            $regexInclude = $regex['include'];
        }
        $rawExclude = array_merge($llmExclude, $regexExclude);
        $rawInclude = array_merge($llmInclude, $regexInclude);

        // VOCABULARY FILTER — final safety net. Only known foods get through.
        $intent = [
            'exclude' => FoodVocabulary::filter($rawExclude),
            'include' => FoodVocabulary::filter($rawInclude),
        ];
        $result['source'] = $llmExclude || $llmInclude ? 'llm' : ($regexExclude || $regexInclude ? 'regex' : null);
        if (!$intent['exclude'] && !$intent['include']) {
            // We may have HAD candidates that the vocabulary rejected. Log
            // those for debugging without saving them.
            if ($rawExclude || $rawInclude) {
                \Log::info('Dietary intent rejected by vocabulary filter', [
                    'user_id' => $userId,
                    'raw_exclude' => $rawExclude,
                    'raw_include' => $rawInclude,
                ]);
            }
            return $result;
        }

        $user = User::find($userId);
        if (!$user) return $result;

        $current = is_array($user->food_exclusions) ? $user->food_exclusions : [];
        $currentLower = array_map('mb_strtolower', $current);

        $added = [];
        foreach ($intent['exclude'] as $food) {
            if (!in_array(mb_strtolower($food), $currentLower, true)) {
                $current[] = $food;
                $currentLower[] = mb_strtolower($food);
                $added[] = $food;
            }
        }

        // Build a reverse map: synonym → parent category.
        // e.g. "salmon" → "fish", "mozzarella" → "dairy"
        // Used so "I can eat salmon again" correctly removes a stored "fish" exclusion.
        static $reverseMap = null;
        if ($reverseMap === null) {
            $reverseMap = [];
            foreach (static::expandExclusionSynonyms(array_keys([
                'fish'=>1,'seafood'=>1,'shellfish'=>1,'dairy'=>1,'gluten'=>1,
                'nuts'=>1,'peanut'=>1,'soy'=>1,'pork'=>1,'beef'=>1,'chicken'=>1,
                'egg'=>1,'meat'=>1,'red meat'=>1,
            ])) as $syn) { /* populated below */ }
            $synMap = [
                'fish'=>['salmon','tuna','trout','cod','halibut','mackerel','sardine','anchovy','tilapia','haddock','snapper','bass','catfish','pollock','flounder','sole','swordfish','mahi'],
                'seafood'=>['fish','salmon','tuna','trout','cod','shrimp','prawn','lobster','crab','clam','mussel','oyster','squid','octopus','scallop','calamari','anchovy','sardine'],
                'shellfish'=>['shrimp','prawn','lobster','crab','clam','mussel','oyster','scallop','crayfish'],
                'dairy'=>['milk','cheese','butter','cream','yogurt','yoghurt','whey','casein','ghee','paneer','mozzarella','cheddar','feta','parmesan'],
                'gluten'=>['wheat','barley','rye','bread','pasta','flour','farro'],
                'nuts'=>['almond','walnut','pecan','cashew','hazelnut','pistachio','brazil nut','macadamia','pine nut'],
                'peanut'=>['peanuts','peanut butter','groundnut'],
                'soy'=>['soya','tofu','tempeh','edamame','miso','soybean'],
                'pork'=>['bacon','ham','prosciutto','sausage','pancetta','chorizo','salami'],
                'beef'=>['steak','brisket','roast beef','meatloaf'],
                'meat'=>['beef','pork','chicken','lamb','bacon','sausage','ham','turkey','mutton'],
            ];
            foreach ($synMap as $parent => $synonyms) {
                foreach ($synonyms as $syn) {
                    $reverseMap[$syn] = $reverseMap[$syn] ?? $parent;
                }
            }
        }

        $removed = [];
        foreach ($intent['include'] as $food) {
            $low = mb_strtolower($food);
            // Direct match first.
            $idx = array_search($low, $currentLower, true);
            if ($idx !== false) {
                $removed[] = $current[$idx];
                array_splice($current, $idx, 1);
                array_splice($currentLower, $idx, 1);
                continue;
            }
            // Synonym match: "I can eat salmon" → remove stored "fish" exclusion.
            $parent = $reverseMap[$low] ?? null;
            if ($parent !== null) {
                $idx = array_search($parent, $currentLower, true);
                if ($idx !== false) {
                    $removed[] = $current[$idx];
                    array_splice($current, $idx, 1);
                    array_splice($currentLower, $idx, 1);
                }
            }
        }

        if (!$added && !$removed) return $result;

        $user->food_exclusions = array_values($current);
        $user->save();

        // Mirror back into the session user so subsequent requests in the
        // same session see the update without a fresh login.
        $sessionUser = request()->session()->get('user');
        if ($sessionUser instanceof User && $sessionUser->id === $user->id) {
            $sessionUser->food_exclusions = $user->food_exclusions;
            request()->session()->put('user', $sessionUser);
        }

        $parts = [];
        if ($added) $parts[] = 'added ' . implode(', ', $added);
        if ($removed) $parts[] = 'removed ' . implode(', ', $removed);
        $notice = '✓ Updated your food exclusions: ' . implode('; ', $parts) . '.';

        // Trigger a regen only when there's an active plan to refresh.
        $hasPlan = MealPlan::where('user_id', $userId)->exists();
        if ($hasPlan) {
            $notice .= ' Regenerating your weekly menu in the background (a few minutes) — your /recipe page will update on its own.';
        }

        return [
            'acted' => true,
            'regenerate' => $hasPlan,
            'added' => $added,
            'removed' => $removed,
            'notice' => $notice,
            'exclusions_now' => array_values($current),
            // Which detector caught this — surfaced for debugging / UX
            // (e.g. show a "detected by AI router" badge later).
            'source' => $result['source'] ?? null,
        ];
    }

    /**
     * Detect statements like "I'm vegan", "I went vegetarian", "I follow a
     * keto diet" and save the diet type to dietary_preferences. This is
     * distinct from food_exclusions (handled by applyDietaryIntent).
     */
    protected function applyDietTypeDeclaration(int $userId, string $message, ?array $llmIntent = null): void
    {
        $allowed = ['vegan','vegetarian','pescatarian','halal','kosher','keto','low_carb'];

        // LLM-first: trust the structured diet_type field.
        $matched = null;
        $llmDietType = $llmIntent['diet_type'] ?? null;
        if (is_string($llmDietType) && in_array($llmDietType, $allowed, true)) {
            $matched = $llmDietType;
        }

        // Regex fallback when the LLM didn't emit diet_type.
        if ($matched === null) {
            $regexPatterns = [
                'vegan'       => 'vegan',
                'vegetarian'  => 'vegetarian',
                'pescatarian' => 'pescatarian',
                'halal'       => 'halal',
                'kosher'      => 'kosher',
                'keto'        => 'keto',
                'low.?carb'   => 'low_carb',
            ];
            $msg = mb_strtolower($message);
            $declarationPattern = '/\b(?:i\'?m|i\s+am|i\s+went|i\s+follow(?:\s+a)?|i\s+eat|i\s+switched\s+to|i\s+became?|make\s+me|set\s+me\s+as)\s+(?:a\s+|an\s+)?/u';
            $isDiet = preg_match('/\b(?:diet\s+is|eating\s+(?:vegan|vegetarian|keto|halal|kosher|pescatarian|low.?carb))\b/u', $msg);
            if (preg_match($declarationPattern, $msg) || $isDiet) {
                foreach ($regexPatterns as $pattern => $canonical) {
                    if (preg_match('/\b' . $pattern . '\b/u', $msg)) {
                        $matched = $canonical;
                        break;
                    }
                }
            }
        }

        if ($matched === null) return;

        $user = User::find($userId);
        if (!$user) return;

        $current = is_array($user->dietary_preferences) ? $user->dietary_preferences : [];
        if (in_array($matched, $current, true)) return; // already set

        $user->dietary_preferences = [$matched];
        $user->save();

        // Mirror into session.
        $sessionUser = request()->session()->get('user');
        if ($sessionUser instanceof User && $sessionUser->id === $user->id) {
            $sessionUser->dietary_preferences = $user->dietary_preferences;
            request()->session()->put('user', $sessionUser);
        }

        \Log::info('Diet type declaration saved', ['user_id' => $userId, 'diet' => $matched]);
    }

    /**
     * Kick off a weekly-menu regeneration as a detached background process
     * so the chat reply can return immediately. We use an artisan command
     * (php artisan menu:regenerate {userId}) and disown it via shell.
     */
    protected function spawnWeeklyMenuRegen(int $userId): bool
    {
        return $this->spawnArtisan('menu:regenerate', $userId, 'menu-regen.log');
    }

    protected function spawnWorkoutRegen(int $userId): bool
    {
        return $this->spawnArtisan('exercise:regenerate', $userId, 'exercise-regen.log');
    }

    /**
     * Fire an artisan command as a detached background process so the
     * parent HTTP request returns immediately. nohup + & keeps it alive
     * past the parent process exit; output is appended to storage/logs.
     */
    protected function spawnArtisan(string $command, int $userId, string $log): bool
    {
        if (!function_exists('shell_exec') || in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
            \Log::error("spawnArtisan: shell_exec is disabled — cannot run {$command} for user {$userId}");
            return false;
        }
        $artisan = base_path('artisan');
        $php = PHP_BINARY ?: 'php';
        $logPath = storage_path('logs/' . $log);
        $cmd = sprintf(
            'nohup %s %s %s %d >> %s 2>&1 &',
            escapeshellarg($php),
            escapeshellarg($artisan),
            escapeshellarg($command),
            $userId,
            escapeshellarg($logPath),
        );
        @shell_exec($cmd);
        return true;
    }

    public function listConversations(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['conversations' => []]);
        }
        $conversations = ChatConversation::where('user_id', $userId)
            ->orderByDesc('last_message_at')
            ->limit(50)
            ->get(['id', 'title', 'last_message_at', 'created_at']);
        return response()->json(['conversations' => $conversations]);
    }

    public function showConversation(Request $request, int $id): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        $conv = ChatConversation::find($id);
        if (!$conv || $conv->user_id !== $userId) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }
        $messages = $conv->messages()->get(['id', 'role', 'content', 'cards', 'created_at']);
        return response()->json([
            'id' => $conv->id,
            'title' => $conv->title,
            'messages' => $messages,
        ]);
    }

    public function deleteConversation(Request $request, int $id): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        $conv = ChatConversation::find($id);
        if (!$conv || $conv->user_id !== $userId) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }
        $conv->delete();
        return response()->json(['deleted' => true]);
    }

    public function listExercisePlans(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['plans' => []]);
        }
        $plans = ExercisePlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'title', 'fitness_goals', 'available_days', 'created_at']);
        return response()->json(['plans' => $plans]);
    }

    public function showExercisePlan(Request $request, int $id): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        $plan = ExercisePlan::find($id);
        if (!$plan || $plan->user_id !== $userId) {
            return response()->json(['error' => 'Plan not found'], 404);
        }
        return response()->json($plan->payload + [
            '_plan_id' => $plan->id,
            '_created_at' => optional($plan->created_at)->toISOString(),
            '_request_payload' => $plan->request_payload ?? [],
        ]);
    }

    public function listMealPlans(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['plans' => []]);
        }
        $plans = MealPlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'span', 'target_calories', 'diet', 'created_at']);
        return response()->json(['plans' => $plans]);
    }

    public function showMealPlan(Request $request, int $id): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        $plan = MealPlan::find($id);
        if (!$plan || $plan->user_id !== $userId) {
            return response()->json(['error' => 'Plan not found'], 404);
        }
        return response()->json($plan->payload + ['_plan_id' => $plan->id, 'span' => $plan->span]);
    }

    /**
     * Resolve the meal plan to display on /recipe for the current week.
     * Picks the most recent weekly plan; falls back to the most recent
     * daily plan. Returns 404 (with an empty body) if the user has no
     * plans yet — the page treats that as "click Generate."
     */
    public function activeMealPlan(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['plan' => null], 200);
        }
        $plan = MealPlan::where('user_id', $userId)
            ->where('span', 'weekly')
            ->orderByDesc('created_at')
            ->first()
            ?? MealPlan::where('user_id', $userId)
                ->orderByDesc('created_at')
                ->first();
        if (!$plan) {
            return response()->json(['plan' => null], 200);
        }
        return response()->json([
            'plan_id' => $plan->id,
            'span' => $plan->span,
            'created_at' => $plan->created_at,
            'payload' => $plan->payload,
        ]);
    }

    public function profileMe(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['error' => 'Not logged in'], 401);
        }
        $u = User::find($userId);
        if (!$u) return response()->json(['error' => 'Not found'], 404);
        return response()->json([
            'id' => $u->id,
            'first_name' => $u->first_name,
            'last_name' => $u->last_name,
            'email' => $u->email,
            'phone_number' => $u->phone_number,
            'date_of_birth' => $u->date_of_birth,
            'gender' => $u->gender,
            'age' => $u->age,
            'height' => $u->height,
            'weight' => $u->weight,
            'profile_photo' => $u->profile_photo,
            'dietary_preferences' => $u->dietary_preferences ?? [],
            'food_exclusions' => $u->food_exclusions ?? [],
            'calorie_goal' => (int) ($u->calorie_goal ?? 2000),
            'created_at' => optional($u->created_at)->toISOString(),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['error' => 'Not logged in'], 401);
        }
        $u = User::find($userId);
        if (!$u) return response()->json(['error' => 'Not found'], 404);

        $data = $request->validate([
            'calorie_goal' => 'sometimes|integer|min:800|max:5000',
        ]);

        $u->fill($data)->save();

        return response()->json(['ok' => true, 'calorie_goal' => (int) $u->calorie_goal]);
    }

    public function listLikedMeals(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['likes' => []]);
        }
        $likes = LikedMeal::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get(['id', 'meal_plan_id', 'day_key', 'meal_type', 'title', 'snapshot', 'created_at']);
        return response()->json(['likes' => $likes]);
    }

    public function likeMeal(Request $request): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) {
            return response()->json(['error' => 'Login required'], 401);
        }
        $data = $request->validate([
            'meal_plan_id' => 'sometimes|nullable|integer',
            'day_key' => 'sometimes|nullable|string|max:32',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'title' => 'required|string|max:255',
            'snapshot' => 'required|array',
        ]);
        // upsert-ish: if the user already liked this exact slot, return it.
        $existing = LikedMeal::where('user_id', $userId)
            ->where('meal_plan_id', $data['meal_plan_id'] ?? null)
            ->where('day_key', $data['day_key'] ?? null)
            ->where('meal_type', $data['meal_type'])
            ->first();
        if ($existing) {
            return response()->json(['like' => $existing, 'created' => false]);
        }
        $like = LikedMeal::create([
            'user_id' => $userId,
            'meal_plan_id' => $data['meal_plan_id'] ?? null,
            'day_key' => $data['day_key'] ?? null,
            'meal_type' => $data['meal_type'],
            'title' => $data['title'],
            'snapshot' => $data['snapshot'],
        ]);
        return response()->json(['like' => $like, 'created' => true], 201);
    }

    public function unlikeMeal(Request $request, int $id): JsonResponse
    {
        $userId = $this->sessionUserId($request);
        $like = LikedMeal::find($id);
        if (!$like || $like->user_id !== $userId) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $like->delete();
        return response()->json(['deleted' => true]);
    }

    /**
     * Compact text summary of the cards attached to an assistant turn —
     * fed back into the LLM history so it can answer follow-ups like
     * "that's breakfast, I wanted dinner" instead of saying "I don't
     * have any info in our current conversation."
     */
    protected function cardsToHistorySummary(array $cards): string
    {
        $parts = ['[Cards I just showed the user:]'];
        foreach ($cards as $c) {
            if (!is_array($c)) continue;
            $t = $c['type'] ?? '';
            if ($t === 'recipe') {
                $parts[] = sprintf(
                    '- Recipe card: "%s" (slot: %s, day: %s, %s cal). %s',
                    $c['title'] ?? '',
                    $c['meal_type'] ?? '?',
                    $c['day_key'] ?? '?',
                    $c['calories'] ?? '?',
                    $c['description'] ?? ''
                );
            } elseif ($t === 'exercise') {
                $parts[] = sprintf(
                    '- Workout card: "%s" (%s, %d exercises). %s',
                    $c['title'] ?? '',
                    $c['day_label'] ?? '?',
                    (int) ($c['exercise_count'] ?? 0),
                    $c['description'] ?? ''
                );
            } elseif ($t === 'dietary_update') {
                $added = $c['added'] ?? [];
                $removed = $c['removed'] ?? [];
                $now = $c['exclusions_now'] ?? [];
                $parts[] = sprintf(
                    '- Dietary update saved: added [%s], removed [%s]. Current exclusions: [%s].',
                    implode(', ', $added),
                    implode(', ', $removed),
                    implode(', ', $now)
                );
            }
        }
        return implode("\n", $parts);
    }

    /**
     * Small confirmation card surfaced after we update a user's food
     * exclusions from a chat message. Shows what changed + the current
     * full list + a link to the recipe page that auto-refreshes.
     */
    protected function buildExclusionsCard(array $dietaryChange): array
    {
        return [
            'type' => 'dietary_update',
            'added' => $dietaryChange['added'] ?? [],
            'removed' => $dietaryChange['removed'] ?? [],
            'exclusions_now' => $dietaryChange['exclusions_now'] ?? [],
            'regenerating' => (bool) ($dietaryChange['regenerate'] ?? false),
        ];
    }

    /**
     * Build the chat reply's `cards` array based on what the user asked
     * for. Sourced from the user's most recent saved meal plan / exercise
     * plan — never blocks the chat on an LLM call. Returns [] when
     * nothing matches so the response stays compact.
     */
    protected function buildRecommendationCards(?int $userId, string $message, ?array $llmIntent = null): array
    {
        if ($userId === null) return [];
        $regex = $this->recommendIntent->detect($message);
        $wantsRecipe = $regex['recipe'] || ($llmIntent['wants_recipe'] ?? false);
        $mealType = $regex['meal_type'] ?? ($llmIntent['meal_type'] ?? null);
        $wantsExercise = $regex['exercise'] || ($llmIntent['wants_workout'] ?? false);
        $showWeek = ($regex['show_week'] ?? false) || ($llmIntent['show_week'] ?? false);

        // Resolve target day from BOTH sources. The LLM is best at "next
        // Friday"; regex catches "tomorrow / today / yesterday" reliably.
        $targetDay = $llmIntent['target_day'] ?? null;
        if (!$targetDay) {
            $targetDay = $this->detectTargetDay($message);
        }
        // "tomorrow"-style asks usually imply they want to see something —
        // if no recipe/workout intent was flagged but they're asking about
        // a future day, treat it as a recipe ask by default.
        if ($targetDay && !$wantsRecipe && !$wantsExercise) {
            $wantsRecipe = true;
        }

        // "Show all days" mode — return cards for every day in the plan.
        if ($showWeek) {
            $cards = [];
            if ($wantsRecipe || !$wantsExercise) {
                $cards = array_merge($cards, $this->pickAllRecipeCards($userId, $mealType));
            }
            if ($wantsExercise || (!$wantsRecipe && !$wantsExercise)) {
                $cards = array_merge($cards, $this->pickAllExerciseCards($userId));
            }
            return $cards;
        }

        $cards = [];
        if ($wantsRecipe) {
            $card = $this->pickRecipeCard($userId, $mealType, $targetDay);
            if ($card) $cards[] = $card;
        }
        if ($wantsExercise) {
            $card = $this->pickExerciseCard($userId, $targetDay);
            if ($card) $cards[] = $card;
        }
        return $cards;
    }

    /**
     * Regex fallback for "tomorrow / today / yesterday / monday / friday"
     * style phrasing when the LLM didn't emit `target_day`. Returns a
     * lowercase weekday name or null.
     */
    protected function detectTargetDay(string $message): ?string
    {
        $msg = mb_strtolower($message);
        $weekdays = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

        // "next <weekday>" — always means the coming occurrence AFTER today.
        foreach ($weekdays as $d) {
            if (preg_match('/\bnext\s+' . $d . '\b/u', $msg)) {
                $shifted = (clone (new \DateTime()))->modify('next ' . $d);
                return strtolower($shifted->format('l'));
            }
        }

        // "this weekend" / "the weekend" → Saturday (first weekend day).
        if (preg_match('/\b(?:this\s+|the\s+)?weekend\b/u', $msg)) {
            $shifted = (clone (new \DateTime()))->modify('saturday');
            // If today IS Saturday or Sunday, keep it as Saturday / Sunday respectively.
            $dow = (int) date('w');
            if ($dow === 6) return 'saturday';
            if ($dow === 0) return 'sunday';
            return strtolower($shifted->format('l'));
        }

        // Bare explicit weekday (without "next").
        foreach ($weekdays as $d) {
            if (preg_match('/\b' . $d . '\b/u', $msg)) return $d;
        }

        // Relative day words.
        $shiftMap = ['tomorrow' => 1, 'tmrw' => 1, 'yesterday' => -1, 'today' => 0, 'tonight' => 0];
        foreach ($shiftMap as $kw => $shift) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/u', $msg)) {
                $shifted = (clone (new \DateTime()))->modify(($shift >= 0 ? '+' : '') . $shift . ' day');
                return strtolower($shifted->format('l'));
            }
        }
        if (preg_match('/\bin\s+(\d+)\s+days?\b/u', $msg, $m)) {
            $shifted = (clone (new \DateTime()))->modify('+' . ((int)$m[1]) . ' day');
            return strtolower($shifted->format('l'));
        }
        return null;
    }

    /**
     * Pull a meal out of the user's most recent meal plan. When the user
     * specified a slot ("suggest a dinner"), we look hardest for that slot
     * on today's weekday, then on any other day, before falling back to
     * the current time-of-day. The card includes `requested_meal_type`
     * and `note` so the UI can show why the fallback kicked in.
     */
    protected function pickRecipeCard(int $userId, ?string $mealType, ?string $targetDay = null): ?array
    {
        $plan = MealPlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if (!$plan) return null;
        $payload = $plan->payload ?? [];
        $plans = $payload['plan'] ?? [];
        if (!$plans) return null;

        // Honour an explicit weekday request ("tomorrow" → tuesday) when
        // provided; otherwise default to today's weekday.
        $referenceDay = $targetDay ?: strtolower(date('l'));
        $todayKey = isset($plans[$referenceDay]) ? $referenceDay : null;
        $allDayKeys = array_keys($plans);

        // Resolution order:
        //   1. Asked slot on TODAY's day
        //   2. Asked slot on ANY day in the plan
        //   3. Time-of-day slot on today
        //   4. Time-of-day slot on any day
        //   5. First day, any available slot
        $dayKey = null; $slot = null; $note = null;
        $tryPick = function (?string $dKey, ?string $sKey) use ($plans) {
            if (!$dKey || !$sKey) return null;
            $d = $plans[$dKey] ?? null;
            if (!is_array($d) || !isset($d[$sKey]) || !is_array($d[$sKey])) return null;
            return [$dKey, $sKey];
        };

        if ($mealType) {
            // Try today first.
            $hit = $tryPick($todayKey, $mealType);
            // Else any other day.
            if (!$hit) {
                foreach ($allDayKeys as $dk) {
                    $hit = $tryPick($dk, $mealType);
                    if ($hit) {
                        $note = "Today's plan doesn't have a {$mealType} — showing {$dk}'s instead.";
                        break;
                    }
                }
            }
            if ($hit) {
                [$dayKey, $slot] = $hit;
            } else {
                $note = "Your saved plan doesn't have a {$mealType} for any day. Showing a different meal.";
            }
        }

        // Fallback: time-of-day slot.
        if (!$dayKey || !$slot) {
            $bySlot = $this->mealSlotByHour((int) date('G'));
            $hit = $tryPick($todayKey, $bySlot) ?: null;
            if (!$hit) {
                foreach ($allDayKeys as $dk) {
                    $hit = $tryPick($dk, $bySlot);
                    if ($hit) break;
                }
            }
            // Last resort: ANY slot on the first available day.
            if (!$hit) {
                foreach ($allDayKeys as $dk) {
                    foreach (['breakfast','lunch','dinner','snack'] as $sk) {
                        $hit = $tryPick($dk, $sk);
                        if ($hit) break 2;
                    }
                }
            }
            if (!$hit) return null;
            [$dayKey, $slot] = $hit;
        }

        $meal = $plans[$dayKey][$slot];

        return [
            'type' => 'recipe',
            'meal_type' => $slot,
            'requested_meal_type' => $mealType,
            'day_key' => $dayKey,
            'meal_plan_id' => $plan->id,
            'title' => (string) ($meal['title'] ?? ucfirst($slot)),
            'subtitle' => (string) ($meal['subtitle'] ?? ucfirst($slot)),
            'description' => (string) ($meal['description'] ?? ''),
            'calories' => (string) ($meal['calories'] ?? ''),
            'protein' => (string) ($meal['protein'] ?? ''),
            'carbs' => (string) ($meal['carbs'] ?? ''),
            'tags' => array_map(
                fn ($t) => is_array($t) ? ($t['text'] ?? '') : (string) $t,
                $meal['tags'] ?? []
            ),
            'ingredient_count' => is_array($meal['ingredients'] ?? null) ? count($meal['ingredients']) : 0,
            'note' => $note,
            // Full snapshot so the like-button on the card can POST it.
            'snapshot' => $meal,
        ];
    }

    protected function mealSlotByHour(int $hour): string
    {
        if ($hour < 10) return 'breakfast';
        if ($hour < 15) return 'lunch';
        if ($hour < 21) return 'dinner';
        return 'snack';
    }

    /**
     * Pick an exercise card from the user's most recent saved exercise plan.
     * Surfaces today's workout if the plan has a weekly structure, else the
     * first listed workout. Includes the first exercise's image_id so the
     * card can render a GIF via /api/exercises/{id}/image.
     */
    protected function pickExerciseCard(int $userId, ?string $targetDay = null): ?array
    {
        $plan = ExercisePlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if (!$plan) return null;
        $payload = $plan->payload ?? [];
        $days = $payload['weekly_workout_plan'] ?? [];
        if (!is_array($days) || empty($days)) return null;

        $referenceDay = $targetDay ?: strtolower(date('l'));
        $picked = null;
        foreach ($days as $d) {
            $label = mb_strtolower((string) ($d['day_label'] ?? ''));
            if ($label === $referenceDay) { $picked = $d; break; }
        }
        $picked ??= $days[0];

        $exercises = $picked['exercises'] ?? [];
        $exerciseIds = [];
        foreach (array_slice($exercises, 0, 6) as $ex) {
            if (!empty($ex['exercise_id'])) $exerciseIds[] = $ex['exercise_id'];
        }

        return [
            'type' => 'exercise',
            'exercise_plan_id' => $plan->id,
            'day_label' => (string) ($picked['day_label'] ?? ''),
            'title' => (string) ($picked['title'] ?? $picked['heading'] ?? 'Workout'),
            'description' => (string) ($picked['description'] ?? ''),
            'duration' => (string) ($picked['duration'] ?? ''),
            'exercise_count' => count($exercises),
            'exercises_preview' => array_map(fn ($e) => [
                'name' => (string) ($e['name'] ?? ''),
                'detail' => (string) ($e['detail'] ?? ''),
                'exercise_id' => $e['exercise_id'] ?? null,
            ], array_slice($exercises, 0, 4)),
            'image_ids' => array_values(array_filter($exerciseIds)),
        ];
    }

    protected function pickAllRecipeCards(int $userId, ?string $mealType): array
    {
        $plan = MealPlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if (!$plan) return [];
        $plans = $plan->payload['plan'] ?? [];
        if (!$plans) return [];

        // Cap at 7 cards (one per day) to keep the chat response manageable.
        // When a specific slot is requested show that slot; otherwise pick the
        // most calorie-appropriate slot for the current time of day.
        $fallbackSlot = $this->mealSlotByHour((int) date('G'));
        $weekOrder = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $cards = [];
        foreach ($weekOrder as $dayKey) {
            if (!isset($plans[$dayKey]) || !is_array($plans[$dayKey])) continue;
            $slot = $mealType ?? $fallbackSlot;
            // Fall back through slots if the preferred one is missing for this day.
            $meal = $plans[$dayKey][$slot] ?? null;
            if (!is_array($meal)) {
                foreach (['breakfast','lunch','dinner','snack'] as $s) {
                    $meal = $plans[$dayKey][$s] ?? null;
                    if (is_array($meal)) { $slot = $s; break; }
                }
            }
            if (!is_array($meal)) continue;
            $cards[] = [
                'type' => 'recipe',
                'meal_type' => $slot,
                'requested_meal_type' => $mealType,
                'day_key' => $dayKey,
                'meal_plan_id' => $plan->id,
                'title' => (string) ($meal['title'] ?? ucfirst($slot)),
                'subtitle' => ucfirst($dayKey) . ' · ' . ucfirst($slot),
                'description' => (string) ($meal['description'] ?? ''),
                'calories' => (string) ($meal['calories'] ?? ''),
                'protein' => (string) ($meal['protein'] ?? ''),
                'carbs' => (string) ($meal['carbs'] ?? ''),
                'tags' => array_map(
                    fn ($t) => is_array($t) ? ($t['text'] ?? '') : (string) $t,
                    $meal['tags'] ?? []
                ),
                'ingredient_count' => is_array($meal['ingredients'] ?? null) ? count($meal['ingredients']) : 0,
                'note' => null,
                'snapshot' => $meal,
            ];
        }
        return $cards;
    }

    protected function pickAllExerciseCards(int $userId): array
    {
        $plan = ExercisePlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if (!$plan) return [];
        $days = $plan->payload['weekly_workout_plan'] ?? [];
        if (!is_array($days) || empty($days)) return [];

        $cards = [];
        foreach ($days as $d) {
            $exercises = $d['exercises'] ?? [];
            $exerciseIds = [];
            foreach (array_slice($exercises, 0, 6) as $ex) {
                if (!empty($ex['exercise_id'])) $exerciseIds[] = $ex['exercise_id'];
            }
            $cards[] = [
                'type' => 'exercise',
                'exercise_plan_id' => $plan->id,
                'day_label' => (string) ($d['day_label'] ?? ''),
                'title' => (string) ($d['title'] ?? $d['heading'] ?? 'Workout'),
                'description' => (string) ($d['description'] ?? ''),
                'duration' => (string) ($d['duration'] ?? ''),
                'exercise_count' => count($exercises),
                'exercises_preview' => array_map(fn ($e) => [
                    'name' => (string) ($e['name'] ?? ''),
                    'detail' => (string) ($e['detail'] ?? ''),
                    'exercise_id' => $e['exercise_id'] ?? null,
                ], array_slice($exercises, 0, 4)),
                'image_ids' => array_values(array_filter($exerciseIds)),
            ];
        }
        return $cards;
    }

    /**
     * Merge the user's saved food_exclusions into a per-request allergies
     * list (case-insensitive, no duplicates). Used by recipe daily/weekly
     * so the generator always honours what the user told the chat.
     *
     * Also expands category nouns into their common species/forms because
     * the gateway's filter is substring-based — "fish" alone won't catch
     * "trout" / "salmon" / "tuna".
     */
    protected function mergeUserExclusions(Request $request, array $allergies): array
    {
        $userId = $this->sessionUserId($request);
        if ($userId === null) return array_values(array_unique(static::expandExclusionSynonyms($allergies)));
        $user = User::find($userId);
        if (!$user || empty($user->food_exclusions) || !is_array($user->food_exclusions)) {
            return array_values(array_unique(static::expandExclusionSynonyms($allergies)));
        }
        $merged = $allergies;
        $lowered = array_map('mb_strtolower', $merged);
        foreach ($user->food_exclusions as $excl) {
            $low = mb_strtolower((string) $excl);
            if (!in_array($low, $lowered, true)) {
                $merged[] = $excl;
                $lowered[] = $low;
            }
        }
        return array_values(array_unique(static::expandExclusionSynonyms($merged)));
    }

    /**
     * Expand category foods into the substrings the recipe filter actually
     * needs to see. "fish" alone misses "salmon" / "trout"; "dairy" misses
     * "cheese" / "milk"; "shellfish" misses "shrimp" / "lobster". This
     * stays in Laravel so the gateway can remain generic.
     */
    public static function expandExclusionSynonyms(array $items): array
    {
        $map = [
            'fish' => ['fish', 'salmon', 'tuna', 'trout', 'cod', 'halibut',
                'mackerel', 'sardine', 'anchovy', 'tilapia', 'haddock',
                'snapper', 'bass', 'catfish', 'pollock', 'flounder', 'sole',
                'swordfish', 'mahi'],
            'seafood' => ['fish', 'salmon', 'tuna', 'trout', 'cod', 'shrimp',
                'prawn', 'lobster', 'crab', 'clam', 'mussel', 'oyster',
                'squid', 'octopus', 'scallop', 'calamari', 'anchovy', 'sardine'],
            'shellfish' => ['shellfish', 'shrimp', 'prawn', 'lobster', 'crab',
                'clam', 'mussel', 'oyster', 'scallop', 'crayfish'],
            'dairy' => ['dairy', 'milk', 'cheese', 'butter', 'cream', 'yogurt',
                'yoghurt', 'whey', 'casein', 'ghee', 'paneer'],
            'gluten' => ['gluten', 'wheat', 'barley', 'rye', 'bread', 'pasta',
                'flour', 'farro'],
            'nuts' => ['nuts', 'almond', 'walnut', 'pecan', 'cashew', 'hazelnut',
                'pistachio', 'brazil nut', 'macadamia'],
            'peanut' => ['peanut', 'peanuts', 'peanut butter', 'groundnut'],
            'peanuts' => ['peanut', 'peanuts', 'peanut butter', 'groundnut'],
            'soy' => ['soy', 'soya', 'tofu', 'tempeh', 'edamame', 'miso', 'soybean'],
            'pork' => ['pork', 'bacon', 'ham', 'prosciutto', 'sausage', 'pancetta',
                'chorizo', 'salami'],
            'beef' => ['beef', 'steak', 'brisket', 'roast beef', 'meatloaf'],
            'chicken' => ['chicken', 'poultry', 'hen'],
            'lamb' => ['lamb', 'mutton'],
            'turkey' => ['turkey'],
            'duck' => ['duck'],
            'egg' => ['egg', 'eggs'],
            'eggs' => ['egg', 'eggs'],
            'meat' => ['meat', 'beef', 'pork', 'chicken', 'lamb', 'bacon',
                'sausage', 'ham', 'turkey', 'duck', 'mutton'],
            'red meat' => ['beef', 'pork', 'lamb', 'mutton', 'venison'],
            'alcohol' => ['alcohol', 'wine', 'beer', 'spirits', 'liquor',
                'cocktail', 'whiskey', 'whisky', 'vodka', 'rum', 'gin'],
            'sugar' => ['sugar', 'cane sugar', 'brown sugar', 'syrup',
                'maple syrup', 'honey', 'agave'],
            'spicy' => ['spicy', 'chili', 'chilli', 'jalapeño', 'cayenne',
                'sriracha', 'hot sauce'],
        ];
        $out = [];
        $seen = [];
        foreach ($items as $raw) {
            $key = mb_strtolower(trim((string) $raw));
            if ($key === '') continue;
            $expansions = $map[$key] ?? [$key];
            foreach ($expansions as $e) {
                if (!isset($seen[$e])) {
                    $out[] = $e;
                    $seen[$e] = true;
                }
            }
        }
        return $out;
    }

    /**
     * Compose a plain-text block summarising what the assistant should know
     * about this user: profile + most recent health checkup. Returns null
     * when the user isn't logged in. Kept short on purpose — every chat
     * turn pays for this in tokens.
     */
    protected function buildUserContext(?int $userId): ?string
    {
        if ($userId === null) {
            return null;
        }
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $lines = [];
        // Today's calendar context so the LLM can resolve "tomorrow",
        // "next monday", etc. without guessing.
        $lines[] = "Today is " . date('l, Y-m-d') . " (tomorrow = " . date('l', strtotime('+1 day')) . ").";
        $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if ($name !== '') {
            $lines[] = "Name: {$name}";
        }
        $profile = [];
        if (!empty($user->age)) $profile[] = "age {$user->age}";
        if (!empty($user->gender)) $profile[] = "{$user->gender}";
        if (!empty($user->height)) $profile[] = "height {$user->height} cm";
        if (!empty($user->weight)) $profile[] = "weight {$user->weight} kg";
        if ($profile) {
            $lines[] = 'Profile: ' . implode(', ', $profile);
        }
        $calorieGoal = (int) ($user->calorie_goal ?? 2000);
        $goalLabel = match(true) {
            $calorieGoal <= 1350 => 'Strict Diet',
            $calorieGoal <= 1650 => 'Weight Loss',
            $calorieGoal <= 1900 => 'Light Cut',
            $calorieGoal <= 2200 => 'Maintain',
            $calorieGoal <= 2750 => 'Active',
            default              => 'Build / Muscle Gain',
        };
        $lines[] = "Daily calorie goal: {$calorieGoal} kcal ({$goalLabel}) — tailor meal recommendations to hit this target.";

        if (!empty($user->dietary_preferences)) {
            $diet = is_array($user->dietary_preferences)
                ? implode(', ', $user->dietary_preferences)
                : (string) $user->dietary_preferences;
            if ($diet !== '') $lines[] = "Dietary preferences: {$diet}";
        }
        if (!empty($user->food_exclusions) && is_array($user->food_exclusions)) {
            $lines[] = 'Foods to AVOID (the user said they cannot or will not eat these — '
                . 'treat as a personal exclusion, NOT a medical allergy unless they '
                . 'specifically said "allergic"): ' . implode(', ', $user->food_exclusions);
        }

        // Pull the most recent health checkup (if any) and lift only the
        // small, decision-grade fields. Avoid dumping the raw biomarker
        // array — that's huge and rarely useful in a chat reply.
        $report = HealthReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if ($report) {
            $when = optional($report->created_at)->toDateString() ?? 'recent';
            $lines[] = "\nLatest health checkup ({$when}, file: {$report->original_filename}):";
            if (!empty($report->overall_severity)) {
                $lines[] = "  Overall severity: {$report->overall_severity}";
            }
            $counts = [];
            if (!is_null($report->biomarker_count)) $counts[] = "{$report->biomarker_count} biomarkers";
            if (!is_null($report->abnormal_count)) $counts[] = "{$report->abnormal_count} abnormal";
            if (!is_null($report->critical_count)) $counts[] = "{$report->critical_count} critical";
            if ($counts) $lines[] = '  Counts: ' . implode(', ', $counts);

            if (!empty($report->summary)) {
                $lines[] = '  Summary: ' . \Illuminate\Support\Str::limit($report->summary, 600);
            }

            $payload = $report->payload ?? [];
            $abnormal = $payload['abnormal_findings'] ?? [];
            if (is_array($abnormal) && !empty($abnormal)) {
                $lines[] = '  Abnormal findings:';
                foreach (array_slice($abnormal, 0, 10) as $finding) {
                    if (!is_array($finding)) continue;
                    $bio = $finding['biomarker'] ?? ($finding['name'] ?? 'unknown');
                    $val = $finding['value'] ?? null;
                    $unit = $finding['unit'] ?? '';
                    $sev = $finding['severity'] ?? '';
                    $note = $finding['note'] ?? ($finding['interpretation'] ?? '');
                    $valStr = $val !== null ? "{$val} {$unit}" : '';
                    $extra = $sev ? " [{$sev}]" : '';
                    $lines[] = "    - {$bio}: {$valStr}{$extra}"
                        . ($note ? ' — ' . \Illuminate\Support\Str::limit((string) $note, 120) : '');
                }
            }

            $critical = $payload['critical_findings'] ?? [];
            if (is_array($critical) && !empty($critical)) {
                $lines[] = '  Critical findings (urgent):';
                foreach (array_slice($critical, 0, 5) as $finding) {
                    if (!is_array($finding)) continue;
                    $bio = $finding['biomarker'] ?? ($finding['name'] ?? 'unknown');
                    $val = $finding['value'] ?? null;
                    $unit = $finding['unit'] ?? '';
                    $lines[] = "    - {$bio}: " . ($val !== null ? "{$val} {$unit}" : '(see report)');
                }
            }

            $diet = $payload['diet_advice'] ?? null;
            if (is_string($diet) && trim($diet) !== '') {
                $lines[] = '  Diet advice from report: ' . \Illuminate\Support\Str::limit($diet, 300);
            }
            $exercise = $payload['exercise_advice'] ?? null;
            if (is_string($exercise) && trim($exercise) !== '') {
                $lines[] = '  Exercise advice from report: ' . \Illuminate\Support\Str::limit($exercise, 300);
            }
        } else {
            $lines[] = "\nNo health checkup uploaded yet.";
        }

        $mealPlan = MealPlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if ($mealPlan) {
            $plans = $mealPlan->payload['plan'] ?? [];
            $today = strtolower(date('l'));
            $weekOrder = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
            $lines[] = "\nWEEKLY MEAL PLAN (plan #{$mealPlan->id}, span {$mealPlan->span}):";
            foreach ($weekOrder as $dayKey) {
                $day = $plans[$dayKey] ?? null;
                if (!is_array($day)) continue;
                $marker = ($dayKey === $today) ? ' ← TODAY' : '';
                $lines[] = "  {$dayKey}{$marker}:";
                foreach (['breakfast','lunch','dinner','snack'] as $slot) {
                    $meal = $day[$slot] ?? null;
                    if (is_array($meal)) {
                        $title = (string) ($meal['title'] ?? '');
                        $cals = (string) ($meal['calories'] ?? '');
                        $lines[] = sprintf('    %s: %s%s', $slot, $title, $cals !== '' ? " ({$cals} cal)" : '');
                    }
                }
            }
        } else {
            $lines[] = "\nNo meal plan saved yet (suggest the user generate one on /recipe).";
        }

        $exercisePlan = ExercisePlan::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();
        if ($exercisePlan) {
            $days = $exercisePlan->payload['weekly_workout_plan'] ?? [];
            $today = strtolower(date('l'));
            $lines[] = "\nWEEKLY WORKOUT PLAN (plan #{$exercisePlan->id}):";
            foreach ($days as $d) {
                $label = (string) ($d['day_label'] ?? '');
                $title = (string) ($d['title'] ?? $d['heading'] ?? 'Workout');
                $exCount = is_array($d['exercises'] ?? null) ? count($d['exercises']) : 0;
                $marker = (mb_strtolower($label) === $today) ? ' ← TODAY' : '';
                $lines[] = "  {$label}{$marker}: {$title} — {$exCount} exercises.";
            }
        }

        return $lines ? implode("\n", $lines) : null;
    }

    /**
     * Find or create the conversation we should write into. We only persist
     * for logged-in users — anonymous chats are ephemeral.
     */
    protected function resolveConversation(?int $userId, mixed $explicitId): ?ChatConversation
    {
        if ($userId === null) {
            return null;
        }
        if ($explicitId !== null) {
            $conv = ChatConversation::find((int) $explicitId);
            if ($conv && $conv->user_id === $userId) {
                return $conv;
            }
        }
        return ChatConversation::create([
            'user_id' => $userId,
            'last_message_at' => now(),
        ]);
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
