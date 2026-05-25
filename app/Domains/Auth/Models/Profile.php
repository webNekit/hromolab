<?php

declare(strict_types=1);

namespace App\Domains\Auth\Models;

use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'gender',
    ];

    protected function casts(): array
    {
        return [
            'first_name' => 'encrypted',
            'last_name' => 'encrypted',
            'middle_name' => 'encrypted',
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Full name accessor.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim("{$this->last_name} {$this->first_name} {$this->middle_name}"),
        );
    }

    /**
     * Calculate age from birth_date.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->diffInYears(now());
    }

    /**
     * Determine reference range multiplier based on gender and age.
     */
    public function getReferenceContextAttribute(): array
    {
        return [
            'gender' => $this->gender,
            'age' => $this->age,
        ];
    }
}
