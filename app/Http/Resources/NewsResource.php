<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Restaurant;

class NewsResource extends JsonResource
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
            'content' => $this->content,
            'image_path' => $this->image_path,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Polymorphic автор
            'author' => $this->getAuthorResource(),
            'author_type' => $this->getAuthorType(),
            
            // Метрики
            'likes_count' => $this->whenAppended('likes_count'),
        ];
    }
    
    /**
     * Get the appropriate author resource.
     */
    private function getAuthorResource()
    {
        if (!$this->relationLoaded('author')) {
            return null;
        }
        
        return match ($this->author_type) {
            Restaurant::class => new RestaurantResource($this->author),
            default => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'type' => 'admin'
            ]
        };
    }
    
    /**
     * Get simplified author type.
     */
    private function getAuthorType(): string
    {
        return match ($this->author_type) {
            Restaurant::class => 'restaurant',
            default => 'admin'
        };
    }
}
