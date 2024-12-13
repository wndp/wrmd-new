<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'subdivision' => $this->subdivision,
            'created_at_diff_for_humans' => $this->created_at_diff_for_humans,
            'profile_photo_url' => $this->profile_photo_url,
            'website' => $this->website,
        ];
    }
}
