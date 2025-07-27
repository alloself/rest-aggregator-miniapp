<?php

namespace App\Models\Traits;

use App\Models\File;
use App\Models\Pivot\Fileable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasImages
{
    use HasFiles;

    public function images(): MorphToMany
    {
        return $this->morphToMany(File::class, 'fileable')
            ->using(Fileable::class)
            ->wherePivot('type', 'image')
            ->withPivot('key', 'order');
    }

    public function syncImages(array $values): void
    {
        $mapped = collect($values)->mapWithKeys(function ($image) {
            return [
                $image['id'] => [
                    'key' => $image['pivot']['key'] ?? null,
                    'order' => $image['pivot']['order'] ?? 0,
                    'type' => 'image',
                ],
            ];
        });

        $this->images()->sync($mapped->toArray());
    }
}
