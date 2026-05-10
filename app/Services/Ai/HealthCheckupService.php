<?php

namespace App\Services\Ai;

class HealthCheckupService
{
    public function __construct(protected AiClient $client) {}

    public function analyzeManual(array $panel, bool $useLlm = true, bool $useRag = true): array
    {
        return $this->client->postJson('/health-checkup/manual', [
            'panel' => $panel,
            'use_llm' => $useLlm,
            'use_rag' => $useRag,
        ]);
    }

    public function sample(bool $useLlm = true, bool $useRag = true): array
    {
        return $this->client->get('/health-checkup/sample', [
            'use_llm' => $useLlm ? 'true' : 'false',
            'use_rag' => $useRag ? 'true' : 'false',
        ]);
    }

    public function schema(): array
    {
        return $this->client->get('/health-checkup/schema');
    }

    public function probe(): array
    {
        return $this->client->get('/health-checkup/probe');
    }

    public function analyzeUpload(string $absolutePath, string $filename, bool $useLlm = true, bool $useRag = true): array
    {
        $stream = fopen($absolutePath, 'rb');
        if ($stream === false) {
            throw new AiServiceException("Could not open uploaded file: {$absolutePath}");
        }

        try {
            return $this->client->postMultipart('/health-checkup/upload', [
                ['name' => 'file', 'contents' => $stream, 'filename' => $filename],
                ['name' => 'use_llm', 'contents' => $useLlm ? 'true' : 'false'],
                ['name' => 'use_rag', 'contents' => $useRag ? 'true' : 'false'],
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }
}
