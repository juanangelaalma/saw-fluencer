<?php

namespace Database\Seeders;

use App\Models\Criterion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $criteria = [
            ['code' => 'C1', 'name' => 'Engagement Rate', 'weight' => 25, 'type' => Criterion::TYPE_BENEFIT],
            ['code' => 'C2', 'name' => 'Follower', 'weight' => 15, 'type' => Criterion::TYPE_BENEFIT],
            ['code' => 'C3', 'name' => 'Average Like', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
            ['code' => 'C4', 'name' => 'Average Comment', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
            ['code' => 'C5', 'name' => 'Rate Card', 'weight' => 20, 'type' => Criterion::TYPE_COST],
            ['code' => 'C6', 'name' => 'Average Reel View', 'weight' => 20, 'type' => Criterion::TYPE_BENEFIT],
        ];

        foreach ($criteria as $criterion) {
            $model = Criterion::query()->updateOrCreate([
                'code' => $criterion['code'],
            ], $criterion);

            foreach ([1 => 'Kurang', 2 => 'Cukup', 3 => 'Baik', 4 => 'Sangat Baik', 5 => 'Terbaik'] as $level => $label) {
                $model->subCriteria()->updateOrCreate([
                    'level' => $level,
                ], [
                    'label' => $label,
                    'min_value' => null,
                    'max_value' => null,
                ]);
            }
        }
    }
}
