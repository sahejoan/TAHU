<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class familiares extends Model
{
    use HasFactory;
    protected $table = "familiares";
    protected $fillable=['cedula', 'nombre', 'apellido', 'fecha_nac', 'parentesco', 'id_func'];

    public static function buscarfamiliares($id) {
      return familiares::find($id);
    }

    public function Funcionarios()
    {              
        return $this->belongsTo(funcionarios::class, 'id_func'); //indice de la tabla segundaria obligatorio
    }


}