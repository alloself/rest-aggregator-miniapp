<?php

namespace App\Models;

use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use App\Models\Traits\HasImages;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends BaseModel
{
    use HasCRUD, HasList, HasSlug, HasImages, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'working_hours',
        'yandex_metrica_code',
        'address',
        'average_receipt',
        'user_id',
        'subtitle'
    ];


    protected $casts = [
        'order' => 'integer',
        'working_hours' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }






















    /**
     * Получить всех пользователей этого ресторана (включая владельца и сотрудников)
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Получить владельца ресторана
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить всех пользователей с их ролями в этом ресторане
     */
    public function getUsersWithRoles()
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();

        // Устанавливаем team_id этого ресторана
        setPermissionsTeamId($this->id);

        // Получаем всех пользователей с их ролями в этом ресторане
        $users = $this->users()->with(['roles' => function ($query) {
            $query->where('restaurant_id', $this->id);
        }])->get();

        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);

        return $users;
    }

    /**
     * Добавить пользователя с ролью в ресторан
     */
    public function addUser(User $user, string $role): void
    {
        // Добавляем связь многие-ко-многим если её нет
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id);
        }

        // Назначаем роль в контексте этого ресторана
        $user->assignRestaurantRole($this->id, $role);
    }

    /**
     * Удалить пользователя из ресторана
     */
    public function removeUser(User $user): void
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();

        // Устанавливаем team_id этого ресторана
        setPermissionsTeamId($this->id);

        // Удаляем все роли пользователя в этом ресторане
        $user->syncRoles([]);

        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);

        // Удаляем связь многие-ко-многим
        $this->users()->detach($user->id);
    }

    /**
     * Изменить роль пользователя в ресторане
     */
    public function changeUserRole(User $user, string $newRole): void
    {
        $user->assignRestaurantRole($this->id, $newRole);
    }
}
