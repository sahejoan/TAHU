<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\area;
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

class MonitorAreaController extends Controller
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

        return view('area.show', array('objfechacarbon'=>$objfechacarbon));
    }

    public function getMonitorArea(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');


          // datatable column index  => database column name
          $columns = array(          
            0 => 'nom_area',
            1 => 'created_by',
            2 => 'updated_by',            
            3 => 'deleted_by',            
            4 => 'created_at',            
            5 => 'updated_at',
            6 => 'updated_at'            
          );

          $obj = area::query();

          //para jsindexversion1          
          $obj = area::with(['creator', 'editor', 'destroyer']);

          //Cuenta registros
          $totalregistros = area::with(['creator', 'editor', 'destroyer'])
          ->orWhere('area.nom_area','like','%'.$search.'%')
          ->count('area.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = area::with(['creator', 'editor', 'destroyer'])
            ->orWhere('area.nom_area','like','%'.$search.'%')
            ->count(DB::raw('area.id'));
          }

          //Carga los datos
          $obj
          ->orWhere('area.nom_area','like','%'.$search.'%');

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
            $order = 'area.nom_area';
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
