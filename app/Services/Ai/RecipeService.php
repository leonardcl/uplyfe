<?php

namespace App\Services\Ai;

class RecipeService
{
    public function __construct(protected AiClient $client) {}

    public function generateDailyMenu(array $request): array
    {
        return $this->client->postJson('/recipe/daily-menu', $request);
    }
}
