<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class dependencia extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "dependencia";
    protected $fillable=['nom_dep', 'ubicacion', 'telf_dep', 'id_jefedep', 'id_reg',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];

    //Método para crear una relación de pertenencia (Un jefedependencia pertenece a una region)
    public function Region()
    {            
        return $this->belongsTo(region::class, 'id_reg'); //indice de la tabla segundaria obligatorio
    }

    public function JefeDependencia()
    {            
        return $this->belongsTo(jefedependencia::class, 'id_jefedep'); //indice de la tabla segundaria obligatorio
    }

    public function Firmas()
    {              
        return $this->hasMany(firmas::Class,'id_dep');  //indice de la tabla segudaria obligatorio
    }

    public static function buscardependencia($id) {
      return dependencia::find($id);
    }


}