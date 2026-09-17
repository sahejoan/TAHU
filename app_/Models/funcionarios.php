<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Illuminate\Support\Facades\DB;

class funcionarios extends Model
{
    use HasFactory;
    use Userstamps;

    protected $table = "funcionarios";
    protected $fillable=['cedula','rif', 'apellido', 'nombre',
                         'direccion', 'fecha_nac', 'sexo',
                         'telefono_p', 'telefono_cont',
                         'correo_alt', 'correo_i', 'twitter',
                         'fecha_in', 'condicion_in',
                         'tipo_s', 'alergia',
                         'ancho', 'alto', 'tipoimagen', 'foto',
                         'created_at', 'updated_at',
                         'creared_by', 'updated_by', 'deleted_by',
                         'id_dep'
                         ];

    public static function buscarfotofuncionario($id) {
      return funcionarios::find($id);
    }

    public function buscarfamiliares($id) {
      return familiares::where('id_func','=',$id)->get()->toArray();
    }

    public function imprimircargafamiliar($id) {
      return familiares::where('id_func','=',$id)->get();   
    }

    public function imprimirhijos12($id) {
        return familiares::where(DB::raw('TIMESTAMPDIFF(YEAR,fecha_nac,CURDATE())'),'<','13')->where('id_func','=',$id)->get();
    }

    public function ConDesignacionActiva($id) {
        $objdesignacion = designacion::select('estatus')->where('id_func','=',$id)->where('estatus','=','Activo')->get()->first();

        if ($objdesignacion!=null) {
            return true;
        } else {
            return false;
        }       
    }

    public function Familiares()
    {              
        return $this->hasMany(familiares::Class,'id_func');  //indice de la tabla segudaria obligatorio
    }

    public function Designaciones()
    {              
        return $this->hasMany(designacion::Class,'id_func');  //indice de la tabla segudaria obligatorio
    }

}
