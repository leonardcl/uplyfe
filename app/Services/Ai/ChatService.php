<?php

namespace App\Services\Ai;

class ChatService
{
    public function __construct(protected AiClient $client) {}

    public function send(string $message, array $history = [], ?string $userContext = null): array
    {
        $payload = [
            'message' => $message,
            'history' => $history,
        ];
        if ($userContext !== null && trim($userContext) !== '') {
            $payload['user_context'] = $userContext;
        }
        return $this->client->postJson('/chat', $payload);
    }
}
