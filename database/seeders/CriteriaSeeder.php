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
            [
                'code' => 'C1',
                'name' => 'Engagement Rate',
                'weight' => 25,
                'type' => Criterion::TYPE_BENEFIT,
                'sub_criteria' => [
                    ['level' => 1, 'label' => 'Kurang', 'min_value' => null, 'max_value' => 3],
                    ['level' => 2, 'label' => 'Cukup', 'min_value' => 3.1, 'max_value' => 6],
                    ['level' => 3, 'label' => 'Cukup Baik', 'min_value' => 6.1, 'max_value' => 9],
                    ['level' => 4, 'label' => 'Baik', 'min_value' => 9.1, 'max_value' => 12],
                    ['level' => 5, 'label' => 'Sangat Baik', 'min_value' => 12.01, 'max_value' => null],
                ],
            ],
            [
                'code' => 'C2',
                'name' => 'Follower',
                'weight' => 15,
                'type' => Criterion::TYPE_BENEFIT,
                'sub_criteria' => [
                    ['level' => 1, 'label' => 'Kurang', 'min_value' => null, 'max_value' => 50000],
                    ['level' => 2, 'label' => 'Cukup', 'min_value' => 50001, 'max_value' => 100000],
                    ['level' => 3, 'label' => 'Cukup Baik', 'min_value' => 100001, 'max_value' => 150000],
                    ['level' => 4, 'label' => 'Baik', 'min_value' => 150001, 'max_value' => 200000],
                    ['level' => 5, 'label' => 'Sangat Baik', 'min_value' => 200001, 'max_value' => null],
                ],
            ],
            [
                'code' => 'C3',
                'name' => 'Jumlah Like',
                'weight' => 10,
                'type' => Criterion::TYPE_BENEFIT,
                'sub_criteria' => [
                    ['level' => 1, 'label' => 'Kurang', 'min_value' => null, 'max_value' => 2000],
                    ['level' => 2, 'label' => 'Cukup', 'min_value' => 2001, 'max_value' => 4000],
                    ['level' => 3, 'label' => 'Cukup Baik', 'min_value' => 4001, 'max_value' => 6000],
                    ['level' => 4, 'label' => 'Baik', 'min_value' => 6001, 'max_value' => 8000],
                    ['level' => 5, 'label' => 'Sangat Baik', 'min_value' => 8001, 'max_value' => null],
                ],
            ],
            [
                'code' => 'C4',
                'name' => 'Jumlah Komentar',
                'weight' => 10,
                'type' => Criterion::TYPE_BENEFIT,
                'sub_criteria' => [
                    ['level' => 1, 'label' => 'Kurang', 'min_value' => null, 'max_value' => 100],
                    ['level' => 2, 'label' => 'Cukup', 'min_value' => 101, 'max_value' => 200],
                    ['level' => 3, 'label' => 'Cukup Baik', 'min_value' => 201, 'max_value' => 300],
                    ['level' => 4, 'label' => 'Baik', 'min_value' => 301, 'max_value' => 400],
                    ['level' => 5, 'label' => 'Sangat Baik', 'min_value' => 401, 'max_value' => null],
                ],
            ],
            [
                'code' => 'C5',
                'name' => 'Rate Card',
                'weight' => 20,
                'type' => Criterion::TYPE_COST,
                'sub_criteria' => [
                    ['level' => 1, 'label' => 'Kurang', 'min_value' => null, 'max_value' => 1000000],
                    ['level' => 2, 'label' => 'Cukup', 'min_value' => 1000000, 'max_value' => 2000000],
                    ['level' => 3, 'label' => 'Cukup Baik', 'min_value' => 2000001, 'max_value' => 3000000],
                    ['level' => 4, 'label' => 'Baik', 'min_value' => 3000001, 'max_value' => 4000000],
                    ['level' => 5, 'label' => 'Sangat Baik', 'min_value' => 4000001, 'max_value' => null],
                ],
            ],
            [
                'code' => 'C6',
                'name' => 'Average Reel View',
                'weight' => 20,
                'type' => Criterion::TYPE_BENEFIT,
                'sub_criteria' => [
                    ['level' => 1, 'label' => 'Kurang', 'min_value' => null, 'max_value' => 10000],
                    ['level' => 2, 'label' => 'Cukup', 'min_value' => 10001, 'max_value' => 25000],
                    ['level' => 3, 'label' => 'Cukup Baik', 'min_value' => 25001, 'max_value' => 50000],
                    ['level' => 4, 'label' => 'Baik', 'min_value' => 50001, 'max_value' => 100000],
                    ['level' => 5, 'label' => 'Sangat Baik', 'min_value' => 100001, 'max_value' => null],
                ],
            ],
        ];

        foreach ($criteria as $criterion) {
            $subCriteria = $criterion['sub_criteria'];
            unset($criterion['sub_criteria']);

            $model = Criterion::query()->updateOrCreate([
                'code' => $criterion['code'],
            ], $criterion);

            foreach ($subCriteria as $subCriterion) {
                $model->subCriteria()->updateOrCreate([
                    'level' => $subCriterion['level'],
                ], $subCriterion);
            }
        }
    }
}
