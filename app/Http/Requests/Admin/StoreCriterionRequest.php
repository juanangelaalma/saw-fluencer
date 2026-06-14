<?php

namespace App\Http\Requests\Admin;

use App\Models\Criterion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCriterionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'alpha_dash', Rule::unique(Criterion::class, 'code')],
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:0', 'max:100'],
            'type' => ['required', Rule::in([Criterion::TYPE_BENEFIT, Criterion::TYPE_COST])],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $total = Criterion::query()->sum('weight') + (int) $this->integer('weight');

            if ($total > 100) {
                $validator->errors()->add('weight', 'Total bobot kriteria maksimal 100%.');
            }
        }];
    }
}
