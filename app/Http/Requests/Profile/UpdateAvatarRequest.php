<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar_url' => ['required', 'string', 'max:2048']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
