<?php

use Illuminate\Database\Seeder;
use App\Models\Entities\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'id' => 1,
            'name' => 'Chile',
            'code' => 'CL'
        ]);

        Country::create([
            'id' => 2,
            'name' => 'Argentina',
            'code' => 'AR'
        ]);

        Country::create([
            'id' => 3,
            'name' => 'Peru',
            'code' => 'PER'
        ]);

        Country::create([
            'id' => 4,
            'name' => 'Colombia',
            'code' => 'COL'
        ]);
    }
}
