<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
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
            'price' => $this->price,
            'image_path' => $this->image_path,
            'is_available' => $this->is_available,
            'allergens' => $this->allergens,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Отношения
            'menu' => new MenuResource($this->whenLoaded('menu')),
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            
            // Метрики
            'likes_count' => $this->whenAppended('likes_count'),
            'categories_count' => $this->whenCounted('categories'),
        ];
    }
}
