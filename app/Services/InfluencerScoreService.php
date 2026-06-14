<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\InfluencerScore;

class InfluencerScoreService
{
    public function syncScores(Influencer $influencer, array $criteriaValues): void
    {
        $criteria = Criterion::query()->with('subCriteria')->get()->keyBy('id');
        $syncedIds = [];

        foreach ($criteriaValues as $criterionId => $rawValue) {
            $criterion = $criteria->get((int) $criterionId);

            if ($criterion === null) {
                continue;
            }

            $syncedIds[] = $criterion->id;

            InfluencerScore::query()->updateOrCreate([
                'influencer_id' => $influencer->id,
                'criterion_id' => $criterion->id,
            ], [
                'raw_value' => (float) $rawValue,
                'likert_value' => $this->likertValue($criterion, (float) $rawValue),
            ]);
        }

        $influencer->scores()
            ->whereNotIn('criterion_id', $syncedIds)
            ->delete();
    }

    public function likertValue(Criterion $criterion, float $rawValue): int
    {
        $matched = $criterion->subCriteria->first(function ($subCriterion) use ($rawValue) {
            $min = $subCriterion->min_value;
            $max = $subCriterion->max_value;

            if ($min !== null && $rawValue < (float) $min) {
                return false;
            }

            if ($max !== null && $rawValue > (float) $max) {
                return false;
            }

            return true;
        });

        if ($matched !== null) {
            return $matched->level;
        }

        return 1;
    }
}
