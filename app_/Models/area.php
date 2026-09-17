<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class area extends Model
{
    use HasFactory;
    use Userstamps;
    protected $table = "area";
    protected $fillable=['nom_area','ubicacion', 'telf_area', 'coord_area', 'id_divi',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                        ];
    public function Designacion()
    {              
        return $this->hasMany(designacion::Class,'id_area');  //indice de la tabla segudaria obligatorio
    }

    public function Division()
    {              
        return $this->belongsTo(division::class, 'id_divi'); //indice de la tabla segundaria obligatorio
    }

    public static function buscararea($id) {
      return area::find($id);
    }                        
}