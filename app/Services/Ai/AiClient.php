<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AiClient
{
    public function __construct(
        protected string $baseUrl,
        protected ?string $apiKey,
        protected int $timeout = 120,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: rtrim((string) config('services.ai_service.url'), '/'),
            apiKey: config('services.ai_service.key'),
            timeout: (int) config('services.ai_service.timeout', 120),
        );
    }

    public function postJson(string $path, array $payload): array
    {
        return $this->parse($this->request(retry: false)->post($this->url($path), $payload));
    }

    public function postMultipart(string $path, array $multipart): array
    {
        $request = $this->request(retry: false);
        foreach ($multipart as $part) {
            $request = $request->attach(
                $part['name'],
                $part['contents'],
                $part['filename'] ?? null,
            );
        }

        return $this->parse($request->post($this->url($path)));
    }

    public function get(string $path, array $query = []): array
    {
        // Long-running GETs (e.g. /sample) hit the LLM too — only retry the
        // short health-check ping, never the AI endpoints.
        $retry = $path === '/healthz' || str_ends_with($path, '/health');
        return $this->parse($this->request(retry: $retry)->get($this->url($path), $query));
    }

    protected function request(bool $retry = false): PendingRequest
    {
        $request = Http::timeout($this->timeout)->acceptJson();

        if ($retry) {
            $request = $request->retry(2, 250, throw: false);
        }

        if ($this->apiKey) {
            $request = $request->withHeaders(['X-API-Key' => $this->apiKey]);
        }

        return $request;
    }

    protected function url(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }

    protected function parse(Response $response): array
    {
        if ($response->failed()) {
            throw new AiServiceException(
                "AI service error ({$response->status()}): " . $response->body(),
                $response->status(),
            );
        }

        return $response->json() ?? [];
    }
}
