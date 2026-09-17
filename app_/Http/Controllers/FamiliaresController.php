<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\familiares;
use App\Http\Controllers\Controller;

use App\Http\Requests\FamiliaresCreateRequest;
use App\Http\Requests\FamiliaresUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class FamiliaresController extends Controller
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
        $this->middleware('role_or_permission:10_Crear_funcionarios|10_Editar_funcionarios', ['only' => ['GuardarFamiliares','ActualizarFamiliares','EliminarFamiliares','BuscarFamiliares']]);

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
    public function store(Request $request)
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

    public function GuardarFamiliares(Request $request)
    {
        if ($request->ajax()) {
            try {
                $datarequest = new FamiliaresCreateRequest();

                $data = [
                'cedula'=>$request['cedula'],
                'nombre'=>strtoupper($request['nombre']),
                'apellido'=>strtoupper($request['apellido']),
                'fecha_nac'=>$request['fecha_nac'],
                'parentesco'=>$request['parentesco'],
                'id_func'=>$request['id_func']
                ];                              

                $validation = Validator::make($data, $datarequest->rules(), $datarequest->messages());  //se valida los datos y se recupera el mensaje de la validacion

                if ($validation->fails()) {
                    $errores = [];
                    $i = 0;
                    foreach ($validation->messages()->all() as $key) {                    
                        $errores[$i] = $key;
                        $i++;
                    }
                    return response()->json($errores);
                } else {
                    familiares::create($data);
                    return response()->json("OK");              
                }
            } catch (Exception $e) { 
                if (isset($e->errorInfo)) {
                    if ($e->errorInfo[1]=='1451') return response()->json(["0"=>"Imposible eliminar el registro relacionado a otros datos."]);
                    if ($e->errorInfo[1]=='1062') return response()->json(["0"=>"El registro ya existe."]);
                    if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') return response()->json(["0"=>"Operación invpalidad"]);
                } else {
                    return response()->json(["0"=>"Ocurrio un problema, no se pudo crear el registro"]);
                }
            }
        } else {
          return response()->json(["0"=>"Solicitud inválida"]);              
        }            
    }

    public function ActualizarFamiliares(Request $request)
    {
        if ($request->ajax()) {
          try {
            $datarequest = new FamiliaresUpdateRequest();            
            $id = $request['id'];

            $datarequest->setId($id); 

            $data = [
                'cedula'=>$request['cedula'],
                'nombre'=>strtoupper($request['nombre']),
                'apellido'=>strtoupper($request['apellido']),
                'fecha_nac'=>$request['fecha_nac'],
                'parentesco'=>$request['parentesco'],
            ];

            $validation = Validator::make($data, $datarequest->rules(), $datarequest->messages());  //se valida los datos y se recupera el mensaje de la validacion
            if ($validation->fails()) {
                $errores = [];
                $i = 0;
                foreach ($validation->messages()->all() as $key) {                    
                    $errores[$i] = $key;
                    $i++;
                }
                return response()->json($errores);
            } else {
                $afectados = DB::table('familiares')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'cedula'=>$data['cedula'],
                      'nombre'=>strtoupper($data['nombre']),
                      'apellido'=>strtoupper($data['apellido']),
                      'parentesco'=>$data['parentesco'],
                      'fecha_nac'=>$data['fecha_nac'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {                    
                    return response()->json("OK");              
                } else {
                    $objf = familiares::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        return response()->json("OK");              
                    } else {
                        return response()->json(["0"=>"Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, vuelva a realizar la operación para ver los datos nuevos"]);              
                    }
                }                
            }
        
          } catch (Exception $e) {    
            if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') return response()->json(["0"=>"Intenta escribir un valor que está registrado."]);
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') return response()->json(["0"=>"Operación inválida."]);
                return response()->json(["0"=>"Actualización no se pudo realizar."]);
            } else {
                return response()->json(["0"=>"Ocurrio un problema, no se pudo actualizar el registro"]);              
            }

            return response()->json(["0"=>"Solicitud inválida"]);              
          }
        } else {
          return response()->json(["0"=>"Solicitud inválida"]);              
        }            
    }

    public function EliminarFamiliares(Request $request)
    {
        if ($request->ajax()) {
          try {
            $id = $request['id'];

            $afectados =  familiares::destroy($id);                   
            if ($afectados > 0) 
            { 
              return response()->json("OK");
            }              
          } catch (Exception $e) {    
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') return response()->json(["0"=>"Imposible eliminar un registro asociado a otros datos."]); 
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') return response()->json(["0"=>"Operación inválida."]);
            } else {
              return response()->json(["0"=>"Ocurrio un problema, no se pudo eliminar el registro"]);              
            }

            return response()->json(["0"=>"Solicitud inválida"]);              
          }
        } else {
          return response()->json(["0"=>"Solicitud inválida"]);              
        }            
    }    

    public function BuscarFamiliares(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->id;          

            $objfamiliares = familiares::find($id);

            Session::put('dateconcurrente', $objfamiliares['updated_at']);
            Session::save();

            try {                              
                $result = familiares::buscarfamiliares($id);
                return response()->json($result);              
            } catch (Exception $e) {    
                return response()->json("Error");
            }
        } else {
          return response()->json("Error");              
        }            
    }     
}