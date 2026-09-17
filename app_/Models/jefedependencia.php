<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class jefedependencia extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "jefedependencia";
    protected $fillable=['nom_jefe','actual',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];

    //Método para crear una relación de pertenencia (Un jefedependencia pertenece a una region)
    public function Dependencia()
    {              
        return $this->hasMany(dependencia::Class,'id_jefedep');  //indice de la tabla segudaria obligatorio
    }

    public function Division()
    {              
        return $this->hasMany(division::Class,'id_jefedep');  //indice de la tabla segudaria obligatorio
    }

    public static function buscarjefedependencia($id) {
      return jefedependencia::find($id);
    }
}