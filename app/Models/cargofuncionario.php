<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class cargofuncionario extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "cargo";
    protected $fillable=['id_lcargo','id_area', 'id_desig', 'fecha_ex', 'fecha_inic', 'fecha_culm', 'sta_cargo', 'sta_contrato', 'observaciobes',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];

    public static function buscarcargofuncionario($id) {
      return cargofuncionario::find($id);
    }               

    public function Funciones()
    {            
        return $this->belongsTo(funciones::class, 'id_cargo'); //indice de la tabla segundaria obligatorio
    }

}