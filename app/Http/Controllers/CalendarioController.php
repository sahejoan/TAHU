<?php

namespace App\Http\Controllers; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;
use App\Models\funcionarios; 

class CalendarioController extends Controller{

     
    public function index()
    {
        //Para saber si cumpleaños durante el mes actual
        $today = Carbon::today();
        $month = $today->month;
        $year = $today->year;
        // $day  = $today->day;
        $funcionarios = DB::table('funcionarios')
        ->select(
            DB::raw('concat(year(now()),"-",IF(month(fecha_nac) >
            9,month(fecha_nac),concat("0",month(fecha_nac))), "-", IF(day
            (fecha_nac) >9, day(fecha_nac),
            concat("0",day(fecha_nac)))) as birthdate'),
            'funcionarios.id',
            'funcionarios.id_dep',
            'funcionarios.nombre as nom',
            'funcionarios.apellido as ape',
            'funcionarios.foto',
            'funcionarios.fecha_nac',
            'dependencia.nom_dep as dependencia', // Selecciona el nombre de la dependencia         
             
        )
        ->join('dependencia', 'funcionarios.id_dep', '=', 'dependencia.id') // Realiza el JOIN
        ->whereMonth('funcionarios.fecha_nac', $month)
        ->get();    

       // Mapear los funcionarios a eventos para FullCalendar
       $eventos = $funcionarios->map(function ($funcionario) {

        //Prestablecer etiqueta de la dependencia.
        if ($funcionario->id_dep == 1) {
            // Código para la opción 1
            $funcionario->dependencia='GERENCIA REGIONAL LOS LLANOS';
        } elseif ($funcionario->id_dep == 2) {
            // Código para la opción 2
            $funcionario->dependencia='SECTOR SAN JUAN DE LOS MORROS';
        } elseif ($funcionario->id_dep == 3) {
            // Código para la opción 3
            $funcionario->dependencia='SECTOR SAN FERNANDO DE APURE';
        } elseif ($funcionario->id_dep== 4) {
            // Código para la opción 4
            $funcionario->dependencia='UNIDAD ALTAGRACIA DE ORITUCO';
        } elseif ($funcionario->id_dep == 5) {
            // Código para la opción 5
            $funcionario->dependencia='SECTOR VALLE DE LA PASCUA';
        }  
         //Calculo de la edad del funcionario
       // $edad = Carbon::today()->diffInYears(Carbon::parse($funcionario->fecha_nac));
       // $text = 'AÑOS';
        // Concatenar nombre y apellido en una sola cadena
        
        $nombreCompleto = strtoupper($funcionario->nom . ' ' . $funcionario->ape);
        $dependencia =$funcionario->dependencia;
        $evento = $nombreCompleto.'<br>'.$dependencia;
        return [
            'title' => $evento,  
            'start' => $funcionario->birthdate,
            'end' => $funcionario->birthdate, // FullCalendar requiere 'end' aunque sea el mismo día
            'color' => '#007bff', // Color del evento (opcional)
            'className' => 'evento-titulo', // Clase CSS personalizada
            'extendedProps' => [
                'foto' => $funcionario->foto ? 'data:image/jpeg;base64,' . base64_encode($funcionario->foto) : null, // Foto en base64
            ],
        ];
    });
   // dd($eventos);
    return view('birthdate.calendario', compact('eventos', 'year'));
        
    }

    

        
}
