<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries=array(
            array('code'=>'US','name'=>'United States'),
            array('code'=>'CA','name'=>'Canada'),
            array('code'=>'AF','name'=>'Afghanistan'),
            array('code'=>'PK','name'=>'Pakistan'),
            array('code'=>'AL','name'=>'Albania'),
            array('code'=>'OZ','name'=>'Algeria'),
            array('code'=>'AD','name'=>'Andorra'),
            array('code'=>'ZR','name'=>'Zaire'),
            array('code'=>'ZM','name'=>'Zambia'),
            array('code'=>'ZW','name'=>'Zimbabwe'),
        );
        DB::table('countries')->insert($countries);
    }
}
