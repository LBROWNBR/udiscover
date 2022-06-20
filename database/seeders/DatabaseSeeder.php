<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // This way, we can simply run
        // $ php artisan db:seed and it will run all the called classes:
        $this->call(ArticlesTableSeeder::class);
        $this->call(App_usuariosTableSeeder::class);
        $this->call(CompaniasTableSeeder::class);
        $this->call(EmpreadosTableSeeder::class);
    }
}
