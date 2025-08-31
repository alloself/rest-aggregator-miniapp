<?php

namespace App\Http\Resources\Account;

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
            'slug' => $this->slug,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'start_at' => $this->start_at,
            'price' => $this->price,
            'description' => $this->description,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'restaurant' => $this->whenLoaded('restaurant'),
            'is_sold_out' => $this->is_sold_out,
            'files' => FileResource::collection($this->whenLoaded('files')),
            'images' => FileResource::collection($this->whenLoaded('images')),
        ];
    }
}