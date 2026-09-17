<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Models\User;
use App\Models\funcionarios;
use App\Models\asistencia;
use App\Models\dependencia;
use App\Models\designacion;
use App\Models\division;
use App\Models\fechacarbon;
use App\Models\area;
use App\Http\Requests\PeriodoRequest;
use Validator;
use PDF;

use Redirect;
use DateTime;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class scan3BusquedaController extends Controller
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
        $this->middleware('role_or_permission:20_Generaqr', ['only' => ['index','RegistrarNuevoCarnet']]);
    }  

    public function index(Request $request)
    {    
        try {
          return view('nuevocarnet.index');
        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El registro ya existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo realiza la consulta');
             }
             return redirect('/');
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

    public function RegistrarNuevoCarnet(Request $request)
    {
        if ($request->ajax()) {
          $cedula = $request->cedula;          
          $nuevoqr = $request->nuevoqr;

          try {
            $obj = funcionarios::where('cedula','=',$cedula)->get()->first(); 

            if($obj!=null) {
              $obj = $obj->toArray();
              $afectados = DB::table('funcionarios')->where('id','=',$obj['id'])
              ->update(
                [
                    'nuevoqr'=>$nuevoqr,
                    'updated_at'=>DB::raw('NOW()')
                ]
              );
              return response()->json('Ok');            
            }else{
              return response()->json('Error');            
            }            
          } catch (Exception $e) {    
            return response()->json('Error');              
          }
        } else {
          return response()->json("Error");              
        }  
    }        
}
