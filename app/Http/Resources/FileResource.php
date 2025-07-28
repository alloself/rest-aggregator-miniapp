<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'original_name' => $this->original_name,
            'size' => $this->size,
            'extension' => $this->extension,
            'mime_type' => $this->mime_type,
            'path' => $this->path,
            'url' => $this->url,
        ];
    }
}
