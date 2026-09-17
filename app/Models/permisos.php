<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permisos extends Model
{
    use HasFactory;
    protected $table = "permissions";
    protected $fillable=['name', 'guard_name',
                         'created_at', 'updated_at'
                        ];

    public static function buscarpermiso($id) {
      return permisos::find($id);
    }                        
}