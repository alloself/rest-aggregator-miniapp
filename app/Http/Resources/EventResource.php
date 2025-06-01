<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'max_participants' => $this->max_participants,
            'price' => $this->price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Отношения
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            
            // Computed properties
            'is_active' => $this->when(isset($this->is_active), $this->is_active),
            'duration_hours' => $this->when(
                $this->start_date && $this->end_date,
                $this->start_date->diffInHours($this->end_date)
            ),
            
            // Метрики
            'likes_count' => $this->whenAppended('likes_count'),
        ];
    }
}
