<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()
    ->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/
function givePermissions($resource, $permissions = null, $user = null): void
{
    $permissionsKeys = $permissions
        ?? ['view', 'create', 'update', 'delete', 'force_delete', 'restore', 'replicate', 'reorder'];

    $user = $user ?? auth()->user();

    foreach ($permissionsKeys as $permission) {
        $permissionSingle = Permission::create(['name' => "{$permission}_{$resource}"]);
        $permissionAny = Permission::create(['name' => "{$permission}_any_{$resource}"]);

        $user->givePermissionTo($permissionSingle, $permissionAny);
    }
}

function assignRole($role, $user = null): void
{
    $role = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);

    $user = $user ?? auth()->user();

    $user->assignRole($role);
}

function actingAsWithPermissions($resource, $permissions, $role = null)
{
    $user = User::factory()->create();
    test()->actingAs($user);

    givePermissions($resource, $permissions);

    if ($role) {
        assignRole($role, $user);
    }

    return $user;
}
