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
use App\Models\lcargos;
use App\Models\funcionarios;
use App\Models\cargofuncionario;
use App\Models\funciones;
use App\Models\dependencia;
use App\Models\jefedependencia;
use App\Models\tipificaciones;
use App\Models\area;
use App\Models\division;
use App\Models\firmas;

use App\Http\Controllers\Controller;

use App\Http\Requests\DesignacionCreateRequest;
use App\Http\Requests\DesignacionUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
class DesignacionController extends Controller
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

        $this->middleware('role_or_permission:9_Buscar_designacion|9_Ver_designacion|9_Crear_designacion|9_Editar_designacion|9_Borrar_designacion', ['only' => ['index']]);
        $this->middleware('role_or_permission:9_Designacion', ['only' => ['index']]);
        $this->middleware('role_or_permission:9_Ver_designacion', ['only' => ['VerRegion']]);
        $this->middleware('role_or_permission:9_Buscar_designacion', ['only' => ['BuscarRegion','getDesignacion']]);
        $this->middleware('role_or_permission:9_Crear_designacion', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:9_Editar_designacion', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:9_Borrar_designacion', ['only' => ['destroy']]);        
        $this->middleware('role_or_permission:9_Imprimir_designacion_doc', ['only' => ['ImprimirDesignacion']]);        

    }  

    public function index()
    {     
        try{
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
            ->where('dependencia.id_reg','=',Auth::user()->Dependencia->Region->id)
            ->orderBy('dependencia.nom_dep','asc')
            ->pluck('nombredependencia','id')->toArray();

            $objdivision = division::select(DB::raw('concat(division.descripcion," - ",dependencia.nom_dep," ",dependencia.ubicacion,IF(division.actual=0, " (Coordinador)", IF(division.actual=1, " (Jefatura)",""))) as descripcion'),'division.id')
            ->leftjoin('dependencia','division.ubicacion','=','dependencia.id')
            ->where('division.actual','<','2')
            ->where('division.ubicacion','=',Auth::user()->Dependencia->Region->id)
            ->orderBy('descripcion','asc')
            ->pluck('descripcion','id')->toArray();

            if (count($objdivision)>0) {                                
              $objdivision = ['0'=>'Todos']+$objdivision;                  
            } else {
              $objdivision = ['0'=>'Todos'];
            }
          } else {
            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
            ->orderBy('dependencia.nom_dep','asc')
            ->pluck('nombredependencia','id')->toArray();            

            if (count($objdependencia)>0) {                                
              $objdependencia = ['0'=>'Todos']+$objdependencia;                  
            } else {
              $objdependencia = ['0'=>'Todos'];
            }

            $objdivision = ['0'=>'Todos'];                  
          }

          $objarea = ['0'=>'Todos'];

          $objdesignacion = designacion::orderBy('fecha_desig','asc')->orderBy('numdesig','asc')->limit($this->RegxPag)->get();
          return view('designacion.index', array('busqueda'=>'', 'objdependencia'=>$objdependencia, 'id_temp'=>'', 'objarea'=>$objarea, 'objdivision'=>$objdivision,'id_tempa'=>'0', 'id_tempd'=>'0'));

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

    public function getDesignacion(Request $request) {

      if ($request->ajax()) {
        try {
          $search = $request->input('search.value');
          $id_dep = $request->id_dep;
          $id_divi = $request->id_divi;
          $id_area = $request->id_area;                                          
          $estatus = $request['estatus'];
          if ($estatus=="Todos") { $estatus = '';}

          //para jsindexversion1
          $seleccionado1 = "designacion.id";

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($id_dep != 0) {
              $id_dep = Auth::user()->Dependencia->Region->id;
            }            
          }

          if (Auth::user()->hasAnyPermission('9_Ver_designacion')) {
            $ver1 = "designacion.id";
          } else {
            $ver1 = "0";
          }                                    

          if (Auth::user()->hasAnyPermission('9_Editar_designacion')) {
            $editar1 = "designacion.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('9_Borrar_designacion')) {
            $borrar1 = "designacion.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'seleccionado',
            1 => 'fecha_desig',            
            2 => 'estatus',
            3 => 'nombre',            
            4 => 'nom_dep',            
            5 => 'nom_reg',                        
            6 => 'ubicacion',            
            7 => 'nom_area',            
            8 => 'ver',            
            9 => 'editar',
            10 => 'borrar'            
          );

          $operador = '>';
          $valor = '0';
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $operador = '=';
            $valor = Auth::user()->id_dep;
          } else {
            if ($request->id_dep != '0') {
              $operador = '=';
              $valor = $request->id_dep;
            } else {
              $operador = '>';
              $valor = '0';
            }
          }
 
           $operadordivi = '>';          
          if ($id_divi != 0) {
            $operadordivi = '=';
          } else {
            $operadordivi = '>';
          }

          $operadorarea = '>';          
          if ($id_area != 0) {
            $operadorarea = '=';
          } else {
            $operadorarea = '>';
          }

          $objdesignacion = designacion::query();

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $objdesignacion = designacion::select(
              DB::raw($seleccionado1.' as seleccionado'), 
              'designacion.id', 'designacion.numdesig', 
              DB::raw('date_format(designacion.fecha_desig, "%d-%m-%Y") as fecha_desig'), 
              'designacion.estatus',               
              DB::raw('(select (concat(funcionarios.nombre," ",funcionarios.apellido)) from funcionarios where designacion.id_func = funcionarios.id) as nombre'),
              DB::raw('(select dependencia.nom_dep from dependencia where dependencia.id = designacion.id_dep) as nom_dep'),
              DB::raw('(select dependencia.ubicacion from dependencia where dependencia.id = designacion.id_dep) as ubicacion'),
              DB::raw('(select area.nom_area from area where area.id = designacion.id_area) as nom_area'),
              DB::raw('(select region.nom_reg from region where region.id = (select dependencia.id_reg from dependencia where dependencia.id = designacion.id_dep)) as nom_reg'),
              DB::raw($ver1.' as ver'), 
              DB::raw($editar1.' as editar'), 
              DB::raw($borrar1.' as borrar'));            
          } else {
            $objdesignacion = designacion::select(
              DB::raw($seleccionado1.' as seleccionado'), 
              'designacion.id', 'designacion.numdesig', 
              DB::raw('date_format(designacion.fecha_desig, "%d-%m-%Y") as fecha_desig'), 
              'designacion.estatus', 
              DB::raw('(select (concat(funcionarios.nombre," ",funcionarios.apellido)) from funcionarios where designacion.id_func = funcionarios.id) as nombre'),
              DB::raw('(select dependencia.nom_dep from dependencia where dependencia.id = designacion.id_dep) as nom_dep'),
              DB::raw('(select dependencia.ubicacion from dependencia where dependencia.id = designacion.id_dep) as ubicacion'),
              DB::raw('(select area.nom_area from area where area.id = designacion.id_area) as nom_area'),
              DB::raw('(select region.nom_reg from region where region.id = (select dependencia.id_reg from dependencia where dependencia.id = designacion.id_dep)) as nom_reg'),
              DB::raw($ver1.' as ver'), 
              DB::raw($editar1.' as editar'), 
              DB::raw($borrar1.' as borrar'));
          }

          //Cuenta registros
          $totalregistros = designacion::select(
            DB::raw($seleccionado1.' as seleccionado'), 
            'designacion.id', 'designacion.numdesig', 
            DB::raw('date_format(designacion.fecha_desig, "%d-%m-%Y") as fecha_desig'), 
            'designacion.estatus',             
            DB::raw('(select (concat(funcionarios.nombre," ",funcionarios.apellido)) from funcionarios where designacion.id_func = funcionarios.id) as nombre'),
            DB::raw('(select dependencia.nom_dep from dependencia where dependencia.id = designacion.id_dep) as nom_dep'),
            DB::raw('(select dependencia.ubicacion from dependencia where dependencia.id = designacion.id_dep) as ubicacion'),
            DB::raw('(select area.nom_area from area where area.id = designacion.id_area) as nom_area'),
            DB::raw('(select region.nom_reg from region where region.id = (select dependencia.id_reg from dependencia where dependencia.id = designacion.id_dep)) as nom_reg'))
            ->where('designacion.id_dep',$operador,$valor)
            ->where('designacion.id_divi',$operadordivi,$id_divi)
            ->where('designacion.id_area',$operadorarea,$id_area)                                      
            ->where(function ($query) use ($search) {
            $query->orwhere('designacion.numdesig', 'like', '%'. $search . '%')
              ->orwhere(DB::raw('(select funcionarios.cedula from funcionarios where funcionarios.id = designacion.id_func)'), 'like', '%' . $search . '%')
              ->orwhere(DB::raw('(select (concat(funcionarios.nombre," ",funcionarios.apellido)) from funcionarios where funcionarios.id = designacion.id_func)'), 'like', '%' . $search . '%');
            })
            ->where('designacion.estatus','like', '%'. $estatus . '%')
            ->count('designacion.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = designacion::select(
              DB::raw($seleccionado1.' as seleccionado'), 
              'designacion.id', 
              'designacion.numdesig', 
              DB::raw('date_format(designacion.fecha_desig, "%d-%m-%Y") as fecha_desig'), 
              'designacion.estatus', 
              DB::raw('(select (concat(funcionarios.nombre," ",funcionarios.apellido)) from funcionarios where designacion.id_func = funcionarios.id) as nombre'),
              DB::raw('(select dependencia.nom_dep from dependencia where dependencia.id = designacion.id_dep) as nom_dep'),
              DB::raw('(select dependencia.ubicacion from dependencia where dependencia.id = designacion.id_dep) as ubicacion'),
              DB::raw('(select area.nom_area from area where area.id = designacion.id_area) as nom_area'),
              DB::raw('(select region.nom_reg from region where region.id = (select dependencia.id_reg from dependencia where dependencia.id = designacion.id_dep)) as nom_reg'))
              ->where('designacion.id_dep',$operador,$valor)
              ->where('designacion.id_divi',$operadordivi,$id_divi)
              ->where('designacion.id_area',$operadorarea,$id_area)                                      
              ->where(function ($query) use ($search) {
              $query->orwhere('designacion.numdesig', 'like', '%'. $search . '%')
                ->orwhere(DB::raw('(select funcionarios.cedula from funcionarios where funcionarios.id = designacion.id_func)'), 'like', '%' . $search . '%')
                ->orwhere(DB::raw('(select (concat(funcionarios.nombre," ",funcionarios.apellido)) from funcionarios where funcionarios.id = designacion.id_func)'), 'like', '%' . $search . '%');
              })
              ->where('designacion.estatus','like', '%'. $estatus . '%')            
              ->count(DB::raw('designacion.id'));
          }
 
          //Carga los datos
          $objdesignacion
          ->where('designacion.id_dep',$operador,$valor)
          ->where('designacion.id_divi',$operadordivi,$id_divi)
          ->where('designacion.id_area',$operadorarea,$id_area)                                      
          ->where(function ($query) use ($search) {
            $query->orwhere('designacion.numdesig', 'like', '%'. $search . '%')
              ->orwhere(DB::raw('(select funcionarios.cedula from funcionarios where funcionarios.id = designacion.id_func)'), 'like', '%' . $search . '%')
              ->orwhere(DB::raw('(select (concat(funcionarios.nombre," ",funcionarios.apellido)) from funcionarios where funcionarios.id = designacion.id_func)'), 'like', '%' . $search . '%');
          })
          ->where('designacion.estatus','like', '%'. $estatus . '%');
          
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
            $order = 'designacion.fecha_desig';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objdesignacion->offset($start);
          $objdesignacion->limit($limit);
          $objdesignacion->orderBy($order, $dir);

          $objdesignaciones = $objdesignacion->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objdesignaciones;

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
      /*
          $busqueda = $request->busqueda;
          $objdesignacion = designacion::select('designacion.id', 'designacion.numdesig', 'designacion.fecha_desig', 'designacion.fecha_cese', 'designacion.estatus', 'designacion.id_func', 'designacion.id_dep', 'designacion.id_jefedep', 'designacion.id_area', 'funcionarios.nombre', 'funcionarios.apellido')
          ->leftjoin('funcionarios','designacion.id_func','=','funcionarios.id')
          ->orwhere('designacion.numdesig', 'like', '%'. $busqueda . '%')
          ->orwhere('funcionarios.nombre', 'like', '%' . $busqueda . '%')
          ->orwhere('funcionarios.apellido', 'like', '%' . $busqueda . '%')                                   
          ->orderBy('designacion.fecha_desig','asc')
          ->orderBy('designacion.numdesig','asc')
          ->limit($this->RegxPag)->get();
      */

      
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    	try {
          $objtipificacion = new tipificaciones();        
    		  $objestatus = $objtipificacion->estatusdesignacion;
          
        	$objfuncionarios = funcionarios::select(DB::raw('(concat(funcionarios.nombre, " ", funcionarios.apellido)) as nombrefuncionario'),'id')->orderBy('nombre','asc')->orderBy('apellido','asc')->limit($this->RegxPag)->pluck('nombrefuncionario','id')->toArray();
          $objjefedependencia = jefedependencia::select('nom_jefe','id')->where('id','=','0')->orderBy('nom_jefe','asc')->limit($this->RegxPag)->pluck('nom_jefe','id')->toArray();

          $objarea = area::select('nom_area','id')->where('id','=','0')->orderBy('nom_area','asc')->limit($this->RegxPag)->pluck('nom_area','id')->toArray();

          $objdivision = division::select(DB::raw('concat(descripcion," - ",ubicacion,IF(actual=0, " (Coordinador)", IF(ACTUAL=1, " (Jefatura)",""))) as descripcion'),'id')->where('actual','<','2')
          ->where('id','=','0')->orderBy('descripcion','asc')->limit($this->RegxPag)->pluck('descripcion','id')->toArray();

          $objfirmas = firmas::select(DB::raw('concat(firmas.firma," ",dependencia.nom_dep," ",dependencia.ubicacion," - Region: ", region.nom_reg) as firma'),'firmas.id')
          ->join('dependencia','firmas.id_dep','=','dependencia.id')
          ->join('region','dependencia.id_reg','=','region.id')
          ->where('firmas.actual','=','1')->where('firmas.id_dep','=','0')->orderBy('firma','asc')->limit($this->RegxPag)->pluck('firma','id')->toArray();

        	$objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion, " - Region: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
        	->leftjoin('region','dependencia.id_reg','=','region.id')
          ->orderBy('dependencia.nom_dep','asc')
        	->limit($this->RegxPag)->pluck('nombredependencia','id')->toArray();

            if (count($objdependencia)>0) {                                
                $objdependencia = [''=>'Escribir [Enter Busca]']+$objdependencia;                  
            } else {
                $objdependencia = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objfuncionarios)>0) {                                
                $objfuncionarios = [''=>'Escribir [Enter Busca]']+$objfuncionarios;                  
            } else {
                $objfuncionarios = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objjefedependencia)>0) {                                
                $objjefedependencia = [''=>'Escribir [Enter Busca]']+$objjefedependencia;                  
            } else {
                $objjefedependencia = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objarea)>0) {                                
                $objarea = [''=>'Escribir [Enter Busca]']+$objarea;                  
            } else {
                $objarea = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objdivision)>0) {                                
                $objdivision = [''=>'Escribir [Enter Busca]']+$objdivision;                  
            } else {
                $objdivision = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objfirmas)>0) {                                
                $objfirmas = [''=>'Escribir [Enter Busca]']+$objfirmas;                  
            } else {
                $objfirmas = [''=>'Escribir [Enter Busca]'];
            }

        	  return view('designacion.crear', array('objestatus'=>$objestatus, 'objfuncionarios'=>$objfuncionarios, 'objjefedependencia'=>$objjefedependencia, 'objdependencia'=>$objdependencia, 'objarea'=>$objarea, 'objdivision'=>$objdivision,'objfirmas'=>$objfirmas));

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

    public function CrearDesignacion($id_func)
    {

      try {
          $objf = funcionarios::find($id_func);

          if ($objf['id']>0) {
            $objtipificacion = new tipificaciones();        
            $objestatus = $objtipificacion->estatusdesignacion;
          
            $objfuncionarios = funcionarios::select(DB::raw('(concat(funcionarios.nombre, " ", funcionarios.apellido)) as nombrefuncionario'),'id')->where('id','=',$id_func)->orderBy('nombre','asc')->orderBy('apellido','asc')->limit($this->RegxPag)->pluck('nombrefuncionario','id')->toArray();
            $objjefedependencia = jefedependencia::select('nom_jefe','id')->where('id','=','0')->orderBy('nom_jefe','asc')->limit($this->RegxPag)->pluck('nom_jefe','id')->toArray();

            $objarea = area::select('nom_area','id')->where('id','=','0')->orderBy('nom_area','asc')->limit($this->RegxPag)->pluck('nom_area','id')->toArray();
            $objdivision = division::select('descripcion','id')->where('id','=','0')->where('actual','<','2')->orderBy('descripcion','asc')->limit($this->RegxPag)->pluck('descripcion','id')->toArray();

            $objfirmas = firmas::select(DB::raw('concat(firmas.firma," ",dependencia.nom_dep," ",dependencia.ubicacion," - Region: ", region.nom_reg) as firma'),'firmas.id')
            ->join('dependencia','firmas.id_dep','=','dependencia.id')
            ->join('region','dependencia.id_reg','=','region.id')
            ->where('firmas.actual','=','1')->where('firmas.id_dep','=','0')->orderBy('firma','asc')->limit($this->RegxPag)->pluck('firma','id')->toArray();

            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion, " - Region: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
            ->leftjoin('region','dependencia.id_reg','=','region.id')
            ->orderBy('dependencia.nom_dep','asc')
            ->limit($this->RegxPag)->pluck('nombredependencia','id')->toArray();

            if (count($objdependencia)>0) {                                
                $objdependencia = [''=>'Escribir [Enter Busca]']+$objdependencia;                  
            } else {
                $objdependencia = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objjefedependencia)>0) {                                
                $objjefedependencia = [''=>'Escribir [Enter Busca]']+$objjefedependencia;                  
            } else {
                $objjefedependencia = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objarea)>0) {                                
                $objarea = [''=>'Escribir [Enter Busca]']+$objarea;                  
            } else {
                $objarea = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objdivision)>0) {                                
                $objdivision = [''=>'Escribir [Enter Busca]']+$objdivision;                  
            } else {
                $objdivision = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objfirmas)>0) {                                
                $objfirmas = [''=>'Escribir [Enter Busca]']+$objfirmas;                  
            } else {
                $objfirmas = [''=>'Escribir [Enter Busca]'];
            }

            return view('designacion.crearf', array('objestatus'=>$objestatus, 'objfuncionarios'=>$objfuncionarios, 'objjefedependencia'=>$objjefedependencia, 'objdependencia'=>$objdependencia, 'objarea'=>$objarea, 'objdivision'=>$objdivision,'objfirmas'=>$objfirmas, 'tid_func'=>$id_func));

          } else {
            Session::flash('message-error','La consulta para este funcionario no se puede realizar.');
            return redirect('/admin');
          }

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El registro ya existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo realiza la consulta');
             }
             return redirect('/admin');
        }         
    }    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DesignacionCreateRequest $request)
    {
        try{
            $data = $request->all();

            $data2 = $data;

            $fecha = $data2['fecha_desig']." 00:00:00";
            $dt = new DateTime($fecha);
            $data['fecha_desig'] = $dt->format('Y-m-d');            

            if (!isset($data2['fecha_cese'])) {
              $fecha = $data2['fecha_cese']." 00:00:00";
              $dt = new DateTime($fecha);
              $data['fecha_cese'] = $dt->format('Y-m-d');
            } else {
              $data['fecha_cese'] = null;
            }

            if (designacion::DesignacionExistente($data['id_func'])) {
              Session::flash('message-error','Existe una designación que no ha cesado debe actualizar la designación del funcionario antes de crear otra designación');
              return redirect('/designacion');
            } else {

              $firmas = firmas::find($data['id_firma']);
              $provi_jefe = $firmas['provi_jefe'];
              $fecha_provi = $firmas['fecha_provi'];
              $gaceta_provi = $firmas['gaceta_provi'];
              $fecha_gaceta = $firmas['fecha_gaceta'];
              $idusuario = Auth::user()->id;           //El usuario que carga la designacion y la genera apunta al indice id_coorth

              $data = $data + ['provi_jefe'=>$provi_jefe, 'fecha_provi'=>$fecha_provi, 'gaceta_provi'=>$gaceta_provi, 'fecha_gaceta'=>$fecha_gaceta, 'id_coorth'=>$idusuario];

              designacion::create($data);
    
              Session::flash('message','Registro se creo con éxito');
              return redirect('/designacion');
            }

            //$ultimoid = DB::getPdo()->lastInsertId(); 
            //Session::flash('message','Registro se creo con éxito');
            //return redirect('/designacion/'.$ultimoid.'/edit');

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

            $objdesignacion = designacion::find($id);

            $objtipificacion = new tipificaciones();        
            $objestatus = $objtipificacion->estatusdesignacion;
    
            if (isset($objdesignacion['id'])) {
                $objfuncionarios = funcionarios::select(DB::raw('(concat(funcionarios.nombre, " ", funcionarios.apellido)) as nombrefuncionario'),'id')->where('id','=',$objdesignacion['id_func'])->orderBy('funcionarios.nombre','asc')->orderBy('funcionarios.apellido','asc')->pluck('nombrefuncionario','id')->toArray();
                $objjefedependencia = jefedependencia::select('nom_jefe','id')->where('id','=',$objdesignacion['id_jefedep'])->orderBy('nom_jefe','asc')->pluck('nom_jefe','id')->toArray();
                $objjefedivision = jefedependencia::select('nom_jefe','id')->where('id','=',$objdesignacion['id_jefediv'])->orderBy('nom_jefe','asc')->pluck('nom_jefe','id')->toArray();
                $objjefeth = jefedependencia::select('nom_jefe','id')->where('id','=',$objdesignacion['id_jefeth'])->orderBy('nom_jefe','asc')->pluck('nom_jefe','id')->toArray();
                $objcoorth = jefedependencia::select('nom_jefe','id')->where('id','=',$objdesignacion['id_coorth'])->orderBy('nom_jefe','asc')->pluck('nom_jefe','id')->toArray();
                $objarea = area::select('nom_area','id')->where('id','=',$objdesignacion['id_area'])->orderBy('nom_area','asc')->pluck('nom_area','id')->toArray();

                $objdivision = division::select(DB::raw('concat(division.descripcion," - ",dependencia.nom_dep," ",dependencia.ubicacion,IF(division.actual=0, " (Coordinador)", IF(division.actual=1, " (Jefatura)",""))) as descripcion'),'division.id')
                ->leftjoin('dependencia','division.ubicacion','=','dependencia.id')
                ->where('division.actual','<','2')
                ->where('division.id','=',$objdesignacion['id_divi'])->orderBy('division.descripcion','asc')->pluck('descripcion','id')->toArray();

                $objfirmas = firmas::select(DB::raw('concat(firmas.firma," ",dependencia.nom_dep," ",dependencia.ubicacion," - Region: ", region.nom_reg) as firma'),'firmas.id')
                ->join('dependencia','firmas.id_dep','=','dependencia.id')
                ->join('region','dependencia.id_reg','=','region.id')
                ->where('firmas.id','=',$objdesignacion['id_firma'])->orderBy('firma','asc')->pluck('firma','id')->toArray();

                $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep," - ", dependencia.ubicacion, " - Región: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
                ->where('dependencia.id','=',$objdesignacion['id_dep'])
                ->leftjoin('region','dependencia.id_reg','=','region.id')
                ->orderBy('dependencia.nom_dep','asc')
                ->pluck('nombredependencia','id')->toArray();
            } else {
                $objfuncionarios = [''=>'Escribir [Enter Busca]'];
                $objjefedependencia = [''=>'Escribir [Enter Busca]'];
                $objjefedivision = [''=>'Escribir [Enter Busca]'];
                $objjefeth = [''=>'Escribir [Enter Busca]'];
                $objcoorth = [''=>'Escribir [Enter Busca]'];
                $objdependencia = [''=>'Escribir [Enter Busca]'];
                $objarea = [''=>'Escribir [Enter Busca]'];
                $objdivision = [''=>'Escribir [Enter Busca]'];
                $objfirmas = [''=>'Escribir [Enter Busca]'];
            }

            Session::put('dateconcurrente', $objdesignacion['updated_at']);
            Session::save();

            return view('designacion.editar', array('objdesignacion'=>$objdesignacion, 'objestatus'=>$objestatus, 'objfuncionarios'=>$objfuncionarios, 'objjefedependencia'=>$objjefedependencia, 'objdependencia'=>$objdependencia, 'objarea'=>$objarea, 'objdivision'=>$objdivision, 'objfirmas'=>$objfirmas, 'objjefedivision'=>$objjefedivision,'objjefeth'=>$objjefeth,'objcoorth'=>$objcoorth));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/designacion');

        }
        
        return redirect('/designacion');        
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
            $request = new DesignacionUpdateRequest();       //Instancia de la clase que valida        
            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $request->setId($id);


            if (designacion::DesignacionActualizable($data['id_func'], $id)) {
              $objdesignacion = designacion::find($id);       //Se busca el registro

              $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

              //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
              if ($validation->fails()) {

                return redirect()->back()->withInput($data)->withErrors($validation->messages());

              } else {
                
                $firmas = firmas::find($data['id_firma']);
                $provi_jefe = $firmas['provi_jefe'];
                $fecha_provi = $firmas['fecha_provi'];
                $gaceta_provi = $firmas['gaceta_provi'];
                $fecha_gaceta = $firmas['fecha_gaceta'];
                $idusuario = Auth::user()->id;           //El usuario que carga la designacion y la genera apunta al indice id_coorth

                if (!isset($data['fecha_cese'])) {
                  $fecha_cese = date("Y-m-d",strtotime($fecha_cese));
                } else {
                  $fecha_cese = null;
                }

                if (!isset($data['fecha_desig'])) {
                  $fecha_desig = date("Y-m-d",strtotime($fecha_desig));
                } else {
                  $fecha_desig = null;
                }

                  $afectados = DB::table('designacion')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                  ->update(
                  [
                      'numdesig'=>$data['numdesig'],
                      'fecha_desig'=> $fecha_desig,
                      'fecha_cese'=> $fecha_cese,
                      'id_func'=>$data['id_func'],
                      'id_dep'=>$data['id_dep'],
                      'id_jefedep'=>$data['id_jefedep'],
                      'id_area'=>$data['id_area'],
                      'id_divi'=>$data['id_divi'],
                      'id_firma'=>$data['id_firma'],
                      'id_jefeth'=>$data['id_jefeth'],
                      'id_coorth'=>$idusuario,
                      'estatus'=>$data['estatus'],
                      'provi_jefe'=>$provi_jefe,
                      'fecha_provi'=>$fecha_provi,
                      'gaceta_provi'=>$gaceta_provi,
                      'fecha_gaceta'=>$fecha_gaceta,
                      'updated_at'=>DB::raw('NOW()')
                    ]
                  );


                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/designacion');
                } else {
                    $objf = designacion::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/designacion');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/designacion');
                    }
                }
              }
            } else {
              Session::flash('message-error','Existe otra designación activa para el funcionarios, debe culminar la designación para poder actualizar este registro.');
              return redirect('/designacion');
            }

        } catch (Exception $e) {                        
            if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'Intenta escribir un valor que está registrado.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo actualizar el registro');
            }

            return redirect('/designacion');
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

            $afectados = null;
            $objcargofuncionario = cargofuncionario::where('id_desig','=',$id)->get()->toArray();

            if (count($objcargofuncionario)>0) {
              $id_cargo = $objcargofuncionario['id'];
            } else {
              $id_cargo = 0;
            }
            

            $id_desig = $id;
            $afectados = DB::transaction(function() use ($id_desig, $id_cargo) {
              $result1 =  funciones::where('id_cargo','=',$id_cargo)->delete();
              $result2 =  cargofuncionario::where('id_desig','=',$id_desig)->delete();
              $result3 =  designacion::where('id','=',$id_desig)->delete();
              return $result1+$result2+$result3;
            });
        
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }        
            return redirect('/designacion');

        } catch (Exception $e) {    

            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/designacion');        
        }       
    }

    public function VerDesignacion(Request $request) {
        if ($request->ajax()) {
          $id = $request->id;          

          try {                  
            $obj = designacion::buscardesignacion($id);
            $numdesig = $obj['numdesig'];
            $fecha_desig = $obj['fecha_desig'];
            $fecha_cese = $obj['fecha_sece'];
            $estatus =  $obj['estatus'];
            $nom_func = $obj->Funcionarios->nombre.' '.$obj->Funcionarios->apellido;
            $nom_dep = $obj->Dependencia->nom_dep;
            $nom_reg = $obj->Dependencia->Region->nom_reg;
            $ubicacion= $obj->Dependencia->ubicacion;
            $nom_area = $obj->Area->nom_area;            
            $nom_divi = $obj->Division->descripcion;            
            $nom_firma = $obj->Firmas->firma;
            $nom_jefeth = $obj->JefeTH->nom_jefe;
            $nom_coorth = $obj->CoordinadorTH->name;

            $jefe_dep = $obj->JefeDependencia->nom_jefe;
            $jefe_div = $obj->JefeDivision->nom_jefe;

            //$result = ['numdesig'=>$numdesig,'fecha_desig'=>$fecha_desig,'fecha_cese'=>$fecha_cese,'nom_func'=>$nom_func,'nom_dep'=>$nom_dep,'nom_reg'=>$nom_reg,'nom_area'=>$nom_area];
            $result = ['numdesig'=>$numdesig,'fecha_desig'=>$fecha_desig,'fecha_cese'=>$fecha_cese,'nom_func'=>$nom_func,'nom_dep'=>$nom_dep,'nom_reg'=>$nom_reg,'nom_area'=>$nom_area, 'nom_divi'=>$nom_divi, 'nom_firma'=>$nom_firma, 'ubicacion'=>$ubicacion,'estatus'=>$estatus, 'jefe_dep'=>$jefe_dep, 'jefe_div'=>$jefe_div, 'nom_jefeth'=>$nom_jefeth, 'nom_coorth'=>$nom_coorth];
            return response()->json($result);              
          } catch (Exception $e) {    
            return response()->json('Error');
          }
        } else {
          return response()->json("Error");              
        } 
    }

    public function VerDesignacionActual(Request $request) {
        if ($request->ajax()) {
          $id_func = $request->id_func;

          try {                  
            $objdf = designacion::select('id')->where('estatus','=','Activo')->where('id_func','=',$id_func)->get()->first();

            if ($objdf != null) {
              $id = $objdf['id'];

              $obj = designacion::buscardesignacion($id);
              $numdesig = $obj['numdesig'];
              $fecha_desig = $obj['fecha_desig'];
              $fecha_cese = $obj['fecha_sece'];
              $estatus =  $obj['estatus'];
              $nom_func = $obj->Funcionarios->nombre.' '.$obj->Funcionarios->apellido;
              $nom_dep = $obj->Dependencia->nom_dep;
              $nom_reg = $obj->Dependencia->Region->nom_reg;
              $ubicacion= $obj->Dependencia->ubicacion;
              $nom_area = $obj->Area->nom_area;            
              $nom_divi = $obj->Division->descripcion;            
              $nom_firma = $obj->Firmas->firma;
              $nom_jefeth = $obj->JefeTH->nom_jefe;
              $nom_coorth = $obj->CoordinadorTH->name;

              $jefe_dep = $obj->JefeDependencia->nom_jefe;
              $jefe_div = $obj->JefeDivision->nom_jefe;

              //$result = ['numdesig'=>$numdesig,'fecha_desig'=>$fecha_desig,'fecha_cese'=>$fecha_cese,'nom_func'=>$nom_func,'nom_dep'=>$nom_dep,'nom_reg'=>$nom_reg,'nom_area'=>$nom_area];
              $result = ['numdesig'=>$numdesig,'fecha_desig'=>$fecha_desig,'fecha_cese'=>$fecha_cese,'nom_func'=>$nom_func,'nom_dep'=>$nom_dep,'nom_reg'=>$nom_reg,'nom_area'=>$nom_area, 'nom_divi'=>$nom_divi, 'nom_firma'=>$nom_firma, 'ubicacion'=>$ubicacion,'estatus'=>$estatus, 'jefe_dep'=>$jefe_dep, 'jefe_div'=>$jefe_div, 'nom_jefeth'=>$nom_jefeth, 'nom_coorth'=>$nom_coorth];
              return response()->json($result);              
            } else {
              return response()->json('Error');  
            }              
          } catch (Exception $e) {    
            return response()->json('Error');
          }
        } else {
          return response()->json("Error");              
        } 
    }

    public function ImprimirDesignacion(Request $request) {
        $id = $request->idcheck;          

        try {                            
          if ($id >0) {
            $objhoraactual = DB::select('select now() as horaactual;');          
            $horaactual = $objhoraactual[0]->horaactual;

            $obj = designacion::buscardesignacion($id);
            $numdesig = $obj['numdesig'];
            $fecha_desig = $obj['fecha_desig'];
            $fecha_cese = $obj['fecha_sece'];
            $estatus =  $obj['estatus'];
            
            $nom_func = $obj->Funcionarios->nombre.' '.$obj->Funcionarios->apellido;
            $ced_func = $obj->Funcionarios->cedula;
            $condicion_in = $obj->Funcionarios->condicion_in;
            $nom_firma = $obj->Firmas->firma;
            $nom_reg = $obj->Dependencia->Region->nom_reg;            
            $nom_area = $obj->Area->nom_area;            
            $jefe_dep = $obj->JefeDependencia->nom_jefe;
            $provi_jefe = $obj->provi_jefe;
            $fecha_provi = $obj->fecha_provi;
            $gaceta_provi = $obj->gaceta_provi;
            $fecha_gaceta = $obj->fecha_gaceta;
            $nom_dep = $obj->Dependencia->nom_dep;
            $ubicacion= $obj->Dependencia->ubicacion;            
           
            $nom_jefeth = $obj->JefeTH->nom_jefe;
            $nom_coorth = $obj->CoordinadorTH->name;

            Carbon::setLocale('es');

            if ($fecha_desig!="") {
              $fecha = Carbon::parse($fecha_desig);            
              $fecha = $fecha->format('d M Y'); 
            } else {
              $fecha = "";
            }
            
            $siglas_firma = $this->Siglas($nom_firma);            
            $siglas_jefeth = $this->Siglas($nom_jefeth);
            $siglas_coorth = $this->Siglas($nom_coorth);

            $result = [
            'numdesig'=>$numdesig,
            'nom_func'=>$nom_func,
            'ced_func'=>$ced_func,
            'condicion_in'=>$condicion_in,
            'nom_firma'=>$nom_firma,
            'nom_reg'=>$nom_reg,
            'fecha_desig'=>$fecha,
            'nom_area'=>$nom_area,
            'nom_dep'=>$nom_dep,
            'ubicacion'=>$ubicacion,            
            'provi_jefe'=>$provi_jefe,
            'fecha_provi'=>$fecha_provi,
            'gaceta_provi'=>$gaceta_provi,
            'fecha_gaceta'=>$fecha_gaceta,
            'siglas_firma'=>$siglas_firma,
            'siglas_jefeth'=>$siglas_jefeth,
            'siglas_coorth'=>$siglas_coorth,
            'horaactual'=>$horaactual
            ];

            return PDF::loadView('designacion.imprimirdesignacion',$result)
            ->stream('designacion.pdf');
          } else {
            Session::flash('message-error','No tiene seleccionado la designación asociada al documento que va a generar.');     
            return redirect('admin');
          }
        } catch (Exception $e) {    
            Session::flash('message-error','Ocurrio un problema, no se pudo generar el reporte');     
            return redirect('admin');
        }
    }


    public function BuscarDesignacion(Request $request) {
        try {

          $busqueda = $request->busqueda;
          $objdesignacion = designacion::select('designacion.id', 'designacion.numdesig', 'designacion.fecha_desig', 'designacion.fecha_cese', 'designacion.estatus', 'designacion.id_func', 'designacion.id_dep', 'designacion.id_jefedep', 'designacion.id_area', 'funcionarios.nombre', 'funcionarios.apellido')
          ->leftjoin('funcionarios','designacion.id_func','=','funcionarios.id')
          ->orwhere('designacion.numdesig', 'like', '%'. $busqueda . '%')
          ->orwhere('funcionarios.nombre', 'like', '%' . $busqueda . '%')
          ->orwhere('funcionarios.apellido', 'like', '%' . $busqueda . '%')                                   
          ->orderBy('designacion.fecha_desig','asc')
          ->orderBy('designacion.numdesig','asc')
          ->limit($this->RegxPag)->get();

          return view('designacion.index', array('objdesignacion'=>$objdesignacion, 'busqueda'=>$busqueda));

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

      public function BuscarDesignacionActual($id) {

        try {

          $busqueda = "";
          $objdesignacion = designacion::where('designacion.id', '=', $id)->orderBy('fecha_desig','asc')->orderBy('numdesig','asc')->limit($this->RegxPag)->get();

          return view('designacion.index', array('objdesignacion'=>$objdesignacion, 'busqueda'=>$busqueda));

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

      public function Siglas($cadena){
        $reult = "";
        $result = $cadena[0];
        for($i=1; $i<strlen($cadena);$i++) {
          if ($cadena[$i]==' ') {                        
            if($cadena[$i+1]!=' ') {
              $result = $result.$cadena[$i+1];
            }
          }
        }
        return $result;        
      }      
}