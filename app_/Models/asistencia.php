<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class asistencia extends Model
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
                         'created_at', 'updated_at',
                         'creared_by', 'updated_by', 'deleted_by'
                         ];
    public function funcionarios() {
        return $this->belongsTo(funcionarios::class, 'id_func'); //indice de la tabla segundaria obligatorio
    }

    public function entrada($id, $fecha) {
    	$f = new Carbon($fecha);
    	$comparar = $f->format('d-m-Y');    	
        $objasistencia = DB::select(DB::raw("SELECT asistencia.id, asistencia.hora_e, asistencia.hora_s, asistencia.hora_e2, asistencia.hora_s2, 
            asistencia.hora_e3, asistencia.hora_s3, 
            asistencia.hora_e4, asistencia.hora_s4, 
            asistencia.hora_e5, asistencia.hora_s5, 
            asistencia.hora_e6, asistencia.hora_s6, 
            asistencia.hora_e7, asistencia.hora_s7, 
            asistencia.hora_e8, asistencia.hora_s8 FROM asistencia WHERE asistencia.id_func = '$id' AND DATE_FORMAT(asistencia.hora_e,'%d-%m-%Y') = '$comparar'"));
        return $objasistencia;
    }

    public function salida($id, $fecha) {
    	$f = new Carbon($fecha);
    	$comparar = $f->format('d-m-Y');    	
        $objasistencia = DB::select(DB::raw("SELECT asistencia.id, asistencia.hora_e, asistencia.hora_s, asistencia.hora_e2, asistencia.hora_s2, 
            asistencia.hora_e3, asistencia.hora_s3, 
            asistencia.hora_e4, asistencia.hora_s4, 
            asistencia.hora_e5, asistencia.hora_s5, 
            asistencia.hora_e6, asistencia.hora_s6, 
            asistencia.hora_e7, asistencia.hora_s7, 
            asistencia.hora_e8, asistencia.hora_s8 FROM asistencia WHERE asistencia.id_func = '$id' AND DATE_FORMAT(asistencia.hora_e,'%d-%m-%Y') = '$comparar'"));
        return $objasistencia;
    }    

}