<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Entities\User;
use App\Models\Entities\Configuration;
use App\Models\Entities\UserImage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // default user
        $defaultUser = User::create([
            'username' => 'tom',
            'name' => 'Tom',
            'lastname' => 'Riddle',
            'email' => 'imlordvoldemort@shadow.uk',
            'type' => 'user',
            'genre' => 'male',
            'active' => true,
            'password' => 123,
            'slug' => '',
            'city_id' => $faker->randomElement([1,2,3,4])
        ]);

        Configuration::create([
            'notificacion' => true,
            'deal' => true,
            'user_id' => $defaultUser->id
        ]);

        UserImage::create([
            'path' => 'assets/img/avatar.gif',
            'thumb_path' => 'assets/img/avatar.gif',
            'user_id' => $defaultUser->id
        ]);

        // anothers users...
        foreach(range(1, 50) as $index)
        {
            $user = User::create([
                'username' => $faker->username,
                'name' => $faker->name,
                'lastname' => $faker->lastname,
                'email' => $faker->email,
                'type' => 'user',
                'genre' => $faker->randomElement(['male','female']),
                'active' => true,
                'password' => 123, // \Hash::make(123),
                'slug' => '',
                'city_id' => $faker->randomElement([1,2,3,4])
            ]);

            Configuration::create([
              'notificacion' => true,
              'deal' => true,
              'user_id' => $user->id
            ]);

            UserImage::create([
              'path' => 'assets/img/avatar.gif',
              'thumb_path' => 'assets/img/avatar.gif',
              'user_id' => $user->id
            ]);
        }
    }
}
