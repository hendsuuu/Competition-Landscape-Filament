<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Location extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $locations = array(
            array('name' => 'Jawa Tengah'),
            array('name' => 'Jawa Timur '),
            array('name' => 'Bali'),
            array('name' => 'Nusa Tenggara Barat'),
            array('name' => 'Nusa Tenggara Timur'),
            array('name' => 'DI Yogyakarta'),
        );
        DB::table('locations')->insert($locations);
    }
}
