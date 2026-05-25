<?php

declare(strict_types=1);

namespace App\Domains\Results\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Orders\Models\OrderItem;
use Database\Factories\MedicalResultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MedicalResult extends Model
{
    /** @use HasFactory<MedicalResultFactory> */
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'lab_assistant_id',
        'parameter_values',
        'pdf_path',
        'download_count',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'parameter_values' => 'encrypted:array',
            'download_count' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function labAssistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lab_assistant_id');
    }

    /**
     * Get the PDF file from secure storage.
     */
    public function getPdfContentAttribute(): ?string
    {
        if (! $this->pdf_path) {
            return null;
        }

        return Storage::disk('secure_medical_results')->get($this->pdf_path);
    }

    /**
     * Generate a temporary download URL for the PDF.
     */
    public function getTemporaryDownloadUrlAttribute(): ?string
    {
        if (! $this->pdf_path) {
            return null;
        }

        return Storage::disk('secure_medical_results')->temporaryUrl(
            $this->pdf_path,
            now()->addMinutes(30),
        );
    }

    /**
     * Increment download counter.
     */
    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    /**
     * Check if the result has been verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if values are out of reference ranges.
     *
     * @return array<string, array{value: mixed, reference: string, out_of_range: bool}>
     */
    public function getOutOfRangeValuesAttribute(): array
    {
        $outOfRange = [];
        $parameters = $this->parameter_values ?? [];
        $referenceRanges = $this->orderItem->analysis->reference_ranges ?? [];

        foreach ($parameters as $key => $data) {
            if (! isset($data['value'], $referenceRanges[$key])) {
                continue;
            }

            $range = $referenceRanges[$key];
            $value = $data['value'];
            $min = $range['min'] ?? null;
            $max = $range['max'] ?? null;

            $outOfRange[$key] = [
                'value' => $value,
                'unit' => $data['unit'] ?? '',
                'reference' => $data['reference'] ?? '',
                'out_of_range' => ($min !== null && $value < $min) || ($max !== null && $value > $max),
            ];
        }

        return $outOfRange;
    }
}
