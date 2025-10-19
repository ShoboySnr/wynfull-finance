<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name'         => ['nullable', 'string', 'max:120'],
            'last_name'          => ['nullable', 'string', 'max:120'],
            'professional_title' => ['nullable', 'string', 'max:160'],
            'specialities'       => ['nullable', 'array'],
            'specialities.*'     => ['string', 'max:120'],
            'email'              => ['nullable', 'email', 'max:191'],
            'phone'              => ['nullable', 'string', 'max:60'],
            'bio'                => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('specialities') && is_string($this->specialities)) {
            $this->merge([
                'specialities' => collect(explode(',', $this->specialities))
                    ->map(fn($s) => trim($s))->filter()->unique()->values()->all()
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }
}
