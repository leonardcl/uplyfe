<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageGenerationController extends Controller
{
    private function saveFromDataUrlOrRemote(string $url, string $filename): bool
    {
        if (!$url) return false;
        if (str_starts_with($url, 'data:')) {
            $base64 = substr($url, strpos($url, ',') + 1);
            Storage::disk('public')->put($filename, base64_decode($base64));
            return true;
        }
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $body = Http::timeout(30)->get($url)->body();
            Storage::disk('public')->put($filename, $body);
            return true;
        }
        return false;
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prompt' => 'required|string|max:600',
        ]);

        $prompt = $data['prompt'];
        $hash = md5($prompt);
        $filename = "generated/{$hash}.jpg";

        if (Storage::disk('public')->exists($filename)) {
            return response()->json(['url' => '/storage/' . $filename]);
        }

        $apiKey = config('services.openrouter.key');
        $model  = config('services.openrouter.image_model', 'google/gemini-2.5-flash-image');

        if (!$apiKey) {
            return response()->json(['error' => 'Image generation not configured'], 503);
        }

        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'HTTP-Referer'  => config('app.url'),
            'X-Title'       => 'Uplyfe',
            'Content-Type'  => 'application/json',
        ];

        $response = Http::withHeaders($headers)->timeout(90)
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'    => $model,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if ($response->failed()) {
            return response()->json([
                'error'  => 'Generation failed: ' . $response->status(),
                'detail' => $response->body(),
            ], 500);
        }

        $result = $response->json();

        $message = $result['choices'][0]['message'] ?? [];

        // OpenRouter image models (e.g. gemini-2.5-flash-image) return image
        // data in message.images[], not in message.content
        $imageParts = $message['images'] ?? [];
        foreach ($imageParts as $part) {
            $dataUrl = $part['image_url']['url'] ?? '';
            if ($this->saveFromDataUrlOrRemote($dataUrl, $filename)) {
                return response()->json(['url' => '/storage/' . $filename]);
            }
        }

        // Some models embed image parts inside content array
        $content = $message['content'] ?? null;
        if (is_array($content)) {
            foreach ($content as $part) {
                if (($part['type'] ?? '') === 'image_url') {
                    $dataUrl = $part['image_url']['url'] ?? '';
                    if ($this->saveFromDataUrlOrRemote($dataUrl, $filename)) {
                        return response()->json(['url' => '/storage/' . $filename]);
                    }
                }
            }
        }

        // Plain URL string
        if (is_string($content) && filter_var(trim($content), FILTER_VALIDATE_URL)) {
            $imageContent = Http::timeout(30)->get(trim($content))->body();
            Storage::disk('public')->put($filename, $imageContent);
            return response()->json(['url' => '/storage/' . $filename]);
        }

        return response()->json([
            'error'  => 'Unexpected response format',
            'detail' => json_encode($result),
        ], 500);
    }
}
