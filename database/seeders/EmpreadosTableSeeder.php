<?php

namespace Database\Seeders;

use App\Models\Empleados;
use Illuminate\Database\Seeder;

class EmpreadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Empleados::create(['PrimerNombre' =>'Luis', 'Apellidos' =>'Bartolo Ricsi', 'Correo' =>'bartoloricsi@gmail.com', 'Telefono' =>'991398554', 'IdCompanias' =>1]);
        Empleados::create(['PrimerNombre' =>'Santiago', 'Apellidos' =>'Yepez Garcia', 'Correo' =>'yepezgarcia@gmail.com', 'Telefono' =>'987654321', 'IdCompanias' =>2]);
    }
}
