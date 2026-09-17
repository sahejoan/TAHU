<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Illuminate\Support\Facades\DB;
class asistencia2 extends Model
{
    use HasFactory;
    use Userstamps;

    protected $table = "asistencia";
    protected $fillable=['hora_e','hora_s', 
                         'hora_e2','hora_s2', 
                         'hora_e3','hora_s3', 
                         'hora_e4','hora_s4', 
                         'hora_e5','hora_s5', 
                         'hora_e6','hora_s6', 
                         'hora_e7','hora_s7', 
                         'hora_e8','hora_s8', 
                         'id_func',
                         'id_dep',
                         'observacion',
                         'o1','o2','o3','o4','o5','o6',
                         'o7','o8','o9','o10','o11','o12',
                         'o13','o14','o15','o16',                         
                         'created_at', 'updated_at',
                         'created_by', 'updated_by', 'deleted_by'
                         ];

}