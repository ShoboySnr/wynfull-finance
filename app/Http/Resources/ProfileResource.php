<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'first_name'         => $this->first_name,
            'last_name'          => $this->last_name,
            'full_name'          => $this->full_name,
            'professional_title' => $this->professional_title,
            'specialities'       => $this->specialities ?? [],
            'email'              => $this->email,
            'phone'              => $this->phone,
            'bio'                => $this->bio,
            'avatar_url'         => $this->avatar_url,
            'updated_at'         => $this->updated_at,
            'created_at'         => $this->created_at,
        ];
    }
}
