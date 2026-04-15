<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketRemarkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Ensure the user relationship is loaded to avoid extra queries
        $this->resource->loadMissing('user');

        return [
            'id' => $this->id,
            'comment' => $this->comment,
            // Include user's name if the relationship exists and is loaded
            'user_name' => $this->whenLoaded('user', function () {
                return $this->user ? $this->user->name : 'System'; // Fallback name
            }),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
