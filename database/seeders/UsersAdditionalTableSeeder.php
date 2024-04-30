<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersAdditionalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe Jr',
                'email' => 'john.doeJr@example.com',
                'age' => 22,
                'aboutMe' => 'I am John Doe Jr',
                'password' => bcrypt('secret'),
                'created_at' => date("Y-m-d h:i:s"),
                'updated_at' => date("Y-m-d h:i:s"),],
            [
                'name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'age' => 22,
                'aboutMe' => 'I am Jane Doe',
                'password' => bcrypt('secret'),
                'created_at' => date("Y-m-d h:i:s"),
                'updated_at' => date("Y-m-d h:i:s"),],

            [
                'name' => 'Mr Beast',
                'email' => 'beast@example.com',
                'age' => 22,
                'aboutMe' => 'I am Mr Beast',
                'password' => bcrypt('secret'),
                'created_at' => date("Y-m-d h:i:s"),
                'updated_at' => date("Y-m-d h:i:s"),],
            [
                'name' => 'Mr Kind',
                'email' => 'Kind@example.com',
                'age' => 22,
                'aboutMe' => 'I am Mr Kind',
                'password' => bcrypt('secret'),
                'created_at' => date("Y-m-d h:i:s"),
                'updated_at' => date("Y-m-d h:i:s"),],
            // Add more users as needed
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
        }
        $permissions = Permission::where('id', 4)->first();

        $admin = Role::where('id', 1)->first();
        $admin->syncPermissions($permissions);

        $teacher = Role::where('id', 2)->first();
        $teacher->syncPermissions($permissions);
        $teacher->syncPermissions(Permission::where('id', 3)->first());

        $user = User::where('id', 4)->first();
        $user->assignRole([$teacher->id]);

        $user = User::where('id', 5)->first();
        $user->assignRole([$teacher->id]);

        $role = Role::where('id', 3)->first();
        $role->syncPermissions($permissions);

        $user = User::where('id', 2)->first();
        $user->assignRole([$role->id]);

        $user = User::where('id', 3)->first();
        $user->assignRole([$role->id]);

    }
}
