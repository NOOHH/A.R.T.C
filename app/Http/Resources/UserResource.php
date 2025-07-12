<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $name = $this->user_firstname . ' ' . $this->user_lastname;
        $email = $this->email;
        $role = $this->role;
        
        // Use the User model's primary key
        $id = $this->user_id;

        return [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'is_online' => $this->is_online ?? false,
            'last_seen' => $this->last_seen,
            'avatar' => $this->avatar ?? null,
            'created_at' => $this->created_at,
        ];
    }
}
