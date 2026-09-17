<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\designacion;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\tipificaciones;
use App\Models\fechacarbon;

use App\Http\Requests\AreaCreateRequest;
use App\Http\Requests\AreaUpdateRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

//use Redirect;
use DateTime;
//use Validator;
//use Session;
use Exception;
use ValidateException;
use ConvertirImagen; 
//use Auth;
use Illuminate\Routing\Route;

class MonitorDesignacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role_or_permission:13_Monitorear', ['only' => ['index']]);
    }  

    public function index()
    {                  
      $objfechacarbon = new fechacarbon();
      return view('designacion.show', array('objfechacarbon'=>$objfechacarbon));
    }

    public function getMonitorDesignacion(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          // datatable column index  => database column name
          $columns = array(          
            0 => 'numdesig',
            1 => 'fecha_desig',
            2 => 'fecha_cese',            
            3 => 'apellido',            
            4 => 'nombre', 
            5 => 'nom_dep',                        
            6 => 'nom_reg',            
            7 => 'ubicacion',            
            8 => 'nom_area',            
            9 => 'created_by',
            10 => 'updated_by',            
            11 => 'deleted_by',            
            12 => 'created_at',            
            13 => 'updated_at',
            14 => 'updated_at'            
          );

          $obj = designacion::query();

          //para jsindexversion1          
          $obj->whereHas('funcionarios',function ($query) use ($search) {
            $query->select('funcionarios.id','funcionarios.apellido','funcionarios.nombre')->where(function($query) use ($search) {                  
              $query
              ->orWhere('funcionarios.nombre','like','%'.$search.'%')
              ->orWhere('funcionarios.apellido','like','%'.$search.'%');
            });
          })          
          ->with(['creator', 'editor', 'destroyer','area',
            'dependencia'=>function ($query) {
                $query->with('region');
            },
            'funcionarios'=>function ($query) use ($search) {
              $query->select('funcionarios.id','funcionarios.apellido','funcionarios.nombre')->where(function($query) use ($search) {                                    
                $query
                ->orWhere('funcionarios.nombre','like','%'.$search.'%')
                ->orWhere('funcionarios.apellido','like','%'.$search.'%');
              });
            }
          ]);

          //Cuenta registros
          $totalregistros = designacion::whereHas('funcionarios',function ($query) use ($search) {
                $query->select('funcionarios.id','funcionarios.apellido','funcionarios.nombre')
                ->where(function($query) use ($search) {                  
                  $query->orWhere('funcionarios.nombre','like','%'.$search.'%')
                  ->orWhere('funcionarios.apellido','like','%'.$search.'%');
                });
           })          
           ->with(['creator', 'editor', 'destroyer','area',
            'dependencia'=>function ($query) {
                $query->with('region');
            },
            'funcionarios'=>function ($query) use ($search) {
                $query->select('funcionarios.id','funcionarios.apellido','funcionarios.nombre')
                ->where(function($query) use ($search) {                                    
                  $query
                  ->orWhere('funcionarios.nombre','like','%'.$search.'%')
                  ->orWhere('funcionarios.apellido','like','%'.$search.'%');
                });
            }])
          ->orWhere('designacion.numdesig','like','%'.$search.'%')          
          ->count('designacion.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = designacion::whereHas('funcionarios',function ($query) use ($search) {
                $query
                ->where(function($query) use ($search) {                  
                  $query->orWhere('funcionarios.nombre','like','%'.$search.'%')
                  ->orWhere('funcionarios.apellido','like','%'.$search.'%');
                });
           })          
           ->with(['creator', 'editor', 'destroyer','area',
            'dependencia'=>function ($query) {
                $query->with('region');
            },
            'funcionarios'=>function ($query) use ($search) {
                $query->select('funcionarios.id','funcionarios.apellido','funcionarios.nombre')
                ->where(function($query) use ($search) {                                    
                  $query
                  ->orWhere('funcionarios.nombre','like','%'.$search.'%')
                  ->orWhere('funcionarios.apellido','like','%'.$search.'%');
                });
            }])
            ->orWhere('designacion.numdesig','like','%'.$search.'%')
            ->count(DB::raw('designacion.id'));
          }

          //Carga los datos
          $obj->orWhere('designacion.numdesig','like','%'.$search.'%');

          if (empty($request->input('length'))) {
            $limit = 10;
          }else{
            if ($request->input('length')=='-1') {
              $limit = $totalregistros;
            } else {
              $limit = $request->input('length');         
            }            
          }

          if (empty($request->input('start'))) {
            $start = 0;
          }else{
            $start = $request->input('start');
          }

          if (empty($request->input('order.0.column'))) {
            $order = 'designacion.numdesig';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $obj->offset($start);
          $obj->limit($limit);
          $obj->orderBy($order, $dir);

          $objs = $obj->get()->toArray();

          $data = array();         
          
          //para jsindexversion1          
          $data = mb_convert_encoding($objs, 'UTF-8', 'UTF-8');
          
          $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data
          );

          return response()->json($json_data);
        } catch (Exception $e) {            
          return response()->json($e->getMessage());
          $data = [];
          $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data
          );

          return response()->json($json_data);
        }

        }
    } 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
