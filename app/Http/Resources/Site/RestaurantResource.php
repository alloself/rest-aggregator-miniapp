<?php

namespace App\Http\Resources\Site;

use App\Http\Resources\Account\CategoryResource;
use App\Http\Resources\Account\FileResource;
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
            'files' => FileResource::collection($this->whenLoaded('files')),
            'images' => FileResource::collection($this->whenLoaded('images')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
