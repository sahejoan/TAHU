<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\funcionarios;
use App\Models\fechacarbon;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\tipificaciones;

use App\Http\Requests\FuncionarioCreateRequest;
use App\Http\Requests\FuncionarioUpdateRequest;
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

class MonitorController extends Controller
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
        return view('funcionarios.show', array('objfechacarbon'=>$objfechacarbon));
    }

    public function getMonitorFuncionarios(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');


          // datatable column index  => database column name
          $columns = array(          
            0 => 'cedula',
            1 => 'apellido',
            2 => 'nombre',            
            3 => 'created_by',
            4 => 'updated_by',            
            5 => 'deleted_by',            
            6 => 'created_at',            
            7 => 'updated_at',
            8 => 'updated_at'            
          );

          $obj = funcionarios::query();

          //para jsindexversion1          
          $obj = funcionarios::select('funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.created_by','funcionarios.updated_by','funcionarios.deleted_by','funcionarios.created_at','funcionarios.updated_at')
          ->with(['creator', 'editor', 'destroyer']);

          //Cuenta registros
          $totalregistros = funcionarios::with(['creator', 'editor', 'destroyer'])
          ->orWhere('funcionarios.cedula','like','%'.$search.'%')
          ->orWhere('funcionarios.nombre','like','%'.$search.'%')
          ->orWhere('funcionarios.apellido','like','%'.$search.'%')
          ->count('funcionarios.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = funcionarios::with(['creator', 'editor', 'destroyer'])
            ->orWhere('funcionarios.cedula','like','%'.$search.'%')
            ->orWhere('funcionarios.nombre','like','%'.$search.'%')
            ->orWhere('funcionarios.apellido','like','%'.$search.'%')
            ->count(DB::raw('funcionarios.id'));
          }

          //Carga los datos
          $obj
          ->orWhere('funcionarios.cedula','like','%'.$search.'%')
          ->orWhere('funcionarios.nombre','like','%'.$search.'%')
          ->orWhere('funcionarios.apellido','like','%'.$search.'%');

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
            $order = 'funcionarios.cedula';
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
          $data = $objs;
          
          $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data
          );

          return response()->json($json_data);
        } catch (Exception $e) {            
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
