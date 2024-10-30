<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;



class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = array(
            array('name' => 'admin', 'email' =>  'admin@admin.com', 'password' => Hash::make('admin123')),
        );

        User::create(['name' => 'admin', 'email' =>  'admin@admin.com', 'password' => Hash::make('admin123')]);
    }
}
