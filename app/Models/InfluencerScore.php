<?php

namespace App\Models;

use Database\Factories\InfluencerScoreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['influencer_id', 'criterion_id', 'raw_value', 'likert_value'])]
class InfluencerScore extends Model
{
    /** @use HasFactory<InfluencerScoreFactory> */
    use HasFactory;

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }

    protected function casts(): array
    {
        return [
            'raw_value' => 'decimal:2',
            'likert_value' => 'integer',
        ];
    }
}
