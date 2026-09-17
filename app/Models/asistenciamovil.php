<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Illuminate\Support\Facades\DB;
class asistenciamovil extends Model
{
    use HasFactory;
    use Userstamps;

    protected $table = "asistenciamovil";
    protected $fillable=['id_func',
                         'id_dep',
                         'hora',
                         'observacion',
                         'enviado',
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                         ];

}