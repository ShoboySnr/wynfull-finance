<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignCoachToClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'coach_id'  => ['required', 'integer', 'exists:users,id', 'different:client_id'],
            'client_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
