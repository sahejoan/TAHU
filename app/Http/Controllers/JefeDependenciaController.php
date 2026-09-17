<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\jefedependencia;
use App\Models\dependencia;
use App\Models\division;
use App\Http\Controllers\Controller;

use App\Http\Requests\JefeDependenciaCreateRequest;
use App\Http\Requests\JefeDependenciaUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class JefeDependenciaController extends Controller
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

        $this->middleware('role_or_permission:4_Buscar_jefes|4_Ver_jefes|4_Crear_jefes|4_Editar_jefes|4_Borrar_jefes', ['only' => ['index']]);
        $this->middleware('role_or_permission:4_Jefes', ['only' => ['index']]);
        $this->middleware('role_or_permission:4_Buscar_jefes', ['only' => ['BuscarJefeDependencia','getJefeDependencia']]);
        $this->middleware('role_or_permission:4_Crear_jefes', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:4_Editar_jefes', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:4_Borrar_jefes', ['only' => ['destroy']]);                        

        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion', ['only' => 'BuscarElJefe']);                
        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion', ['only' => 'BuscarElJefeDivision','BuscarElJefeTH','BuscarElCoorTH']);                        
    }  

    public function index()
    {     
        try{

          return view('jefedependencia.index', array('busqueda'=>''));

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

    public function getJefeDependencia(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('4_Editar_jefes')) {
            $editar1 = "jefedependencia.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('4_Borrar_jefes')) {
            $borrar1 = "jefedependencia.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'nom_jefe',
            1 => 'editar',
            2 => 'borrar'            
          );

          $objjefedependencia = jefedependencia::query();

          //para jsindexversion1          
          $objjefedependencia->select('jefedependencia.id','jefedependencia.nom_jefe',
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));

          //Cuenta registros
          $totalregistros = jefedependencia::select('jefedependencia.id','jefedependencia.nom_jefe')
          ->where('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
          ->count('jefedependencia.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = jefedependencia::select('jefedependencia.id','jefedependencia.nom_jefe')
            ->where('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
            ->count(DB::raw('jefedependencia.id'));
          }

          //Carga los datos
          $objjefedependencia
          ->where('jefedependencia.nom_jefe', 'like', '%'.$search.'%');

          if (empty($request->input('length'))) {
            $limit = 10;
          } else {
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
            $order = 'jefedependencia.nom_jefe';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objjefedependencia->offset($start);
          $objjefedependencia->limit($limit);
          $objjefedependencia->orderBy($order, $dir);

          $objjefedependencias = $objjefedependencia->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objjefedependencias;
          
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
        return view('jefedependencia.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JefeDependenciaCreateRequest $request)
    {
        try{
            $data = $request->all();

            $data['nom_jefe'] = strtoupper($data['nom_jefe']);

            jefedependencia::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/jefedependencia');

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
            $objjefedependencia = jefedependencia::find($id);
    
            Session::put('dateconcurrente', $objjefedependencia['updated_at']);
            Session::save();

            return view('jefedependencia.editar',compact('objjefedependencia'));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/jefedependencia');

        }
        
        return redirect('/jefedependencia');        
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
            $request = new JefeDependenciaUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $objjefedependencia = jefedependencia::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {
                $afectados = DB::table('jefedependencia')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'nom_jefe'=>strtoupper($data['nom_jefe']),
                      'cedula'=>$data['cedula'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/jefedependencia');
                } else {
                    $objf = jefedependencia::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/jefedependencia');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/jefedependencia');
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

            return redirect('/jefedependencia');
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

            $afectados =  jefedependencia::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }else{
                Session::flash('message-error','Registro no se puede eliminar'); 
            }        
            return redirect('/jefedependencia');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/jefedependencia');        
        }       
    }

    public function BuscarJefeDependencia(Request $request) {
        try{

          $busqueda = $request->busqueda;
          $objjefedependencia = jefedependencia::where('nom_jefe', 'like', '%'. $busqueda . '%')
            ->orderBy('nom_jefe','asc')
            ->limit($this->RegxPag)->get();

          return view('jefedependencia.index', array('objjefedependencia'=>$objjefedependencia, 'busqueda'=>$busqueda));                           

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

    public function BuscarElJefe(Request $request){
        if ($request->ajax()) {
            $id_dep  = $request->id_dep;
            if ($id_dep != '0') {
                try {
                    $objdependencia = dependencia::find($id_dep);
                    $id = $objdependencia['id_jefedep'];
                    $objjefedependencia = jefedependencia::orderBy('nom_jefe','asc')->where('id','=',$id)->get()->toArray();
                    return response()->json($objjefedependencia);
                } catch (Exception $e) {    
                    return response()->json('Error');              
                }
            } else {
                return response()->json('Exit');              
            }
        } else {
          return response()->json('Error');
        }
    } 

    public function BuscarElJefeDivision(Request $request){
        if ($request->ajax()) {

            $id_div  = $request->id_div;

            if ($id_div != '0') {
                try {
                    $objdivision = division::select('id_jefedep')->where('id','=',$id_div)->where('actual','>=','0')->get()->first();

                    $id = $objdivision['id_jefedep'];                    
                    $objjefedependencia = jefedependencia::where('id','=',$id)->orderBy('nom_jefe','asc')->get()->toArray();
                    return response()->json($objjefedependencia);
                } catch (Exception $e) {    
                    return response()->json('Error');              
                }
            } else {
                return response()->json('Exit');              
            }
        } else {
          return response()->json('Error');
        }
    } 

    public function BuscarElJefeTH(Request $request) {

        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {    
                $objjefeth = jefedependencia::select('nom_jefe','id')
                ->orwhereRaw('nom_jefe LIKE ?',['%'.$b.'%'])
                ->orderBy('nom_jefe','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objjefeth);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    } 

    public function BuscarElCoorTH(Request $request) {

        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {    
                $objcoorth = jefedependencia::select('nom_jefe','id')
                ->orwhereRaw('nom_jefe LIKE ?',['%'.$b.'%'])
                ->orderBy('nom_jefe','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objcoorth);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    } 

}