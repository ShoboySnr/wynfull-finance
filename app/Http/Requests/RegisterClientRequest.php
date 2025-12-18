<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => ['required','string','max:255'],
            'phone'      => ['nullable','string','max:20'],
            'email'      => ['required','email','max:255','unique:users,email'],
            'password'   => ['required','string','min:8','confirmed'],
            'goal'       => ['nullable','string','max:255'],
            'other-goal' => ['nullable','string','max:255'],
            'community'  => ['nullable','string','max:255'],
            'notes'      => ['nullable','string','max:5000'],
            'accept'     => ['nullable','boolean'],
        ];
    }
    public function authorize(): bool
    {
        return true;
    }
}
