<?php

namespace Database\Seeders;

use App\Models\App_usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class App_usuariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's clear the app_usuarios table first
        App_usuario::truncate();

        $faker = \Faker\Factory::create();

        // Let's make sure everyone has the same password and
        // let's hash it before the loop, or else our seeder
        // will be too slow.
        $password = Hash::make('password');

        App_usuario::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => $password,
            'id_estado' => 1,
        ]);

        // And now let's generate a few dozen app_usuarios for our app:
        for ($i = 0; $i < 10; $i++) {
            App_usuario::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => $password,
                'id_estado' => 1,
            ]);
        }
    }
}
