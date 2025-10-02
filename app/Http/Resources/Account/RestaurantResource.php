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
            'bot_username' => $this->bot_username,
            // SEO
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'seo_robots' => $this->seo_robots,
            'seo_canonical_url' => $this->seo_canonical_url,
            // Open Graph
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image_url' => $this->og_image_url,
            'files' => FileResource::collection($this->whenLoaded('files')),
            'images' => FileResource::collection($this->whenLoaded('images')),
            'news' => NewsResource::collection($this->whenLoaded('news')),
            'events' => EventResource::collection($this->whenLoaded('events')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'telegram_bot_token' => $this->telegram_bot_token,
        ];
    }
}
