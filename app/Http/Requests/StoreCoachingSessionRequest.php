<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoachingSessionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'coach_id'    => ['required','integer','exists:users,id'],
            'client_id'   => ['required','integer','exists:users,id','different:coach_id'],
            'title'       => ['nullable','string','max:120'],
            'type'        => ['nullable','string','max:60'],
            'location_url'=> ['nullable','string','max:255'],
            'notes'       => ['nullable','string','max:5000'],
            'starts_at'   => ['required','date'],
            'ends_at'     => ['required','date','after:starts_at'],
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
