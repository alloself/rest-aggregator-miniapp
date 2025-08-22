<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $childrenCollection = collect();

        if ($this->relationLoaded('children')) {
            $childrenCollection = $this->children;
        } elseif ($this->relationLoaded('descendants')) {
            $childrenCollection = $this->descendants->toTree();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'order' => $this->order,
            'children' => CategoryResource::collection($childrenCollection),
        ];
    }
}
