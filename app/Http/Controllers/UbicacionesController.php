<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\asignacion;
use App\Models\area;
use App\Models\division;
use App\Models\dependencia;
use App\Models\funcionarios;
use App\Models\rpu;

use App\Http\Controllers\Controller;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class UbicacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $RegxPag = 1000;

    public function __construct()
    {
        $this->middleware('auth');
    }  

    public function index()
    {     
             
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaCreateRequest $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $input, $id)
    {
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
    }


    public function BuscarElArea(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            $id_divi = $request->id_divi;

            if ($b=='*') { $b = ''; }

            try { 
              $objarea = area::select(DB::raw('concat(area.nom_area," - ",area.ubicacion) as nom_area'),'area.id')
              ->leftjoin('division','area.id_divi','=','division.id')
              ->where('division.id','=',$id_divi)
              ->where(function($query) use ($b){
                $query->orwhereRaw('area.nom_area LIKE ?',['%'.$b.'%'])
                ->orwhereRaw('area.ubicacion LIKE ?',['%'.$b.'%']);
              })              
              ->orderBy('descripcion','asc')
              ->limit($this->RegxPag)->get()->toArray();

                if (count($objarea)>0) {                                                    
                    $objarea_ = ['nom_area'=>'Todos','id'=>'0'];
                    array_unshift($objarea,$objarea_);                    
                } else {
                    $objarea = ['0'=>['nom_area'=>'Todos','id'=>'0']];                  
                }

                return response()->json($objarea);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }      

    public function BuscarLaDependencia(Request $request) {
        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {

                $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - Región: ", region.nom_reg, " - Ubicación: ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
                ->leftjoin('region','dependencia.id_reg','=','region.id')
                ->whereRaw('dependencia.nom_dep LIKE ?',['%'.$b.'%'])
                ->orderBy('dependencia.nom_dep','asc')
                ->limit($this->RegxPag)->get()->toArray();

                if (count($objdependencia)>0) {                                                    
                    $objdependencia = ['0'=>['nombredependencia'=>'Todos','id'=>'0']]+$objdependencia;                  

                    $objdepedencia_ = ['nombredependencia'=>'Todos','id'=>'0'];
                    array_unshift($objdependencia,$objdependencia_);                    
                } else {
                    $objdependencia = ['0'=>['nombredependencia'=>'Todos','id'=>'0']];                  
                }

                return response()->json($objdependencia);                

            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json('Error');
        }
    }

    public function BuscarLaDivision(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            $id_dep = $request->id_dep;

            if ($b=='*') { $b = ''; }

            try {                  
                //->where('actual','=','1') codicion en modulo de area no mistraba todos los casos
                $objdivision = division::select(DB::raw('concat(division.descripcion," - ",dependencia.nom_dep," ",dependencia.ubicacion,IF(division.actual=0, " (Coordinador)", IF(division.actual=1, " (Jefatura)",""))) as descripcion'),'division.id')
                ->leftjoin('dependencia','division.ubicacion','=','dependencia.id')
                ->where('division.actual','<','2')
                ->where('division.ubicacion','=',$id_dep)
                ->where(function ($query) use ($b) {
                  $query->orWhere('division.descripcion', 'like', '%'.$b.'%')
                  ->orWhere('dependencia.ubicacion', 'like', '%'.$b.'%');
                  }
                )
                ->orderBy('descripcion','asc')
                ->limit($this->RegxPag)->get()->toArray();

                if (count($objdivision)>0) {                                                    
                    $objdivision_ = ['descripcion'=>'Todos','id'=>'0'];
                    array_unshift($objdivision,$objdivision_);                    
                } else {
                    $objdivision = ['0'=>['descripcion'=>'Todos','id'=>'0']];                  
                }

                return response()->json($objdivision);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }    
}