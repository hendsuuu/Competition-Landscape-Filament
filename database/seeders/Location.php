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
            array('name' => 'Semarang'),
            array('name' => 'Jakarta'),
            array('name' => 'Bandung'),
            array('name' => 'Yogyakarta'),
            array('name' => 'Surabaya'),
        );
        DB::table('locations')->insert($locations);
    }
}
