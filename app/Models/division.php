<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class division extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "division";
    protected $fillable=['descripcion', 'id_jefedep','actual','ubicacion',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];

    public function Designacion()
    {              
        return $this->hasMany(designacion::Class,'id_divi');  //indice de la tabla segudaria obligatorio
    }

    public function Area()
    {              
        return $this->hasMany(area::Class,'id_divi');  //indice de la tabla segudaria obligatorio
    }

    public function JefeDependencia()
    {            
        return $this->belongsTo(jefedependencia::class, 'id_jefedep'); //indice de la tabla segundaria obligatorio
    }
}