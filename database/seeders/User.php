<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = array(
            array('name' => 'admin' ,'email' =>  'admin@admin.com','password' => Hash::make('admin123'), 'location_id' =>  1 , 'no_hp' =>  '08122212321'),
            array('name' => 'Hendra' ,'email' =>  'hendra@hendra.com','password' => Hash::make('hendra123'), 'location_id' =>  2, 'no_hp' =>  '08122212321'),
            array('name' => 'Rizki' ,'email' =>  'rizki@gmail.com','password' => Hash::make('rizki123'), 'location_id' =>  3, 'no_hp' =>  '08122212321'),
            array('name' => 'Andro' ,'email' =>  'andro@gmail.com','password' => Hash::make('andro123'), 'location_id' =>  4, 'no_hp' =>  '08122212321'),
            array('name' => 'Hensu' ,'email' =>  'hensu@gmail.com','password' => Hash::make('hensu123'), 'location_id' =>  5, 'no_hp' =>  '08122212321'),
        );
        DB::table('users')->insert($users);
    }
}
