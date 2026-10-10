<?php

use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\SawCalculation;
use App\Models\SubCriterion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

function historyCriteria(): Collection
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

function historyInfluencer(string $name, string $username, Collection $criteria, array $values): Influencer
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

it('saves a calculation of checked influencers to history', function () {
    $admin = User::factory()->admin()->create();
    $criteria = historyCriteria();
    $first = historyInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);
    $second = historyInfluencer('A2', 'a2', $criteria, [1, 3, 1, 1, 2, 5]);
    historyInfluencer('A3', 'a3', $criteria, [1, 3, 2, 5, 1, 5]);

    $this->actingAs($admin)
        ->post(route('saw.history.store'), ['selected' => [$first->id, $second->id]])
        ->assertRedirect(route('saw.history.index'))
        ->assertSessionHas('status', 'Hasil perhitungan berhasil disimpan ke riwayat.');

    $calculation = SawCalculation::query()->latest('id')->firstOrFail();

    expect($calculation->total_influencers)->toBe(2)
        ->and($calculation->user_id)->toBe($admin->id)
        ->and($calculation->rows())->toHaveCount(2)
        ->and(collect($calculation->rows())->pluck('name')->all())->toContain('A1', 'A2')
        ->and(collect($calculation->rows())->pluck('name')->all())->not->toContain('A3')
        ->and($calculation->criteria())->toHaveCount(6);
});

it('keeps history visible after logout and login', function () {
    $admin = User::factory()->admin()->create();
    $criteria = historyCriteria();
    $first = historyInfluencer('A1', 'a1', $criteria, [5, 1, 1, 1, 1, 5]);
    $second = historyInfluencer('A2', 'a2', $criteria, [1, 3, 1, 1, 2, 5]);

    $this->actingAs($admin)
        ->post(route('saw.history.store'), ['selected' => [$first->id, $second->id]])
        ->assertRedirect(route('saw.history.index'));

    $this->post(route('logout'));

    $manager = User::factory()->manajer()->create();

    $this->actingAs($manager)
        ->get(route('saw.history.index'))
        ->assertOk()
        ->assertSee('Riwayat Perhitungan')
        ->assertSee('A1')
        ->assertSee('a1');
});



it('deletes a history record', function () {
    $user = User::factory()->admin()->create();
    $calculation = SawCalculation::factory()->create();

    $this->actingAs($user)
        ->delete(route('saw.history.destroy', $calculation))
        ->assertRedirect(route('saw.history.index'))
        ->assertSessionHas('status', 'Riwayat perhitungan berhasil dihapus.');

    $this->assertDatabaseMissing('saw_calculations', ['id' => $calculation->id]);
});

it('refuses to save an empty history entry', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->post(route('saw.history.store'))
        ->assertRedirect(route('admin.influencers.index'))
        ->assertSessionHas('error', 'Pilih minimal dua influencer untuk dihitung.');

    expect(SawCalculation::query()->count())->toBe(0);
});

it('keeps history read-only for managers', function () {
    $manager = User::factory()->manajer()->create();
    $calculation = SawCalculation::factory()->create();

    $this->actingAs($manager)
        ->post(route('saw.history.store'), ['selected' => [1, 2]])
        ->assertForbidden();

    $this->delete(route('saw.history.destroy', $calculation))
        ->assertForbidden();
});
