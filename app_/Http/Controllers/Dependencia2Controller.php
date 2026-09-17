<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\dependencia;
use App\Http\Controllers\Controller;

use App\Http\Requests\DependenciaCreateRequest;
use App\Http\Requests\DependenciaUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class DependenciaController extends Controller
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
        try{

          $objdependencia = dependencia::limit($this->RegxPag)->get();
          return view('dependencia.index', array('objdependencia'=>$objdependencia, 'busqueda'=>''));

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message', 'El registro ya existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message', 'Error['.$e->errorInfo[1].'] Operación inválida.');
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
        return view('dependencia.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DependenciaCreateRequest $request)
    {
        try{
            $data = $request->all();

            dependencia::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/dependencia');

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message','Ocurrio un problema, no se pudo crear el registro');
             }

             return redirect()->back()->withInput($data);                            
        }
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
        try {                        
            $objdependencia = dependencia::find($id);
    
            Session::put('dateconcurrente', $objdependencia['updated_at']);
            Session::save();

            return view('dependencia.editar',compact('objdependencia'));

        } catch (Exception $e) {

            Session::flash('message','Solicitud invalida');               
            return redirect('/dependencia');

        }
        
        return redirect('/dependencia');        
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
        try {
            $request = new DependenciaUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $objdependencia = dependencia::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {

                $afectados = DB::table('dependencia')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'nom_cargo'=>$data['nom_cargo'],
                      'ubicacion'=>$data['ubicacion'],
                      'telf_area'=>$data['telf_area'],
                      'coord_area'=>$data['coord_area'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/dependencia');
                } else {
                    $objf = dependencia::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/dependencia');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/dependencia');
                    }
                }

            }

        } catch (Exception $e) {    
            if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') Session::flash('message', 'Intenta escribir un valor que está registrado.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
                Session::flash('message','Ocurrio un problema, no se pudo actualizar el registro');
            }

            return redirect('/dependencia');
        }            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $afectados =  dependencia::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }        
            return redirect('/dependencia');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/dependencia');        
        }       
    }

    public function VerDependencia(Request $request) {
        if ($request->ajax()) {
          $id = $request->id;          

          try {                  
            $result = dependencia::buscardependencia($id);
            return response()->json($result);              
          } catch (Exception $e) {    
            return response()->json($e);              
          }
        } else {
          return response()->json("Error");              
        } 
    }

    public function BuscarDependencia(Request $request) {
        try{

          $busqueda = $request->busqueda;
          $objdependencia = dependencia::where('nom_dep', 'like', '%'. $busqueda . '%')
            ->orderBy('nom_reg','asc')
            ->limit($this->RegxPag)->get();

          return view('dependencia.index', array('objdependencia'=>$objdependencia, 'busqueda'=>$busqueda));                           

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message', 'El registro ya existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo realizar la consulta');
             }
             return redirect('/');
        }         
      }    
}