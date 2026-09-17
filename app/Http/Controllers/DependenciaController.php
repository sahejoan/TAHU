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
use App\Models\jefedependencia;
use App\Models\region;
use App\Models\area;
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
use Input;

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

        $this->middleware('role_or_permission:5_Buscar_dependencia|5_Ver_dependencia|5_Crear_dependencia|5_Editar_dependencia|5_Borrar_dependencia', ['only' => ['index']]);
        $this->middleware('role_or_permission:5_Dependencia', ['only' => ['index']]);
        $this->middleware('role_or_permission:5_Ver_dependencia', ['only' => ['VerDependencia']]);
        $this->middleware('role_or_permission:5_Buscar_dependencia', ['only' => ['BuscarDependencia','getDependencia']]);
        $this->middleware('role_or_permission:5_Crear_dependencia', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:5_Editar_dependencia', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:5_Borrar_dependencia', ['only' => ['destroy']]);                        

        $this->middleware('role_or_permission:2_Crear_division|2_Editar_division|5_Crear_dependencia|5_Editar_dependencia', ['only' => 'BuscarDependenciaJefe']);                
        $this->middleware('role_or_permission:5_Crear_dependencia|5_Editar_dependencia', ['only' => 'BuscarDependenciaRegion']);                

        $this->middleware('role_or_permission:8_Crear_firmas|8_Editar_firmas', ['only' => 'BuscarLaDependencia']);                

        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion', ['only' => 'BuscarDependenciaJefe']);                        

    }  

    public function index()
    {     
        try{

          return view('dependencia.index', array('busqueda'=>''));

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

    public function getDependencia(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('5_Ver_dependencia')) {
            $ver1 = "dependencia.id";
          } else {
            $ver1 = "0";
          }                                    

          if (Auth::user()->hasAnyPermission('5_Editar_dependencia')) {
            $editar1 = "dependencia.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('5_Borrar_dependencia')) {
            $borrar1 = "dependencia.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'nom_dep',
            1 => 'nom_reg',
            2 => 'ubicacion',
            3 => 'nom_jefe',
            4 => 'editar',
            5 => 'borrar'            
          );

          $objdependencia = dependencia::query();

          //para jsindexversion1          
          $objdependencia->select('dependencia.id','dependencia.nom_dep','dependencia.ubicacion','region.nom_reg','jefedependencia.nom_jefe',
          DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'))
          ->leftjoin('region','dependencia.id_reg','=','region.id')
          ->leftjoin('jefedependencia','dependencia.id_jefedep','=','jefedependencia.id');          


          //Cuenta registros
          $totalregistros = dependencia::select('dependencia.id','dependencia.nom_dep','dependencia.ubicacion','region.nom_reg','jefedependencia.nom_jefe')
          ->leftjoin('region','dependencia.id_reg','=','region.id')
          ->leftjoin('jefedependencia','dependencia.id_jefedep','=','jefedependencia.id')          
          ->orWhere('dependencia.nom_dep', 'like', '%'.$search.'%')
          ->orWhere('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
          ->orWhere('region.nom_reg', 'like', '%'.$search.'%')
          ->count('jefedependencia.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = dependencia::select('dependencia.id','dependencia.nom_dep','dependencia.ubicacion','region.nom_reg','jefedependencia.nom_jefe')
            ->leftjoin('region','dependencia.id_reg','=','region.id')
            ->leftjoin('jefedependencia','dependencia.id_jefedep','=','jefedependencia.id')          
            ->orWhere('dependencia.nom_dep', 'like', '%'.$search.'%')
            ->orWhere('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
            ->orWhere('region.nom_reg', 'like', '%'.$search.'%')
            ->count(DB::raw('dependencia.id'));
          }

          //Carga los datos
          $objdependencia
          ->orWhere('dependencia.nom_dep', 'like', '%'.$search.'%')
          ->orWhere('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
          ->orWhere('region.nom_reg', 'like', '%'.$search.'%');

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
            $order = 'dependencia.nom_dep';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objdependencia->offset($start);
          $objdependencia->limit($limit);
          $objdependencia->orderBy($order, $dir);

          $objdependencias = $objdependencia->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objdependencias;
          
          $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data
          );

          return response()->json($json_data);
        } catch (Exception $e) {
          //return response()->json($e->getMessage());                              
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
        try{
            $objregion = region::select('nom_reg','id')->orderBy('nom_reg','asc')->limit($this->RegxPag)->pluck('nom_reg','id')->toArray();
            $objjefedependencia = jefedependencia::select('nom_jefe','id')->orderBy('nom_jefe','asc')->limit($this->RegxPag)->pluck('nom_jefe','id')->toArray();

            if (count($objregion)>0) {                                
                $objregion = [''=>'Escribir [Enter Busca]']+$objregion;                  
            } else {
                $objregion = [''=>'Escribir [Enter Busca]'];
            }
            
            if (count($objjefedependencia)>0) {                                
                $objjefedependencia = [''=>'Escribir [Enter Busca]']+$objjefedependencia;                  
            } else {
                $objjefedependencia = [''=>'Escribir [Enter Busca]'];
            }

            return view('dependencia.crear', array('objregion'=>$objregion, 'objjefedependencia'=>$objjefedependencia));

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
            $objdependencia = dependencia::find($id);
    
            if (isset($objdependencia['id'])) {
                if ($objdependencia['id']>0) {                    
                    $objregion = region::select('nom_reg','id')->where('id','=',$objdependencia['id_reg'])->orderBy('nom_reg','asc')->limit($this->RegxPag)->pluck('nom_reg','id')->toArray();
                    $objregion = [''=>'Escribir [Enter Busca]']+$objregion;

                    $objjefedependencia = jefedependencia::select('nom_jefe','id')->where('id','=',$objdependencia['id_jefedep'])->orderBy('nom_jefe','asc')->limit($this->RegxPag)->pluck('nom_jefe','id')->toArray();
                    $objjefedependencia = [''=>'Escribir [Enter Busca2]']+$objjefedependencia;                                        
                }
            } else {
                $objregion = [''=>'Escribir [Enter Busca]'];                
                $objjefedependencia = [''=>'Escribir [Enter Busca]'];
            }

            Session::put('dateconcurrente', $objdependencia['updated_at']);
            Session::save();

            return view('dependencia.editar', array('objdependencia'=>$objdependencia,'objregion'=>$objregion, 'objjefedependencia'=>$objjefedependencia));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
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
                      'nom_dep'=>$data['nom_dep'],
                      'id_jefedep'=>$data['id_jefedep'],
                      'ubicacion'=>$data['ubicacion'],
                      'telf_dep'=>$data['telf_dep'],
                      'id_reg'=>$data['id_reg'],                      
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
                Session::flash('message-error','Ocurrio un problema, no se pudo actualizar el registro');
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
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/dependencia');        
        }       
    }

    public function BuscarDependencia(Request $request) {
        try {

          $busqueda = $request->busqueda;
          $objdependencia = dependencia::orwhere('nom_dep', 'like', '%'. $busqueda . '%')
            ->orwhere('ubicacion', 'like', '%'. $busqueda . '%')
            ->orderBy('nom_dep','asc')
            ->orderBy('ubicacion','asc')
            ->limit($this->RegxPag)->get();

          return view('dependencia.index', array('objdependencia'=>$objdependencia, 'busqueda'=>$busqueda));                           

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

    public function BuscarDependenciaRegion(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {                  
                $objregion = region::select('nom_reg','id')
                ->whereRaw('nom_reg LIKE ?',['%'.$b.'%'])
                ->orderBy('nom_reg','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objregion);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }     

    public function BuscarDependenciaJefe(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {                  
                $objjefedependencia = jefedependencia::select('nom_jefe','id')
                ->whereRaw('nom_jefe LIKE ?',['%'.$b.'%'])
                ->orderBy('nom_jefe','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objjefedependencia);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }

    public function VerDependencia(Request $request) {
        if ($request->ajax()) {
          $id = $request->id;          

          try {                  
            $obj = dependencia::buscardependencia($id);            
            if(!isset($result)) {
              $result = [
                'nom_dep'=> $obj['nom_dep'],
                'nom_jefedep'=> dependencia::buscardependencia($id)->JefeDependencia->nom_jefe,
                'ubicacion'=> $obj['ubicacion'],
                'telf_dep'=> $obj['telf_dep'],
                'nom_reg'=> $obj->Region->nom_reg
              ];
            } else {
              $result = [];
            }

            return response()->json($result);              
          } catch (Exception $e) {    
            return response()->json("Error");
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
                
                return response()->json($objdependencia);                

            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json('Error');
        }
    }    
}