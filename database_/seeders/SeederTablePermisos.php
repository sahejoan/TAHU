<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//Spatie
use Spatie\Permission\Models\Permission;

class SeederTablePermisos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permisos = [
         //tabla roles
          'ver-rol',
          'crear-rol',
          'editar-rol',
          'borrar-rol',

          //blogs
          'ver-blog',
          'crear-blog',
          'editar-blog',
          'borrar-blog',
        ];
        foreach($permisos as $permiso){
          Permission::create(['name'=>$permiso]);

        }
              

        
        

        
    }
}
