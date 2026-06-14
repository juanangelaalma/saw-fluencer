<?php

namespace App\Models;

use Database\Factories\SubCriterionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['criterion_id', 'level', 'label', 'min_value', 'max_value'])]
class SubCriterion extends Model
{
    /** @use HasFactory<SubCriterionFactory> */
    use HasFactory;

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'min_value' => 'decimal:2',
            'max_value' => 'decimal:2',
        ];
    }
}
