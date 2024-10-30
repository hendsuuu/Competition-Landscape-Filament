<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = array(
            // array('name' => 'admin', 'email' =>  'admin@admin.com', 'password' => Hash::make('admin123')),
            array('name' => 'Hendra', 'email' =>  'hendra@hendra.com', 'password' => Hash::make('hendra123')),
            array('name' => 'Rizki', 'email' =>  'rizki@gmail.com', 'password' => Hash::make('rizki123')),
            array('name' => 'Andro', 'email' =>  'andro@gmail.com', 'password' => Hash::make('andro123')),
            array('name' => 'Hensu', 'email' =>  'hensu@gmail.com', 'password' => Hash::make('hensu123')),
        );

        // $adminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $salesRole = Role::firstOrCreate(['name' => 'sales_user']);

        foreach ($users as $user) {
            $newUserId = DB::table('users')->insertGetId($user);
            $newUser   = User::find($newUserId);

            // if ($user['name'] !== 'admin') {
            $newUser->assignRole($salesRole);
            // } else {
            //     $newUser->assignRole($adminRole);
            // }
        }
    }
}
