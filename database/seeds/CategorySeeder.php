<?php

use Illuminate\Database\Seeder;
use App\Models\Entities\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
          'id' => 1,
          'name' => 'Fotografia'
        ]);

        Category::create([
          'id' => 2,
          'name' => 'Computacion'
        ]);

        Category::create([
          'id' => 3,
          'name' => 'Ropa'
        ]);

        Category::create([
          'id' => 4,
          'name' => 'Libros'
        ]);
    }
}
