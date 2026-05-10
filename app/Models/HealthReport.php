<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_filename',
        'original_size_bytes',
        'overall_severity',
        'biomarker_count',
        'abnormal_count',
        'critical_count',
        'summary',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'original_size_bytes' => 'integer',
        'biomarker_count' => 'integer',
        'abnormal_count' => 'integer',
        'critical_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenience: build a HealthReport from an upload + the FinalReport JSON
     * the gateway returned. Caller still has to ::save() it.
     */
    public static function fromGatewayResponse(
        ?int $userId,
        string $filename,
        int $sizeBytes,
        array $payload,
    ): self {
        return new self([
            'user_id' => $userId,
            'original_filename' => $filename,
            'original_size_bytes' => $sizeBytes,
            'overall_severity' => $payload['overall_severity'] ?? null,
            'biomarker_count' => count($payload['panel']['values'] ?? []),
            'abnormal_count' => count($payload['abnormal_findings'] ?? []),
            'critical_count' => count($payload['critical_findings'] ?? []),
            'summary' => $payload['summary'] ?? null,
            'payload' => $payload,
        ]);
    }
}
