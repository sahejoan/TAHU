<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\lfunciones;
use App\Http\Controllers\Controller;

use App\Http\Requests\LfuncionesCreateRequest;
use App\Http\Requests\LfuncionesUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class LfuncionesController extends Controller
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

        $this->middleware('role_or_permission:7_Buscar_funciones|7_Ver_funciones|7_Crear_cago|7_Editar_funciones|7_Borrar_funciones', ['only' => ['index']]);
        $this->middleware('role_or_permission:7_Funciones', ['only' => ['index']]);
        $this->middleware('role_or_permission:7_Buscar_funciones', ['only' => ['BuscarLfunciones','getLfunciones']]);
        $this->middleware('role_or_permission:7_Crear_funciones', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:7_Editar_funciones', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:7_Borrar_funciones', ['only' => ['destroy']]);                        

    }  

    public function index()
    {   
        try{

          $objlfunciones = lfunciones::orderBy('descripcion','asc')->limit($this->RegxPag)->get();          
          return view('lfunciones.index', array('objlfunciones'=>$objlfunciones, 'busqueda'=>''));

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {                
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El registro ya existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo realizar la consulta');
             }
             return redirect('/');
        }              
    }

    public function getLfunciones(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('7_Editar_funciones')) {
            $editar1 = "lfuncion.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('7_Borrar_funciones')) {
            $borrar1 = "lfuncion.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'descripcion',
            1 => 'editar',
            2 => 'borrar'            
          );

          $objlfuncion =lfunciones::query();

          //para jsindexversion1          
          $objlfuncion->select('lfuncion.id','lfuncion.descripcion',
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));                

          //Cuenta registros
          $totalregistros = lfunciones::select('lfuncion.id','lfuncion.descripcion')
          ->where('lfuncion.descripcion', 'like', '%'.$search.'%')
          ->count('lfuncion.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = lfunciones::select('lfuncion.id','lfuncion.descripcion')
            ->where('lfuncion.descripcion', 'like', '%'.$search.'%')
            ->count(DB::raw('lfuncion.id'));
          }

          //Carga los datos
          $objlfuncion
          ->where('lfuncion.descripcion', 'like', '%'.$search.'%');          

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
            $order = 'lfuncion.descripcion';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objlfuncion->offset($start);
          $objlfuncion->limit($limit);
          $objlfuncion->orderBy($order, $dir);

          $objlfunciones = $objlfuncion->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objlfunciones;
          
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
        return view('lfunciones.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LfuncionesCreateRequest $request)
    {
        try{
            $data = $request->all();
            $data['descripcion'] = strtoupper($data['descripcion']);

            lfunciones::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/lfunciones');

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo crear el registro');
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
            $objlfunciones = lfunciones::find($id);
    
            Session::put('dateconcurrente', $objlfunciones['updated_at']);
            Session::save();

            return view('lfunciones.editar',compact('objlfunciones'));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/lfunciones');

        }
        
        return redirect('/lfunciones');        
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
            $request = new LfuncionesUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $objlfunciones = lfunciones::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {
                $data['descripcion'] = strtoupper($data['descripcion']);

                $afectados = DB::table('lfuncion')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'descripcion'=>$data['descripcion'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/lfunciones');
                } else {
                    $objf = lfunciones::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/lfunciones');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/lfunciones');
                    }
                }

            }

        } catch (Exception $e) {    
            if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'Intenta escribir un valor que está registrado.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo actualizar el registro');
            }

            return redirect('/lfunciones');
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

            $afectados =  lfunciones::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message-error','Registro eliminado exitosamente'); 
            }        
            return redirect('/lfunciones');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/lfunciones');        
        }       
    }

    public function BuscarLfunciones(Request $request) {
        try{

          $busqueda = $request->busqueda;
          $objlfunciones = lfunciones::where('descripcion', 'like', '%'. $busqueda . '%')
            ->orderBy('descripcion','asc')
            ->limit($this->RegxPag)->get();

          return view('lfunciones.index', array('objlfunciones'=>$objlfunciones, 'busqueda'=>$busqueda));                           

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El registro ya existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo realizar la consulta');
             }
             return redirect('/');
        }         
      }    
}