<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Отношения загружаются только при необходимости
            'menus' => MenuResource::collection($this->whenLoaded('menus')),
            'events' => EventResource::collection($this->whenLoaded('events')),
            'news' => NewsResource::collection($this->whenLoaded('news')),
            
            // Метрики
            'menus_count' => $this->whenCounted('menus'),
            'events_count' => $this->whenCounted('events'),
            'dishes_count' => $this->whenAppended('dishes_count'),
        ];
    }
}
