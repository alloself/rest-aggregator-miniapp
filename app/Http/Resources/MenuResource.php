<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Отношения
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'dishes' => DishResource::collection($this->whenLoaded('dishes')),
            
            // Метрики
            'dishes_count' => $this->whenCounted('dishes'),
        ];
    }
}
