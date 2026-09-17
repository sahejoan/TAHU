<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class firmas extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "firmas";
    protected $fillable=['firma','ubicacion', 'actual', 'id_dep',
                         'provi_jefe','fecha_provi','gaceta_provi','fecha_gaceta',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];
    public function Designacion()
    {              
        return $this->hasMany(designacion::Class,'id_firma');  //indice de la tabla segudaria obligatorio
    }

    public function Dependencia()
    {            
        return $this->belongsTo(dependencia::class, 'id_dep'); //indice de la tabla segundaria obligatorio
    }


    public static function buscarfirma($id) {
      return firma::find($id);
    }                        
}