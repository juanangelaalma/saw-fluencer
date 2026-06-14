<?php

namespace App\Http\Requests\Admin;

use App\Models\Criterion;
use App\Models\Influencer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInfluencerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique(Influencer::class, 'username')],
            'criteria' => ['required', 'array'],
        ];

        foreach (Criterion::query()->pluck('id') as $id) {
            $rules["criteria.$id"] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }
}
