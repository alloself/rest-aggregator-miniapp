<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes, HasApiTokens;

    /**
     * Custom interfaces for ModelTyper - исключаем нежелательные поля
     *
     * @var array
     */
    public array $interfaces = [
        'tokens' => [
            'type' => 'never',
        ],
        'notifications' => [
            'type' => 'never',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'tokens',           // Скрываем Sanctum tokens от ModelTyper
        'notifications',    // Скрываем notifications от ModelTyper
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Получить рестораны где пользователь имеет роли
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class);
    }

    /**
     * Получить роли пользователя в конкретном ресторане
     */
    public function getRestaurantRoles(string $restaurantId): array
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();
        
        // Устанавливаем team_id ресторана
        setPermissionsTeamId($restaurantId);
        
        // Получаем роли
        $roles = $this->getRoleNames()->toArray();
        
        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);
        
        return $roles;
    }

    /**
     * Проверить есть ли у пользователя роль в ресторане
     */
    public function hasRestaurantRole(string $restaurantId, string $role): bool
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();
        
        // Устанавливаем team_id ресторана
        setPermissionsTeamId($restaurantId);
        
        // Проверяем роль
        $hasRole = $this->hasRole($role);
        
        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);
        
        return $hasRole;
    }

    /**
     * Назначить роль пользователю в ресторане
     */
    public function assignRestaurantRole(string $restaurantId, string $role): self
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();
        
        // Устанавливаем team_id ресторана
        setPermissionsTeamId($restaurantId);
        
        // Назначаем роль
        $this->assignRole($role);
        
        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);
        
        return $this;
    }
}
