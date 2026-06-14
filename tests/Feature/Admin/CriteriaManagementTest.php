<?php

use App\Models\Criterion;
use App\Models\SubCriterion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createDefaultCriteria(): void
{
    collect([
        ['code' => 'C1', 'name' => 'Engagement Rate', 'weight' => 25, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C2', 'name' => 'Follower', 'weight' => 15, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C3', 'name' => 'Jumlah Like', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C4', 'name' => 'Jumlah Komentar', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C5', 'name' => 'Rate Card', 'weight' => 20, 'type' => Criterion::TYPE_COST],
        ['code' => 'C6', 'name' => 'Niche', 'weight' => 20, 'type' => Criterion::TYPE_BENEFIT],
    ])->each(fn (array $criterion) => Criterion::query()->create($criterion));
}

it('allows admin to view criteria', function () {
    $admin = User::factory()->admin()->create();
    createDefaultCriteria();

    $this->actingAs($admin)
        ->get(route('admin.criteria.index'))
        ->assertOk()
        ->assertSee('Engagement Rate')
        ->assertSee('Total bobot: 100% / maksimal 100%')
        ->assertDontSee('Nonaktif');
});

it('allows admin to create criterion when total weight stays below 100', function () {
    $admin = User::factory()->admin()->create();
    Criterion::factory()->create(['code' => 'C1', 'weight' => 50]);

    $this->actingAs($admin)
        ->post(route('admin.criteria.store'), [
            'code' => 'C2',
            'name' => 'Audience Quality',
            'weight' => 25,
            'type' => Criterion::TYPE_BENEFIT,
        ])
        ->assertRedirect(route('admin.criteria.index'));

    $this->assertDatabaseHas('criteria', ['code' => 'C2', 'weight' => 25]);
});

it('rejects criterion when total weight is above 100', function () {
    $admin = User::factory()->admin()->create();
    createDefaultCriteria();

    $this->actingAs($admin)
        ->post(route('admin.criteria.store'), [
            'code' => 'C7',
            'name' => 'Audience Quality',
            'weight' => 10,
            'type' => Criterion::TYPE_BENEFIT,
        ])
        ->assertSessionHasErrors(['weight']);
});

it('allows admin to update criterion without changing total weight', function () {
    $admin = User::factory()->admin()->create();
    createDefaultCriteria();
    $criterion = Criterion::query()->where('code', 'C1')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.criteria.update', $criterion), [
            'code' => 'C1',
            'name' => 'Engagement Rate Instagram',
            'weight' => 25,
            'type' => Criterion::TYPE_BENEFIT,
        ])
        ->assertRedirect(route('admin.criteria.edit', $criterion));

    expect($criterion->refresh()->name)->toBe('Engagement Rate Instagram');
});

it('prevents deleting criterion with sub criteria', function () {
    $admin = User::factory()->admin()->create();
    $criterion = Criterion::factory()->create(['code' => 'C1', 'weight' => 100]);
    SubCriterion::factory()->create(['criterion_id' => $criterion->id, 'level' => 1]);

    $this->actingAs($admin)
        ->delete(route('admin.criteria.destroy', $criterion))
        ->assertRedirect(route('admin.criteria.index'));

    $this->assertDatabaseHas('criteria', ['id' => $criterion->id]);
});

it('denies manajer access to criteria routes', function (string $method, string $routeName) {
    $manager = User::factory()->manajer()->create();
    $criterion = Criterion::factory()->create(['code' => 'C1', 'weight' => 100]);

    $route = match ($routeName) {
        'admin.criteria.edit', 'admin.criteria.update', 'admin.criteria.destroy', 'admin.criteria.sub-criteria.edit', 'admin.criteria.sub-criteria.update' => route($routeName, $criterion),
        default => route($routeName),
    };

    $this->actingAs($manager)
        ->{$method}($route)
        ->assertForbidden();
})->with([
    ['get', 'admin.criteria.index'],
    ['get', 'admin.criteria.create'],
    ['post', 'admin.criteria.store'],
    ['get', 'admin.criteria.edit'],
    ['put', 'admin.criteria.update'],
    ['delete', 'admin.criteria.destroy'],
    ['get', 'admin.criteria.sub-criteria.edit'],
    ['put', 'admin.criteria.sub-criteria.update'],
]);

it('updates five sub criteria levels', function () {
    $admin = User::factory()->admin()->create();
    $criterion = Criterion::factory()->create(['code' => 'C1', 'weight' => 100]);

    $payload = collect(range(1, 5))->map(fn (int $level) => [
        'level' => $level,
        'label' => "Level $level",
        'min_value' => $level * 10,
        'max_value' => ($level * 10) + 9,
    ])->all();

    $this->actingAs($admin)
        ->put(route('admin.criteria.sub-criteria.update', $criterion), ['sub_criteria' => $payload])
        ->assertRedirect(route('admin.criteria.sub-criteria.edit', $criterion));

    expect($criterion->subCriteria()->count())->toBe(5);
    $this->assertDatabaseHas('sub_criteria', ['criterion_id' => $criterion->id, 'level' => 5, 'label' => 'Level 5']);
});

it('rejects sub criteria without five likert levels', function () {
    $admin = User::factory()->admin()->create();
    $criterion = Criterion::factory()->create(['code' => 'C1', 'weight' => 100]);

    $this->actingAs($admin)
        ->put(route('admin.criteria.sub-criteria.update', $criterion), [
            'sub_criteria' => [
                ['level' => 1, 'label' => 'Kurang', 'min_value' => 0, 'max_value' => 10],
            ],
        ])
        ->assertSessionHasErrors(['sub_criteria']);
});
