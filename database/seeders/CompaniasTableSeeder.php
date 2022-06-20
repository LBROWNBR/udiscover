<?php

namespace Database\Seeders;

use App\Models\Companias;
use Illuminate\Database\Seeder;

class CompaniasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Companias::create(['Nombre' =>'Udiscover', 'Correo' =>'admin@Udiscover.com', 'LogoPath' =>'foto01.jpg', 'WebPage' =>'www.udiscover.com']);
        Companias::create(['Nombre' =>'Palmer', 'Correo' =>'admin@Palmer.com', 'LogoPath' =>'foto02.jpg', 'WebPage' =>'www.palmer.com']);
    }
}
