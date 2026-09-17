<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class region extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "region";
    protected $fillable=['nom_reg','direccion', 'telf_reg', 'coordenadas',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];

    //Método para crear una relación Uno a Muchos (Una region tiene varios jefedependencia)
    public function Dependencia()
    {              
        return $this->hasMany(dependencia::Class,'id_reg');  //indice de la tabla segudaria obligatorio
    }

    public static function buscarregion($id) {
      return region::find($id);
    }                        
}