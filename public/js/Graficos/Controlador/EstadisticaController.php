<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\actividad;
use App\Modelsc\actividad_;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActividadCreateRequest;
use App\Http\Requests\ActividadUpdateRequest;
use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;
use App\Models\contribuyente;
use App\Models\division;
use App\Models\estado;
use App\Models\etiquetas;
use App\Models\parroquia;
use App\Models\dependencia;
use App\Models\ciudad;

 

 

class EstadisticaController extends Controller
{
    public function index()
    {
           
      

          $monthCounts =  contribuyente::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(1) as count'))
                ->groupBy('month')
                ->get()
                ->toArray();
       
       //   );
         $counts = array_fill(0, 12, 0);
         foreach($monthCounts as  $monthCount){
               $index = $monthCount['month']-1;
               $counts[$index] =  $monthCount['count'];

         }
          //  dd($counts);        

        $estados = estado::all();
        
        $censo =[];

            foreach($estados as $estado){
            $censo[] = ['name' => $estado['descripcion'], 'y' => ($estado['id'])];
          }          
           return view ('estadisticas/pie', ['data' => json_encode($censo)]);
                   
          
    }              
}

