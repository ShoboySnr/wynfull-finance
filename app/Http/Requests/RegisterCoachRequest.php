<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCoachRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:255'],
            'email'       => ['required','email','max:255','unique:users,email'],
            'password'    => ['required','string','min:8','confirmed'],
            'experience'  => ['nullable','in:1-3,3-5,5+'],
            'specialties' => ['nullable','array'],
            'specialties.*' => ['string','max:255'],
            'linkedin'    => ['nullable','url','max:255'],
            'website'     => ['nullable','url','max:255'],
            'accept'      => ['nullable','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'specialties' => (array) $this->input('specialties', []),
        ]);
    }
    public function authorize(): bool
    {
        return true;
    }
}
