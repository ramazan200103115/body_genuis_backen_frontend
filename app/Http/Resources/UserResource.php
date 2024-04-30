<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'urlAvatar' => $this->urlAvatar,
            'name' => $this->name,
            'age' => $this->age,
            'medcoins' => $this->medcoins,
            'email' => $this->email,
            'aboutMe' => $this->aboutMe,
        ];    }
}
