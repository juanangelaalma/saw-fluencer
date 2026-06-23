<?php

use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\SubCriterion;
use App\Models\User;
use App\Services\SawCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

function createSawCriteria(): Collection
{
    return collect([
        ['code' => 'C1', 'name' => 'Engagement Rate', 'weight' => 25, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C2', 'name' => 'Follower', 'weight' => 15, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C3', 'name' => 'Jumlah Like', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C4', 'name' => 'Jumlah Komentar', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C5', 'name' => 'Rate Card', 'weight' => 20, 'type' => Criterion::TYPE_COST],
        ['code' => 'C6', 'name' => 'Average Reel View', 'weight' => 20, 'type' => Criterion::TYPE_BENEFIT],
    ])->map(function (array $criterion): Criterion {
        $model = Criterion::query()->create($criterion);

        foreach (range(1, 5) as $level) {
            SubCriterion::query()->create([
                'criterion_id' => $model->id,
                'level' => $level,
                'label' => "Level $level",
            ]);
        }

        return $model;
    });
}

function createSawInfluencer(string $name, string $username, Collection $criteria, array $values): Influencer
{
    $influencer = Influencer::factory()->create(['name' => $name, 'username' => $username]);

    foreach ($criteria->values() as $index => $criterion) {
        $influencer->scores()->create([
            'criterion_id' => $criterion->id,
            'raw_value' => $values[$index],
            'likert_value' => $values[$index],
        ]);
    }

    return $influencer;
}

it('calculates saw normalization scores and rankings', function () {
    $criteria = createSawCriteria();
    createSawInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);
    createSawInfluencer('A2', 'a2', $criteria, [1, 3, 1, 1, 2, 5]);
    createSawInfluencer('A3', 'a3', $criteria, [1, 3, 2, 5, 1, 5]);

    $result = app(SawCalculationService::class)->calculate();
    $rows = collect($result['rows'])->keyBy(fn (array $row) => $row['influencer']->username);

    expect(round($rows->get('a1')['score'], 4))->toBe(0.77)
        ->and(round($rows->get('a2')['score'], 4))->toBe(0.57)
        ->and(round($rows->get('a3')['score'], 4))->toBe(0.80)
        ->and($rows->get('a3')['rank'])->toBe(1)
        ->and($rows->get('a1')['rank'])->toBe(2)
        ->and($rows->get('a2')['rank'])->toBe(3);
});

it('allows admin and manager to view saw calculation', function (string $roleFactory, string $routeName) {
    $user = User::factory()->{$roleFactory}()->create();
    $criteria = createSawCriteria();
    createSawInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertOk()
        ->assertSee('Perhitungan SAW')
        ->assertSee('A1')
        ->assertSee('1')
        ->assertDontSee('1,0000');
})->with([
    ['admin', 'admin.saw.index'],
    ['manajer', 'manager.saw.index'],
]);

it('limits saw rows to ten by default and allows limit option', function () {
    $user = User::factory()->admin()->create();
    $criteria = createSawCriteria();

    foreach (range(1, 11) as $index) {
        createSawInfluencer(sprintf('A%02d', $index), sprintf('a%02d', $index), $criteria, [5, 1, 1, 1, 1, 5]);
    }

    $this->actingAs($user)
        ->get(route('admin.saw.index'))
        ->assertOk()
        ->assertSee('Menampilkan 10 dari 11 data')
        ->assertSee('A10')
        ->assertDontSee('A11');

    $this->actingAs($user)
        ->get(route('admin.saw.index', ['limit' => 25]))
        ->assertOk()
        ->assertSee('Menampilkan 11 dari 11 data')
        ->assertSee('A11');

    $this->actingAs($user)
        ->get(route('admin.saw.index', ['limit' => 'all']))
        ->assertOk()
        ->assertSee('Menampilkan 11 dari 11 data')
        ->assertSee('Semua')
        ->assertSee('A11');
});
