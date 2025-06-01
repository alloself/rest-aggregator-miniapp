<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UuidCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_uses_uuid(): void
    {
        $user = User::factory()->create();
        
        $this->assertIsString($user->id);
        $this->assertTrue($this->isValidUuid($user->id));
    }

    public function test_permission_model_uses_uuid(): void
    {
        $permission = Permission::create([
            'name' => 'test-permission',
            'guard_name' => 'web',
        ]);
        
        $this->assertIsString($permission->id);
        $this->assertTrue($this->isValidUuid($permission->id));
    }

    public function test_role_model_uses_uuid(): void
    {
        $role = Role::create([
            'name' => 'test-role',
            'guard_name' => 'web',
        ]);
        
        $this->assertIsString($role->id);
        $this->assertTrue($this->isValidUuid($role->id));
    }

    public function test_user_role_assignment_works_with_uuid(): void
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);
        
        $this->assertTrue($user->hasRole('admin'));
        $this->assertCount(1, $user->roles);
    }

    public function test_user_permission_assignment_works_with_uuid(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create([
            'name' => 'edit-posts',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo($permission);
        
        $this->assertTrue($user->hasPermissionTo('edit-posts'));
        $this->assertCount(1, $user->permissions);
    }

    public function test_user_soft_deletes(): void
    {
        $user = User::factory()->create([
            'name' => 'Soft Delete User',
            'email' => 'softdelete@test.com',
        ]);

        $userId = $user->id;

        // Soft delete пользователя
        $user->delete();

        // Проверяем что пользователь не найден в обычном поиске
        $this->assertNull(User::find($userId));

        // Но найден в withTrashed()
        $trashedUser = User::withTrashed()->find($userId);
        $this->assertNotNull($trashedUser);
        $this->assertNotNull($trashedUser->deleted_at);
        $this->assertTrue($trashedUser->trashed());

        // Можем восстановить
        $trashedUser->restore();
        $restoredUser = User::find($userId);
        $this->assertNotNull($restoredUser);
        $this->assertNull($restoredUser->deleted_at);
    }

    /**
     * Check if string is valid UUID (v4 or v7)
     */
    private function isValidUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[47][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid) === 1;
    }
} 