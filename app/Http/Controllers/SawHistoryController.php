<?php

namespace App\Http\Controllers;

use App\Models\SawCalculation;
use App\Services\SawCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SawHistoryController extends Controller
{
    public function index(): View
    {
        return view('saw.history.index', [
            'calculations' => SawCalculation::query()
                ->with('user')
                ->latest('id')
                ->paginate(10),
        ]);
    }

    public function store(Request $request, SawCalculationService $service): RedirectResponse
    {
        $selectedIds = $this->resolveSelectedIds($request);

        if (count($selectedIds) < 2) {
            return redirect()
                ->route('admin.influencers.index', ['selected' => $selectedIds])
                ->with('error', 'Pilih minimal dua influencer untuk dihitung.');
        }

        $result = $service->calculate($selectedIds);

        if (count($result['rows']) < 2) {
            return redirect()
                ->route('admin.influencers.index', ['selected' => $selectedIds])
                ->with('error', 'Pilih minimal dua influencer yang masih tersedia untuk dihitung.');
        }

        SawCalculation::query()->create([
            'user_id' => $request->user()->id,
            'total_influencers' => count($result['rows']),
            'payload' => $service->buildPayload($result),
        ]);

        return redirect()
            ->route('saw.history.index')
            ->with('status', 'Hasil perhitungan berhasil disimpan ke riwayat.');
    }

    public function show(SawCalculation $calculation): View
    {
        return view('saw.history.show', [
            'calculation' => $calculation,
            'criteriaList' => $calculation->criteria(),
            'rowsList' => $calculation->rows(),
        ]);
    }

    public function destroy(SawCalculation $calculation): RedirectResponse
    {
        $calculation->delete();

        return redirect()
            ->route('saw.history.index')
            ->with('status', 'Riwayat perhitungan berhasil dihapus.');
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

        return array_values(array_unique(array_filter(array_map('intval', $selected), fn (int $id): bool => $id > 0)));
    }
}
