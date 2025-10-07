<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => ['required','string','max:255'],
            'email'      => ['required','email','max:255','unique:users,email'],
            'password'   => ['required','confirmed','min:8'],
            'goal'       => ['required','string','max:255'],
            'other-goal' => ['nullable','string','max:255'],
            'community'  => ['nullable','string','max:255'],
            'notes'      => ['nullable','string','max:5000'],
            'accept'     => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['accept' => $this->boolean('accept')]);
    }
    public function authorize(): bool
    {
        return true;
    }
}
