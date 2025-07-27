<?php

namespace App\Models;

use App\Models\Traits\HasList;
use Illuminate\Support\Facades\Storage;
use App\Models\Pivot\Fileable;

class File extends BaseModel
{
    use HasList;

    protected $fillable = [
        'name',
        'url',
        'name',
        'extension',
    ];

    public function getUrlAttribute($value)
    {
        return asset(Storage::url($value));
    }

    public function fileables()
    {
        return $this->hasMany(Fileable::class);
    }

    function deleteEntity(): bool
    {
        $this->fileables()->delete();

        Storage::disk('public')->delete($this->getRawOriginal('url'));

        return $this->delete();
    }

    public static function createEntity(array $data, array $relations = []): self
    {
        $originalFile = $data['file'];
        $name = $originalFile->getClientOriginalName();
        $url = $originalFile->storeAs('files', uniqid() . "." . $originalFile->getClientOriginalExtension(), 'public');

        $entity = File::create([
            'url' => $url,
            'name' => $name,
            'extension' => $originalFile->getClientOriginalExtension(),
        ]);

        if (!empty($relations)) {
            $entity->load($relations);
        }

        return $entity;
    }
}
