<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSubCriteriaRequest;
use App\Models\Criterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubCriterionController extends Controller
{
    public function edit(Criterion $criterion): View
    {
        $subCriteria = collect(range(1, 5))->map(function (int $level) use ($criterion) {
            return $criterion->subCriteria->firstWhere('level', $level) ?? $criterion->subCriteria()->make([
                'level' => $level,
                'label' => match ($level) {
                    1 => 'Kurang',
                    2 => 'Cukup',
                    3 => 'Baik',
                    4 => 'Sangat Baik',
                    5 => 'Terbaik',
                },
            ]);
        });

        return view('admin.criteria.sub-criteria.edit', [
            'criterion' => $criterion,
            'subCriteria' => $subCriteria,
        ]);
    }

    public function update(UpdateSubCriteriaRequest $request, Criterion $criterion): RedirectResponse
    {
        foreach ($request->validated('sub_criteria') as $subCriterion) {
            $criterion->subCriteria()->updateOrCreate([
                'level' => $subCriterion['level'],
            ], $subCriterion);
        }

        return redirect()
            ->route('admin.criteria.sub-criteria.edit', $criterion)
            ->with('status', 'Sub kriteria berhasil diperbarui.');
    }
}
