<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInfluencerRequest;
use App\Http\Requests\Admin\UpdateInfluencerRequest;
use App\Models\Criterion;
use App\Models\Influencer;
use App\Services\InfluencerScoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfluencerController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->canViewInfluencers(), 403);

        $selectedIds = $this->resolveSelectedIds($request);

        if ($selectedIds !== []) {
            $selectedIds = Influencer::query()->whereKey($selectedIds)->pluck('id')->all();
        }

        $search = trim((string) $request->input('search', ''));

        $influencers = Influencer::query()
            ->with('scores.criterion')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('username', 'like', '%'.$search.'%');
            }))
            ->orderBy('name')
            ->orderBy('username')
            ->paginate(15)
            ->withQueryString();

        if ($selectedIds !== []) {
            $influencers->appends(['selected' => $selectedIds]);
        }

        return view('admin.influencers.index', [
            'criteria' => Criterion::query()->orderBy('name')->get(),
            'influencers' => $influencers,
            'selectedIds' => $selectedIds,
            'search' => $search,
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function resolveSelectedIds(Request $request): array
    {
        $selected = $request->input('selected');

        if (! is_array($selected)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            array_map('intval', $selected),
            fn (int $id): bool => $id > 0,
        )));
    }

    public function create(): View
    {
        return view('admin.influencers.create', [
            'criteria' => Criterion::query()->orderBy('name')->get(),
            'influencer' => new Influencer,
            'scores' => collect(),
        ]);
    }

    public function store(StoreInfluencerRequest $request, InfluencerScoreService $scoreService): RedirectResponse
    {
        $validated = $request->validated();
        $influencer = Influencer::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
        ]);

        $scoreService->syncScores($influencer, $validated['criteria']);

        return redirect()
            ->route('admin.influencers.edit', $influencer)
            ->with('status', 'Influencer berhasil dibuat.');
    }

    public function edit(Influencer $influencer): View
    {
        return view('admin.influencers.edit', [
            'criteria' => Criterion::query()->orderBy('name')->get(),
            'influencer' => $influencer->load('scores.criterion'),
            'scores' => $influencer->scores->keyBy('criterion_id'),
        ]);
    }

    public function update(UpdateInfluencerRequest $request, Influencer $influencer, InfluencerScoreService $scoreService): RedirectResponse
    {
        $validated = $request->validated();
        $influencer->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
        ]);

        $scoreService->syncScores($influencer, $validated['criteria']);

        return redirect()
            ->route('admin.influencers.edit', $influencer)
            ->with('status', 'Influencer berhasil diperbarui.');
    }

    public function destroy(Influencer $influencer): RedirectResponse
    {
        $influencer->delete();

        return redirect()
            ->route('admin.influencers.index')
            ->with('status', 'Influencer berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        Influencer::query()->delete();

        return redirect()
            ->route('admin.influencers.index')
            ->with('status', 'Semua data influencer berhasil dihapus.');
    }
}
