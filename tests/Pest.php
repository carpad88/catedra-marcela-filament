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

use Spatie\Permission\Models\Permission;

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
