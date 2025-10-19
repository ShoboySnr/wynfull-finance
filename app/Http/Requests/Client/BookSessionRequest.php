<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class BookSessionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'coach_id'    => ['required','integer','exists:users,id'],
            'date'        => ['required','date_format:Y-m-d'],
            'start_time'  => ['required','date_format:H:i'], // in coach tz
            'title'       => ['nullable','string','max:120'],
            'type'        => ['nullable','string','max:50'],
            'notes'       => ['nullable','string','max:1000'],
            'location_url'=> ['nullable','url','max:255'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
