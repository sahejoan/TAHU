<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class funciones extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "funciones";
    protected $fillable=['id_cargo','id_lfuncion',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];

    public function Lfunciones()
    {            
        return $this->belongsTo(lfunciones::class, 'id_lfuncion'); //indice de la tabla segundaria obligatorio
    }

    public function CargoFuncionario()
    {              
        return $this->hasMany(cargofuncionario::Class,'id_cargo');  //indice de la tabla segudaria obligatorio
    }

}