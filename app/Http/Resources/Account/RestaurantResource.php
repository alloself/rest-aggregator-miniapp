<?php

namespace App\Http\Resources\Account;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'slug' => $this->slug,
            'description' => $this->description,
            'working_hours' => $this->working_hours,
            'address' => $this->address,
            'average_receipt' => $this->average_receipt,
            'yandex_metrica_code' => $this->yandex_metrica_code,
            'files' => FileResource::collection($this->whenLoaded('files')),
            'images' => FileResource::collection($this->whenLoaded('images')),
            'news' => NewsResource::collection($this->whenLoaded('news')),
            'events' => EventResource::collection($this->whenLoaded('events')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
