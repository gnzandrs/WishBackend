<?php

use Illuminate\Database\Seeder;
use App\Models\Entities\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::create([
            'id' => 1,
            'name' => 'Santiago',
            'code' => '02',
            'country_id' => 1
        ]);

        City::create([
            'id' => 2,
            'name' => 'Mendoza',
            'code' => '03',
            'country_id' => 2
        ]);

        City::create([
            'id' => 3,
            'name' => 'Lima',
            'code' => '03',
            'country_id' => 3
        ]);

        City::create([
            'id' => 4,
            'name' => 'Bogota',
            'code' => '04',
            'country_id' => 4
        ]);
    }
}
