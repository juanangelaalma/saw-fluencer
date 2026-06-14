<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInfluencerRequest;
use App\Http\Requests\Admin\UpdateInfluencerRequest;
use App\Models\Criterion;
use App\Models\Influencer;
use App\Services\InfluencerScoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InfluencerController extends Controller
{
    public function index(): View
    {
        return view('admin.influencers.index', [
            'criteria' => Criterion::query()->orderBy('name')->get(),
            'influencers' => Influencer::query()
                ->with('scores.criterion')
                ->orderBy('name')
                ->orderBy('username')
                ->paginate(15),
        ]);
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
}
