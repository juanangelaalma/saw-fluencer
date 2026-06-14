<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCriterionRequest;
use App\Http\Requests\Admin\UpdateCriterionRequest;
use App\Models\Criterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CriterionController extends Controller
{
    public function index(): View
    {
        return view('admin.criteria.index', [
            'criteria' => Criterion::query()
                ->withCount('subCriteria')
                ->orderBy('code')
                ->paginate(15),
            'weightTotal' => Criterion::query()->sum('weight'),
        ]);
    }

    public function create(): View
    {
        return view('admin.criteria.create', [
            'criterion' => new Criterion(['type' => Criterion::TYPE_BENEFIT]),
            'types' => Criterion::typeLabels(),
            'otherWeightTotal' => Criterion::query()->sum('weight'),
        ]);
    }

    public function store(StoreCriterionRequest $request): RedirectResponse
    {
        Criterion::query()->create($request->validated());

        return redirect()
            ->route('admin.criteria.index')
            ->with('status', 'Kriteria berhasil dibuat.');
    }

    public function edit(Criterion $criterion): View
    {
        return view('admin.criteria.edit', [
            'criterion' => $criterion,
            'types' => Criterion::typeLabels(),
            'otherWeightTotal' => Criterion::query()
                ->whereKeyNot($criterion)
                ->sum('weight'),
        ]);
    }

    public function update(UpdateCriterionRequest $request, Criterion $criterion): RedirectResponse
    {
        $criterion->update($request->validated());

        return redirect()
            ->route('admin.criteria.edit', $criterion)
            ->with('status', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Criterion $criterion): RedirectResponse
    {
        if ($criterion->subCriteria()->exists()) {
            return redirect()
                ->route('admin.criteria.index')
                ->with('error', 'Kriteria tidak dapat dihapus karena masih memiliki sub kriteria.');
        }

        $criterion->delete();

        return redirect()
            ->route('admin.criteria.index')
            ->with('status', 'Kriteria berhasil dihapus.');
    }
}
