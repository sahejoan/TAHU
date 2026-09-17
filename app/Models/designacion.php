<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class designacion extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "designacion";
    protected $fillable=['numdesig', 'fecha_desig', 'fecha_cese', 'estatus', 'id_func', 'id_dep', 'id_jefedep', 'id_area', 'id_divi', 'id_jefediv', 'id_jefeth', 'id_coorth', 'id_firma',
                        'provi_jefe','fecha_provi','gaceta_provi','fecha_gaceta',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];

    public static function buscardesignacion($id) {
      return designacion::find($id);
    }

    public function Funcionarios()
    {            
        return $this->belongsTo(funcionarios::class, 'id_func'); //indice de la tabla segundaria obligatorio
    }

    public function Dependencia()
    {            
        return $this->belongsTo(dependencia::class, 'id_dep'); //indice de la tabla segundaria obligatorio
    }

    public function Area()
    {            
        return $this->belongsTo(area::class, 'id_area'); //indice de la tabla segundaria obligatorio
    }

    public function Division()
    {            
        return $this->belongsTo(division::class, 'id_divi'); //indice de la tabla segundaria obligatorio
    }

    public function Firmas()
    {            
        return $this->belongsTo(firmas::class, 'id_firma'); //indice de la tabla segundaria obligatorio
    }

    public function JefeTH()
    {            
        return $this->belongsTo(jefedependencia::class, 'id_jefeth'); //indice de la tabla segundaria obligatorio
    }

    //Este método apunta al índice del usuario que carga la designación
    public function CoordinadorTH()
    {            
        return $this->belongsTo(User::class, 'id_coorth'); //indice de la tabla segundaria obligatorio
    }

    public function JefeDependencia()
    {            
        return $this->belongsTo(jefedependencia::class, 'id_jefedep'); //indice de la tabla segundaria obligatorio
    }


    public function JefeDivision()
    {            
        return $this->belongsTo(jefedependencia::class, 'id_jefediv'); //indice de la tabla segundaria obligatorio
    }

    public function DesignacionExistente($id_func)
    {            
        $obj = designacion::select('id')->where('id_func','=',$id_func)->where('estatus','=','Activo')->get()->toArray();
        if (count($obj)>0) {
            return true;
        } else {
            return false;
        }        
    }

    public static function DesignacionActualizable($id_func, $id)
    {            
        $obj = designacion::select('id')->where('id_func','=',$id_func)->where('id','<>',$id)->where('estatus','=','Activo')->get()->toArray();
        if (count($obj)>0) {
            return false;
        } else {
            return true;
        }        
    }

}