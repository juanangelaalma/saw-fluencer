<?php

namespace App\Services;

use App\Models\Criterion;
use App\Models\Influencer;
use Illuminate\Support\Collection;

class SawCalculationService
{
    /**
     * @param  array<int, int>|null  $onlyInfluencerIds
     * @return array<string, mixed>
     */
    public function calculate(?array $onlyInfluencerIds = null): array
    {
        $criteria = Criterion::query()->orderBy('code')->get();
        $influencers = Influencer::query()
            ->with(['scores' => fn ($query) => $query->with('criterion')])
            ->when($onlyInfluencerIds !== null, fn ($query) => $query->whereIn('id', $onlyInfluencerIds))
            ->orderBy('name')
            ->orderBy('username')
            ->get();

        $values = $this->values($criteria, $influencers);
        $divisors = $this->divisors($criteria, $values);
        $rows = $this->rows($criteria, $influencers, $values, $divisors);

        usort($rows, fn (array $left, array $right) => [$right['score'], $left['influencer']->name, $left['influencer']->username] <=> [$left['score'], $right['influencer']->name, $right['influencer']->username]);

        foreach ($rows as $index => $row) {
            $rows[$index]['rank'] = $index + 1;
        }

        return [
            'criteria' => $criteria,
            'divisors' => $divisors,
            'rows' => $rows,
        ];
    }

    /**
     * Ubah hasil perhitungan menjadi struktur sederhana untuk disimpan di riwayat.
     *
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>
     */
    public function buildPayload(array $result): array
    {
        return [
            'criteria' => $result['criteria']->map(fn (Criterion $criterion): array => [
                'code' => $criterion->code,
                'name' => $criterion->name,
                'weight' => $criterion->weight,
                'type' => $criterion->type,
                'type_label' => $criterion->typeLabel(),
                'divisor' => $result['divisors'][$criterion->id] ?? 0,
            ])->values()->all(),
            'rows' => array_map(fn (array $row): array => [
                'rank' => $row['rank'],
                'name' => $row['influencer']->name,
                'username' => $row['influencer']->username,
                'score' => $row['score'],
                'criteria_scores' => array_values(array_map(fn (array $score): array => [
                    'raw' => $score['raw'],
                    'likert' => $score['likert'],
                    'normalized' => $score['normalized'],
                    'weighted' => $score['weighted'],
                ], $row['criteria_scores'])),
            ], $result['rows']),
        ];
    }

    private function values(Collection $criteria, Collection $influencers): array
    {
        $values = [];

        foreach ($criteria as $criterion) {
            $values[$criterion->id] = [];
        }

        foreach ($influencers as $influencer) {
            $scores = $influencer->scores->keyBy('criterion_id');

            foreach ($criteria as $criterion) {
                $values[$criterion->id][] = $scores->get($criterion->id)?->likert_value ?? 0;
            }
        }

        return $values;
    }

    private function divisors(Collection $criteria, array $values): array
    {
        $divisors = [];

        foreach ($criteria as $criterion) {
            $criterionValues = array_filter($values[$criterion->id] ?? [], fn (int $value) => $value > 0);
            $divisors[$criterion->id] = $criterion->type === Criterion::TYPE_COST
                ? (empty($criterionValues) ? 0 : min($criterionValues))
                : (empty($criterionValues) ? 0 : max($criterionValues));
        }

        return $divisors;
    }

    private function rows(Collection $criteria, Collection $influencers, array $values, array $divisors): array
    {
        $rows = [];

        foreach ($influencers as $influencer) {
            $scores = $influencer->scores->keyBy('criterion_id');
            $criteriaScores = [];
            $total = 0;

            foreach ($criteria as $criterion) {
                $likert = $scores->get($criterion->id)?->likert_value ?? 0;
                $normalized = $this->normalize($criterion, $likert, $divisors[$criterion->id] ?? 0);
                $weighted = $normalized * ($criterion->weight / 100);
                $total += $weighted;

                $criteriaScores[$criterion->id] = [
                    'raw' => $scores->get($criterion->id)?->raw_value,
                    'likert' => $likert,
                    'normalized' => $normalized,
                    'weighted' => $weighted,
                ];
            }

            $rows[] = [
                'rank' => null,
                'influencer' => $influencer,
                'criteria_scores' => $criteriaScores,
                'score' => $total,
            ];
        }

        return $rows;
    }

    private function normalize(Criterion $criterion, int $value, int $divisor): float
    {
        if ($value <= 0 || $divisor <= 0) {
            return 0;
        }

        if ($criterion->type === Criterion::TYPE_COST) {
            return $divisor / $value;
        }

        return $value / $divisor;
    }
}
