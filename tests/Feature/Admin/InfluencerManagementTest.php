<?php

use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\SubCriterion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createEpic3Criteria(): Collection
{
    return collect([
        ['code' => 'C1', 'name' => 'Engagement Rate', 'weight' => 25, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C2', 'name' => 'Follower', 'weight' => 15, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C3', 'name' => 'Average Like', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C4', 'name' => 'Average Comment', 'weight' => 10, 'type' => Criterion::TYPE_BENEFIT],
        ['code' => 'C5', 'name' => 'Rate Card', 'weight' => 20, 'type' => Criterion::TYPE_COST],
        ['code' => 'C6', 'name' => 'Average Reel View', 'weight' => 20, 'type' => Criterion::TYPE_BENEFIT],
    ])->map(function (array $criterion): Criterion {
        $model = Criterion::query()->create($criterion);

        foreach (range(1, 5) as $level) {
            SubCriterion::query()->create([
                'criterion_id' => $model->id,
                'level' => $level,
                'label' => "Level $level",
                'min_value' => ($level - 1) * 100,
                'max_value' => ($level * 100) - 1,
            ]);
        }

        return $model;
    });
}

function validInfluencerPayload(array $overrides = []): array
{
    $criteria = Criterion::query()->pluck('id')->mapWithKeys(fn (int $id) => [$id => '125.5'])->all();

    return $overrides + [
        'name' => 'Influencer Satu',
        'username' => 'influencer_satu',
        'criteria' => $criteria,
    ];
}

it('allows admin to view influencers with dynamic criteria columns', function () {
    $admin = User::factory()->admin()->create();
    $criteria = createEpic3Criteria();
    $influencer = Influencer::factory()->create(['name' => 'Influencer Satu', 'username' => 'influencer_satu']);

    foreach ($criteria as $criterion) {
        $influencer->scores()->create([
            'criterion_id' => $criterion->id,
            'raw_value' => 125,
            'likert_value' => 2,
        ]);
    }

    $this->actingAs($admin)
        ->get(route('admin.influencers.index'))
        ->assertOk()
        ->assertSee('Influencer Satu')
        ->assertSee('influencer_satu')
        ->assertSee('Engagement Rate')
        ->assertSee('125.00');
});

it('allows admin to create influencer and generate dynamic scores', function () {
    $admin = User::factory()->admin()->create();
    createEpic3Criteria();

    $this->actingAs($admin)
        ->post(route('admin.influencers.store'), validInfluencerPayload())
        ->assertRedirect();

    $influencer = Influencer::query()->where('username', 'influencer_satu')->firstOrFail();

    expect($influencer->scores()->count())->toBe(6);
    $this->assertDatabaseHas('influencer_scores', [
        'influencer_id' => $influencer->id,
        'raw_value' => 125.5,
        'likert_value' => 2,
    ]);
});

it('validates duplicate username and dynamic numeric fields', function () {
    $admin = User::factory()->admin()->create();
    $criteria = createEpic3Criteria();
    Influencer::factory()->create(['username' => 'influencer_satu']);
    $payload = validInfluencerPayload([
        'criteria' => [$criteria->first()->id => 'abc'] + $criteria->skip(1)->pluck('id')->mapWithKeys(fn (int $id) => [$id => '125'])->all(),
    ]);

    $this->actingAs($admin)
        ->post(route('admin.influencers.store'), $payload)
        ->assertSessionHasErrors(['username', 'criteria.'.$criteria->first()->id]);
});

it('allows admin to update influencer and refresh dynamic scores', function () {
    $admin = User::factory()->admin()->create();
    $criteria = createEpic3Criteria();
    $influencer = Influencer::factory()->create(['username' => 'old_user']);

    $payload = validInfluencerPayload([
        'name' => 'Influencer Baru',
        'username' => 'new_user',
        'criteria' => $criteria->pluck('id')->mapWithKeys(fn (int $id) => [$id => $id === $criteria->first()->id ? '425' : '125'])->all(),
    ]);

    $this->actingAs($admin)
        ->put(route('admin.influencers.update', $influencer), $payload)
        ->assertRedirect(route('admin.influencers.edit', $influencer));

    expect($influencer->refresh()->username)->toBe('new_user')
        ->and($influencer->scores()->where('likert_value', 5)->exists())->toBeTrue();
});

it('hard deletes influencer', function () {
    $admin = User::factory()->admin()->create();
    $influencer = Influencer::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.influencers.destroy', $influencer))
        ->assertRedirect(route('admin.influencers.index'));

    $this->assertDatabaseMissing('influencers', ['id' => $influencer->id]);
});

it('denies manajer access to influencer routes', function (string $method, string $routeName) {
    $manager = User::factory()->manajer()->create();
    $influencer = Influencer::factory()->create();

    $route = match ($routeName) {
        'admin.influencers.edit', 'admin.influencers.update', 'admin.influencers.destroy' => route($routeName, $influencer),
        default => route($routeName),
    };

    $this->actingAs($manager)
        ->{$method}($route)
        ->assertForbidden();
})->with([
    ['get', 'admin.influencers.index'],
    ['get', 'admin.influencers.create'],
    ['post', 'admin.influencers.store'],
    ['get', 'admin.influencers.edit'],
    ['put', 'admin.influencers.update'],
    ['delete', 'admin.influencers.destroy'],
    ['get', 'admin.influencers.import.create'],
    ['get', 'admin.influencers.import.template'],
    ['post', 'admin.influencers.import.preview'],
    ['post', 'admin.influencers.import.store'],
]);

it('downloads dynamic csv template', function () {
    $admin = User::factory()->admin()->create();
    createEpic3Criteria();

    $this->actingAs($admin)
        ->get(route('admin.influencers.import.template'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8')
        ->assertSee('name,username,Average Comment,Average Like,Average Reel View,Engagement Rate,Follower,Rate Card');
});

it('previews dynamic csv import with valid invalid and skipped rows', function () {
    Storage::fake('local');
    $admin = User::factory()->admin()->create();
    createEpic3Criteria();
    Influencer::factory()->create(['username' => 'existing_user']);

    $csv = "name;username;Average Comment;Average Like;Average Reel View;Engagement Rate;Follower;Rate Card\n".
        "Valid User;valid_user;10;100;2000;10,5%;1000;500000\n".
        "Invalid User;invalid user;10;100;2000;abc;1000;500000\n".
        "Existing User;existing_user;10;100;2000;10;1000;500000\n";

    $file = UploadedFile::fake()->createWithContent('influencers.csv', $csv);

    $this->actingAs($admin)
        ->post(route('admin.influencers.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('1 valid, 1 gagal, 1 dilewati')
        ->assertSee('valid_user')
        ->assertSee('invalid')
        ->assertSee('skip');
});

it('confirms dynamic csv import and stores only valid rows', function () {
    $admin = User::factory()->admin()->create();
    $criteria = createEpic3Criteria();
    $criteriaValues = $criteria->pluck('id')->mapWithKeys(fn (int $id) => [$id => '125'])->all();

    $rows = [[
        'line' => 2,
        'data' => ['influencer' => ['name' => 'Valid User', 'username' => 'valid_user'], 'criteria' => $criteriaValues],
        'status' => 'valid',
        'message' => 'Valid.',
    ], [
        'line' => 3,
        'data' => ['influencer' => ['name' => 'Invalid User', 'username' => 'invalid user'], 'criteria' => $criteriaValues],
        'status' => 'invalid',
        'message' => 'Invalid.',
    ], [
        'line' => 4,
        'data' => ['influencer' => ['name' => 'Existing User', 'username' => 'existing_user'], 'criteria' => $criteriaValues],
        'status' => 'skip',
        'message' => 'Username sudah ada.',
    ]];

    $this->actingAs($admin)
        ->post(route('admin.influencers.import.store'), [
            'rows' => base64_encode(json_encode($rows, JSON_THROW_ON_ERROR)),
        ])
        ->assertRedirect(route('admin.influencers.index'))
        ->assertSessionHas('status', '1 data berhasil diimport, 1 data gagal, 1 data dilewati.');

    $this->assertDatabaseHas('influencers', ['username' => 'valid_user']);
    $this->assertDatabaseMissing('influencers', ['username' => 'invalid user']);
});
