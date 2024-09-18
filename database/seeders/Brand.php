<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class Brand extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $brands = array(
            array('name' => 'Indosat'),
            array('name' => 'Telkomsel'),
            array('name' => 'XL'),
            array('name' => 'AXIS'),
            array('name' => 'Smartfren'),
        );
        DB::table('brands')->insert($brands);
    }
}
