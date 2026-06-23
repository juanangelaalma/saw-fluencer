<?php

namespace App\Http\Controllers;

use App\Services\SawCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SawController extends Controller
{
    public function index(Request $request, SawCalculationService $service): View
    {
        $limit = $request->input('limit', '10');
        $limitOptions = ['10', '25', '50', '100', 'all'];

        if (! in_array($limit, $limitOptions, true)) {
            $limit = '10';
        }

        $data = $service->calculate();
        $data['limit'] = $limit;
        $data['limitOptions'] = $limitOptions;
        $data['totalRows'] = count($data['rows']);

        if ($limit !== 'all') {
            $data['rows'] = array_slice($data['rows'], 0, (int) $limit);
        }

        return view('saw.index', $data);
    }
}
