<?php

namespace App\Services\Ai;

class ExerciseService
{
    public function __construct(protected AiClient $client) {}

    public function generatePlan(array $request): array
    {
        return $this->client->postJson('/exercise/generate', $request);
    }
}
