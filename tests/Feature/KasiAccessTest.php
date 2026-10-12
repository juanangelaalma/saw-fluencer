<?php

use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\SawCalculation;
use App\Models\SubCriterion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

function kasiCriteria(): Collection
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

function kasiInfluencer(string $name, string $username, Collection $criteria, array $values): Influencer
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

it('registers the kasi role label', function () {
    expect(User::roleLabels()[User::ROLE_KASI])->toBe('Kasi/SPV');
});

it('redirects kasi users to their dashboard after login', function () {
    $kasi = User::factory()->kasi()->create();

    $this->post('/login', ['username' => $kasi->username, 'password' => 'password'])
        ->assertRedirect(route('kasi.dashboard'));

    $this->actingAs($kasi)
        ->get('/dashboard')
        ->assertRedirect(route('kasi.dashboard'));
});

it('lets admin create a kasi user and rejects unknown roles', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Kasi Satu',
            'username' => 'kasi_satu',
            'password' => 'password123',
            'role' => User::ROLE_KASI,
        ])
        ->assertRedirect(route('admin.users.index'));

    $kasi = User::query()->where('username', 'kasi_satu')->firstOrFail();

    expect($kasi->role)->toBe(User::ROLE_KASI)
        ->and($kasi->roleLabel())->toBe('Kasi/SPV')
        ->and($kasi->is_active)->toBeTrue();

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Role Asing',
            'username' => 'role_asing',
            'password' => 'password123',
            'role' => 'direktur',
        ])
        ->assertSessionHasErrors(['role']);

    expect(User::query()->where('username', 'role_asing')->exists())->toBeFalse();
});

it('shows the kasi dashboard with the read-only decision flow', function () {
    $kasi = User::factory()->kasi()->create(['username' => 'kasiuser']);

    $this->actingAs($kasi)
        ->get(route('kasi.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Kasi/SPV')
        ->assertSee('kasiuser')
        ->assertSee('Kasi/SPV')
        ->assertSee('Data Influencer')
        ->assertSee('Perhitungan SAW')
        ->assertSee('Riwayat Perhitungan')
        ->assertDontSee('Manajemen Pengguna')
        ->assertDontSee('Manajemen Kriteria');
});

it('lets kasi view influencer data without any management action', function () {
    $kasi = User::factory()->kasi()->create();
    $criteria = kasiCriteria();
    kasiInfluencer('Budi Santoso', 'budi_s', $criteria, [5, 1, 1, 1, 1, 5]);
    kasiInfluencer('Siti Rahayu', 'siti_r', $criteria, [1, 3, 1, 1, 2, 5]);

    $this->actingAs($kasi)
        ->get(route('kasi.influencers.index'))
        ->assertOk()
        ->assertSee('Data Influencer')
        ->assertSee('Budi Santoso')
        ->assertSee('Siti Rahayu')
        ->assertSee('Engagement Rate')
        ->assertDontSee('Tambah Influencer')
        ->assertDontSee('Import CSV')
        ->assertDontSee('Hitung Data Terpilih')
        ->assertDontSee('Hapus Semua')
        ->assertDontSee('name="selected[]"', false)
        ->assertDontSee('type="checkbox"', false);
});

it('lets kasi search influencers by name and username', function () {
    $kasi = User::factory()->kasi()->create();
    $criteria = kasiCriteria();
    kasiInfluencer('Budi Santoso', 'budi_s', $criteria, [5, 1, 1, 1, 1, 5]);
    kasiInfluencer('Siti Rahayu', 'siti_r', $criteria, [1, 3, 1, 1, 2, 5]);

    $this->actingAs($kasi)
        ->get(route('kasi.influencers.index', ['search' => 'Budi']))
        ->assertOk()
        ->assertSee('Budi Santoso')
        ->assertDontSee('Siti Rahayu');

    $this->actingAs($kasi)
        ->get(route('kasi.influencers.index', ['search' => 'siti_r']))
        ->assertOk()
        ->assertSee('Siti Rahayu')
        ->assertDontSee('Budi Santoso');

    $this->actingAs($kasi)
        ->get(route('kasi.influencers.index', ['search' => 'tidakada']))
        ->assertOk()
        ->assertSee('Tidak ada influencer yang cocok dengan pencarian');
});

it('lets admin search influencers and keeps the search term while paging', function () {
    $admin = User::factory()->admin()->create();
    $criteria = kasiCriteria();

    foreach (range(1, 16) as $index) {
        kasiInfluencer(sprintf('Budi %02d', $index), sprintf('budi_%02d', $index), $criteria, [5, 1, 1, 1, 1, 5]);
    }

    kasiInfluencer('Siti Rahayu', 'siti_r', $criteria, [1, 3, 1, 1, 2, 5]);

    $this->actingAs($admin)
        ->get(route('admin.influencers.index', ['search' => 'Budi']))
        ->assertOk()
        ->assertSee('Hasil untuk "Budi": 16 data ditemukan.', false)
        ->assertSee('Budi 01')
        ->assertSee('search=Budi', false)
        ->assertDontSee('Siti Rahayu');

    $this->actingAs($admin)
        ->get(route('admin.influencers.index', ['search' => 'Budi', 'page' => 2]))
        ->assertOk()
        ->assertSee('Budi 16')
        ->assertSee('value="Budi"', false);
});

it('lets kasi view the full calculation and ranking without selecting data', function () {
    $kasi = User::factory()->kasi()->create();
    $criteria = kasiCriteria();
    kasiInfluencer('Budi Santoso', 'budi_s', $criteria, [5, 1, 1, 1, 1, 5]);
    kasiInfluencer('Siti Rahayu', 'siti_r', $criteria, [1, 3, 1, 1, 2, 5]);
    kasiInfluencer('Andi Wijaya', 'andi_w', $criteria, [1, 3, 2, 5, 1, 5]);

    $this->actingAs($kasi)
        ->get(route('kasi.saw.index'))
        ->assertOk()
        ->assertSee('Perhitungan SAW')
        ->assertSee('Menghitung seluruh 3 data influencer')
        ->assertSee('1. Konversi Nilai Input ke Nilai Rating (Likert)')
        ->assertSee('2. Perhitungan Normalisasi (Pembagi)')
        ->assertSee('3. Hasil Matriks Normalisasi (R)')
        ->assertSee('4. Nilai Akhir (V)')
        ->assertSee('5. Perangkingan')
        ->assertSee('Budi Santoso')
        ->assertSee('Andi Wijaya')
        ->assertDontSee('Simpan ke Riwayat');
});

it('lets kasi view history details but not save or delete them', function () {
    $admin = User::factory()->admin()->create();
    $kasi = User::factory()->kasi()->create();
    $criteria = kasiCriteria();
    $first = kasiInfluencer('Budi Santoso', 'budi_s', $criteria, [5, 1, 1, 1, 1, 5]);
    $second = kasiInfluencer('Siti Rahayu', 'siti_r', $criteria, [1, 3, 1, 1, 2, 5]);

    $this->actingAs($admin)
        ->post(route('saw.history.store'), ['selected' => [$first->id, $second->id]])
        ->assertRedirect(route('saw.history.index'));

    $calculation = SawCalculation::query()->latest('id')->firstOrFail();

    $this->actingAs($kasi)
        ->get(route('saw.history.index'))
        ->assertOk()
        ->assertSee('Riwayat Perhitungan')
        ->assertSee('Budi Santoso')
        ->assertDontSee('Riwayat perhitungan berhasil dihapus.')
        ->assertDontSee('saw.history.destroy', false);

    $this->actingAs($kasi)
        ->get(route('saw.history.show', $calculation))
        ->assertOk()
        ->assertSee('Detail Riwayat Perhitungan')
        ->assertSee('5. Perangkingan');

    $this->actingAs($kasi)
        ->post(route('saw.history.store'), ['selected' => [$first->id, $second->id]])
        ->assertForbidden();

    $this->actingAs($kasi)
        ->delete(route('saw.history.destroy', $calculation))
        ->assertForbidden();

    expect(SawCalculation::query()->count())->toBe(1);
});

it('denies kasi access to every admin route', function (string $method, string $routeName, array $parameters) {
    $kasi = User::factory()->kasi()->create();

    $this->actingAs($kasi)
        ->{$method}(route($routeName, $parameters))
        ->assertForbidden();
})->with([
    ['get', 'admin.dashboard', []],
    ['get', 'admin.users.index', []],
    ['get', 'admin.criteria.index', []],
    ['get', 'admin.influencers.index', []],
    ['get', 'admin.influencers.create', []],
    ['post', 'admin.influencers.destroy-all', []],
    ['get', 'admin.saw.index', []],
]);

it('keeps manajer away from influencer data', function () {
    $manager = User::factory()->manajer()->create();
    $criteria = kasiCriteria();
    kasiInfluencer('Budi Santoso', 'budi_s', $criteria, [5, 1, 1, 1, 1, 5]);

    $this->actingAs($manager)
        ->get(route('kasi.influencers.index'))
        ->assertForbidden();
});
