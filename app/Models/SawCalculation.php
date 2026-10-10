<?php

namespace App\Models;

use Database\Factories\SawCalculationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'total_influencers', 'payload'])]
class SawCalculation extends Model
{
    /** @use HasFactory<SawCalculationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'total_influencers' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function criteria(): array
    {
        return $this->payload['criteria'] ?? [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rows(): array
    {
        return $this->payload['rows'] ?? [];
    }

    public function topRow(): ?array
    {
        $rows = $this->rows();

        return $rows[0] ?? null;
    }
}
