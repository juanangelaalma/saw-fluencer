<?php

namespace App\Models;

use Database\Factories\CriterionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'weight', 'type'])]
class Criterion extends Model
{
    /** @use HasFactory<CriterionFactory> */
    use HasFactory;

    public const TYPE_BENEFIT = 'benefit';

    public const TYPE_COST = 'cost';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_BENEFIT => 'Benefit',
            self::TYPE_COST => 'Cost',
        ];
    }

    public function subCriteria(): HasMany
    {
        return $this->hasMany(SubCriterion::class)->orderBy('level');
    }

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    protected function casts(): array
    {
        return [
            'weight' => 'integer',
        ];
    }
}
