<?php

namespace App\Models;

use Database\Factories\InfluencerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'username'])]
class Influencer extends Model
{
    /** @use HasFactory<InfluencerFactory> */
    use HasFactory;

    public function scores(): HasMany
    {
        return $this->hasMany(InfluencerScore::class);
    }
}
