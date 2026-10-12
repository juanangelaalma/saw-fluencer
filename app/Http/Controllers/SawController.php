<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use App\Services\SawCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SawController extends Controller
{
    public function index(Request $request, SawCalculationService $service): View|RedirectResponse
    {
        $limit = $request->input('limit', '10');
        $limitOptions = ['10', '25', '50', '100', 'all'];

        if (! in_array($limit, $limitOptions, true)) {
            $limit = '10';
        }

        $user = $request->user();

        if ($user->canManageInfluencers()) {
            $selectedIds = $this->resolveSelectedIds($request);

            if ($selectedIds === []) {
                return redirect()
                    ->route('admin.influencers.index')
                    ->with('error', 'Pilih minimal dua influencer untuk dihitung.');
            }

            if (count($selectedIds) === 1 && ! Influencer::query()->whereKey($selectedIds)->exists()) {
                return redirect()
                    ->route('admin.influencers.index', ['selected' => $selectedIds])
                    ->with('error', 'Data influencer terpilih tidak ditemukan.');
            }

            if (count($selectedIds) === 1) {
                return redirect()
                    ->route('admin.influencers.index', ['selected' => $selectedIds])
                    ->with('error', 'Pilih minimal dua influencer untuk dihitung.');
            }

            $onlyIds = $selectedIds;
        } else {
            $selectedIds = [];
            $onlyIds = null;
        }

        $data = $service->calculate($onlyIds);
        $data['limit'] = $limit;
        $data['limitOptions'] = $limitOptions;
        $data['totalRows'] = count($data['rows']);
        $data['totalInfluencers'] = Influencer::query()->count();
        $data['selectedIds'] = $selectedIds;

        if ($user->canManageInfluencers()) {
            if ($data['totalRows'] === 0) {
                return redirect()
                    ->route('admin.influencers.index', ['selected' => $selectedIds])
                    ->with('error', 'Data influencer terpilih tidak ditemukan.');
            }

            if ($data['totalRows'] < 2) {
                return redirect()
                    ->route('admin.influencers.index', ['selected' => $selectedIds])
                    ->with('error', 'Pilih minimal dua influencer yang masih tersedia untuk dihitung.');
            }
        }

        if ($limit !== 'all') {
            $data['rows'] = array_slice($data['rows'], 0, (int) $limit);
        }

        return view('saw.index', $data);
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
