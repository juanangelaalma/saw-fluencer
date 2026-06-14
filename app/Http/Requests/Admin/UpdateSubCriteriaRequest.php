<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSubCriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'sub_criteria' => ['required', 'array', 'size:5'],
            'sub_criteria.*.level' => ['required', 'integer', 'between:1,5'],
            'sub_criteria.*.label' => ['required', 'string', 'max:255'],
            'sub_criteria.*.min_value' => ['nullable', 'numeric'],
            'sub_criteria.*.max_value' => ['nullable', 'numeric'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $levels = collect($this->input('sub_criteria', []))->pluck('level')->sort()->values()->all();

            if ($levels !== [1, 2, 3, 4, 5]) {
                $validator->errors()->add('sub_criteria', 'Sub kriteria harus memiliki level 1 sampai 5.');
            }

            foreach ($this->input('sub_criteria', []) as $index => $subCriterion) {
                $min = $subCriterion['min_value'] ?? null;
                $max = $subCriterion['max_value'] ?? null;

                if ($min !== null && $max !== null && (float) $min > (float) $max) {
                    $validator->errors()->add("sub_criteria.$index.max_value", 'Batas atas harus lebih besar atau sama dengan batas bawah.');
                }
            }
        }];
    }
}
