<?php

use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\SubCriterion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

function selectionCriteria(): Collection
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

function selectionInfluencer(string $name, string $username, Collection $criteria, array $values): Influencer
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


it('calculates only explicitly selected influencers', function () {
    $admin = User::factory()->admin()->create();
    $criteria = selectionCriteria();
    $first = selectionInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);
    $second = selectionInfluencer('A2', 'a2', $criteria, [1, 3, 1, 1, 2, 5]);
    selectionInfluencer('A3', 'a3', $criteria, [1, 3, 2, 5, 1, 5]);

    $this->actingAs($admin)
        ->get(route('admin.saw.index', ['selected' => [$first->id, $second->id]]))
        ->assertOk()
        ->assertSee('A1')
        ->assertSee('A2')
        ->assertSee('Menghitung 2 data terpilih dari 3 data influencer')
        ->assertDontSee('A3');
});

it('requires at least two selected influencers before calculating', function () {
    $admin = User::factory()->admin()->create();
    $criteria = selectionCriteria();
    $first = selectionInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);

    $this->actingAs($admin)
        ->get(route('admin.saw.index', ['selected' => [$first->id]]))
        ->assertRedirect(route('admin.influencers.index', ['selected' => [$first->id]]))
        ->assertSessionHas('error', 'Pilih minimal dua influencer untuk dihitung.');
});

it('does not calculate all data when the calculation page is opened directly', function () {
    $admin = User::factory()->admin()->create();
    $criteria = selectionCriteria();
    selectionInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);
    selectionInfluencer('A2', 'a2', $criteria, [1, 3, 1, 1, 2, 5]);

    $this->actingAs($admin)
        ->get(route('admin.saw.index'))
        ->assertRedirect(route('admin.influencers.index'))
        ->assertSessionHas('error', 'Pilih minimal dua influencer untuk dihitung.');
});

it('preserves influencer selections across management pages', function () {
    $admin = User::factory()->admin()->create();
    $criteria = selectionCriteria();
    $influencers = collect(range(1, 16))->map(fn (int $index) => selectionInfluencer(
        sprintf('A%02d', $index),
        sprintf('a%02d', $index),
        $criteria,
        [5, 1, 1, 1, 1, 5],
    ));
    $selectedIds = [$influencers->first()->id, $influencers->last()->id];

    $this->actingAs($admin);

    $this->get(route('admin.influencers.index', ['selected' => $selectedIds]))
        ->assertOk()
        ->assertSee('id="selected-count" class="font-semibold text-gray-900">2</strong>', false)
        ->assertSee('name="selected[]" value="'.$influencers->last()->id.'"', false);

    $this->get(route('admin.influencers.index', ['page' => 2, 'selected' => $selectedIds]))
        ->assertOk()
        ->assertSee('id="selected-count" class="font-semibold text-gray-900">2</strong>', false)
        ->assertSee('value="'.$influencers->last()->id.'" aria-label="Pilih A16" checked', false)
        ->assertSee('name="selected[]" value="'.$influencers->first()->id.'"', false);
});

it('rejects selected influencers that no longer exist', function () {
    $admin = User::factory()->admin()->create();
    $criteria = selectionCriteria();
    selectionInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);

    $this->actingAs($admin)
        ->get(route('admin.saw.index', ['selected' => [999]]))
        ->assertRedirect(route('admin.influencers.index', ['selected' => [999]]))
        ->assertSessionHas('error', 'Data influencer terpilih tidak ditemukan.');
});


it('deletes all influencers with hapus semua', function () {
    $admin = User::factory()->admin()->create();
    $criteria = selectionCriteria();
    $influencer = selectionInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);

    $this->actingAs($admin)
        ->post(route('admin.influencers.destroy-all'))
        ->assertRedirect(route('admin.influencers.index'))
        ->assertSessionHas('status', 'Semua data influencer berhasil dihapus.');

    expect(Influencer::query()->count())->toBe(0);
    $this->assertDatabaseMissing('influencer_scores', ['influencer_id' => $influencer->id]);
});
