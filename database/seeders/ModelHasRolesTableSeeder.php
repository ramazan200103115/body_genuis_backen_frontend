<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModelHasRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::pluck('id', 'id')->all();
        $role = Role::where('id',1)->first();
        $role->syncPermissions($permissions);
        $user = User::where('id',1)->first();
        $user->assignRole([$role->id]);
    }
}
