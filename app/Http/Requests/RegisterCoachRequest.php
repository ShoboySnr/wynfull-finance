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
            'password'    => ['required','confirmed','min:8'],
            'experience'  => ['required','in:1-3,3-5,5+'],
            'specialties' => ['array'],
            'specialties.*' => ['string','max:255'],
            'linkedin'    => ['nullable','url','max:255'],
            'website'     => ['nullable','url','max:255'],
            'accept'      => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'specialties' => (array) $this->input('specialties', []),
            'accept' => $this->boolean('accept'),
        ]);
    }
    public function authorize(): bool
    {
        return true;
    }
}
