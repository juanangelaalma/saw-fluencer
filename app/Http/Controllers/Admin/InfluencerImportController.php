<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmInfluencerImportRequest;
use App\Http\Requests\Admin\PreviewInfluencerImportRequest;
use App\Models\Criterion;
use App\Models\Influencer;
use App\Services\InfluencerScoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InfluencerImportController extends Controller
{
    public function create(): View
    {
        return view('admin.influencers.import.create', [
            'criteria' => Criterion::query()->orderBy('name')->get(),
            'rows' => [],
            'encodedRows' => null,
            'summary' => null,
        ]);
    }

    public function template(): Response
    {
        $headers = array_merge(['name', 'username'], Criterion::query()->orderBy('name')->pluck('name')->all());

        return response(implode(',', $headers)."\n", 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_influencer.csv"',
        ]);
    }

    public function preview(PreviewInfluencerImportRequest $request): View
    {
        $criteria = Criterion::query()->orderBy('name')->get();
        $rows = $this->parseCsv($request->file('file')->getRealPath());
        $previewRows = $this->previewRows($rows, $criteria);

        return view('admin.influencers.import.create', [
            'criteria' => $criteria,
            'rows' => $previewRows,
            'encodedRows' => base64_encode(json_encode($previewRows, JSON_THROW_ON_ERROR)),
            'summary' => [
                'valid' => collect($previewRows)->where('status', 'valid')->count(),
                'invalid' => collect($previewRows)->where('status', 'invalid')->count(),
                'skip' => collect($previewRows)->where('status', 'skip')->count(),
            ],
        ]);
    }

    public function store(ConfirmInfluencerImportRequest $request, InfluencerScoreService $scoreService): RedirectResponse
    {
        $rows = json_decode(base64_decode($request->validated('rows')), true, 512, JSON_THROW_ON_ERROR);
        $success = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if ($row['status'] === 'skip') {
                $skipped++;

                continue;
            }

            if ($row['status'] !== 'valid') {
                $failed++;

                continue;
            }

            $influencer = Influencer::query()->create($row['data']['influencer']);
            $scoreService->syncScores($influencer, $row['data']['criteria']);
            $success++;
        }

        return redirect()
            ->route('admin.influencers.index')
            ->with('status', "$success data berhasil diimport, $failed data gagal, $skipped data dilewati.");
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        $firstLine = fgets($handle);
        fclose($handle);

        $delimiters = [',', ';', "\t"];
        $delimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = count(explode($d, (string) $firstLine));
            if ($count > $maxCount) {
                $maxCount = $count;
                $delimiter = $d;
            }
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle, 0, $delimiter) ?: [];
        $rows = [];
        $line = 1;

        while (($values = fgetcsv($handle, 0, $delimiter)) !== false) {
            $line++;
            $rows[] = [
                'line' => $line,
                'data' => array_combine($header, array_pad($values, count($header), null)) ?: [],
            ];
        }

        fclose($handle);

        return $rows;
    }

    private function normalizeNumber(string $value): string
    {
        $value = str_replace([' ', '%'], '', $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            return str_replace(',', '.', str_replace('.', '', $value));
        }

        if (str_contains($value, ',')) {
            return str_replace(',', '.', $value);
        }

        return $value;
    }

    private function previewRows(array $rows, $criteria): array
    {
        return collect($rows)->map(function (array $row) use ($criteria) {
            $influencer = [
                'name' => trim((string) ($row['data']['name'] ?? '')),
                'username' => trim((string) ($row['data']['username'] ?? '')),
            ];

            $criteriaValues = [];

            foreach ($criteria as $criterion) {
                $criteriaValues[$criterion->id] = $this->normalizeNumber(trim((string) ($row['data'][$criterion->name] ?? '')));
            }

            $validatorRules = [
                'influencer.name' => ['required', 'string', 'max:255'],
                'influencer.username' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique(Influencer::class, 'username')],
                'criteria' => ['required', 'array'],
            ];

            foreach ($criteria as $criterion) {
                $validatorRules["criteria.$criterion->id"] = ['required', 'numeric', 'min:0'];
            }

            $validator = Validator::make([
                'influencer' => $influencer,
                'criteria' => $criteriaValues,
            ], $validatorRules);

            if (Influencer::query()->where('username', $influencer['username'])->exists()) {
                return [
                    'line' => $row['line'],
                    'data' => ['influencer' => $influencer, 'criteria' => $criteriaValues],
                    'status' => 'skip',
                    'message' => 'Username sudah ada.',
                ];
            }

            if ($validator->fails()) {
                return [
                    'line' => $row['line'],
                    'data' => ['influencer' => $influencer, 'criteria' => $criteriaValues],
                    'status' => 'invalid',
                    'message' => $validator->errors()->first(),
                ];
            }

            return [
                'line' => $row['line'],
                'data' => [
                    'influencer' => $influencer,
                    'criteria' => collect($criteriaValues)->map(fn (string $value) => (float) $value)->all(),
                ],
                'status' => 'valid',
                'message' => 'Valid.',
            ];
        })->all();
    }
}
