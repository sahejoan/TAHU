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
use App\Models\adjuntos;
use App\Models\familiares;
use App\Models\dependencia;
use App\Models\designacion;
use App\Models\calcularedad;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\tipificaciones;

use App\Http\Requests\FuncionarioCreateRequest;
use App\Http\Requests\FuncionarioUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use ConvertirImagen; 
use Auth;
use Illuminate\Routing\Route;
use PDF;

class FuncionariosController extends Controller
{ 
      public $RegxPag = 1000;
      public $edadminima = 12;

      public function __construct()
      {
        $this->middleware('auth');

        $this->middleware('role_or_permission:10_Buscar_funcionarios|10_Ver_funcionarios|10_Crear_funcionarios|10_Editar_funcionarios|10_Borrar_funcionarios', ['only' => ['index']]);
        $this->middleware('role_or_permission:10_Funcionarios', ['only' => ['index']]);
        $this->middleware('role_or_permission:10_Ver_funcionarios', ['only' => ['VerRegion']]);
        $this->middleware('role_or_permission:10_Buscar_funcionarios', ['only' => ['BuscarRegion','getFuncionarios']]);
        $this->middleware('role_or_permission:10_Crear_funcionarios', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:10_Editar_funcionarios', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:10_Borrar_funcionarios', ['only' => ['destroy']]);                
        $this->middleware('role_or_permission:10_Adjuntar_archivos_funcionarios', ['only' => ['SubirArchivo','BorrarAdjuntos']]);        
        $this->middleware('role_or_permission:10_Descargar_archivos_funcionarios', ['only' => ['VerAdjuntos','DescargaAdjuntos']]);        

        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion', ['only' => 'BuscarElFuncionario']);                
      }  

      /**
       * Display a listing of the resource.
       *
       * @return \Illuminate\Http\Response
       */

      public function index()
      {
        try {
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
          //$objfuncionarios = funcionarios::orderBy('cedula','asc')->limit($this->RegxPag)->get();

          return view('funcionarios.index', array('busqueda'=>'', 'objdependencia'=>$objdependencia, 'id_temp'=>'', 'objarea'=>$objarea, 'objdivision'=>$objdivision,'id_tempa'=>'0', 'id_tempd'=>'0'));

        } catch (Exception $e) {
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Realiza La Consulta');
             }
             return redirect('/admin');
        }        
      }
  
    public function getFuncionarios(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');
          $id_dep = $request->id_dep;
          $id_divi = $request->id_divi;
          $id_area = $request->id_area;                                
          $estatus = $request['estatus'];
          if ($estatus=="Todos") { $estatus = '';}
          //para jsindexversion1
          $seleccionado1 = "funcionarios.id";

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($id_dep != 0) {
              $id_dep = Auth::user()->Dependencia->Region->id;
            }            
          }

          if (Auth::user()->hasAnyPermission('10_Ver_funcionarios')) {
            $ver1 = "funcionarios.id";
          } else {
            $ver1 = "0";
          }                                    

          if (Auth::user()->hasAnyPermission('10_Editar_funcionarios')) {
            $editar1 = "funcionarios.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('10_Borrar_funcionarios')) {
            $borrar1 = "funcionarios.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'seleccionado',
            1 => 'estatus',            
            2 => 'cedula',
            3 => 'apellido',            
            4 => 'nombre',            
            5 => 'fecha_nac',                        
            6 => 'telefono_p',            
            7 => 'fecha_in',            
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

          $objfuncionario =funcionarios::query();
//*
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($estatus=='') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }

            if ($estatus=='Activo') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'));
            }

            if ($estatus=='Culminado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'));
            }

            if ($estatus=='Culminado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'));
            }

            if ($estatus=='No asignados') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }
          } else {
            if ($estatus=='') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }
            if ($estatus=='Activo') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'));
            }            
            if ($estatus=='Culminado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'));
            }
            if ($estatus=='Jubilado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'));
            }
            if ($estatus=='No asignado') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }                        
          }

//*          
          //Cuenta registros
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($estatus=='') {
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->count('funcionarios.id');
            }

            if ($estatus=='Activo') {              
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'))
              ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),$operador,$valor)
              ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),$operadorarea,$id_area)                            
              ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'),$operadordivi,$id_divi)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->where(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))')
                ,'=','Activo'
              )                            
              ->count('funcionarios.id');              
            }     
            
            if ($estatus=='Culminado') {              
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'))
              ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operador,$valor)
              ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadorarea,$id_area)                            
              ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadordivi,$id_divi)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->where(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))')
                ,'=','Culminado'
              )                            
              ->count('funcionarios.id');              
            }                                                       

            if ($estatus=='Jubilado') {              
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'))
              ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operador,$valor)
              ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadorarea,$id_area)                            
              ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadordivi,$id_divi)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->where(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))')
                ,'=','Jubilado'
              )                            
              ->count('funcionarios.id');              
            } 

            if ($estatus=='No asignado') {
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->whereNull(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id))')
              )
              ->count('funcionarios.id');
            }
          } else {
            if ($estatus=='') {
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->count('funcionarios.id');
            }

            if ($estatus=='Activo') {              
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'))
              ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operador,$valor)
              ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadorarea,$id_area)                            
              ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadordivi,$id_divi)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->where(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))')
                ,'=','Activo'
              )                            
              ->count('funcionarios.id');
            }

            if ($estatus=='Culminado') {              
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'))
              ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operador,$valor)
              ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadorarea,$id_area)                            
              ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadordivi,$id_divi)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->where(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))')
                ,'=','Culminado'
              )              
              ->count('funcionarios.id');                           
            }

            if ($estatus=='Jubilado') {              
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'))
              ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operador,$valor)
              ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadorarea,$id_area)                            
              ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadordivi,$id_divi)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->where(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))')
                ,'=','Jubilado'
              )              
              ->count('funcionarios.id');                           
            }            

            if ($estatus=='No asignado') {              
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'))
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
              })
              ->whereNull(
                DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id))')
              )                                          
              ->count('funcionarios.id');                           
            }                        
          }

          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
//*            
            $search = $request->input('search.value');

            //filtering
            if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
              if ($estatus=='') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->count(DB::raw('funcionarios.id'));
              }

              if ($estatus=='Activo') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
                DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
                DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
                DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'))
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))')
                  ,'=','Activo'
                )
                ->count(DB::raw('funcionarios.id'));
              }

              if ($estatus=='Culminado') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
                DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
                DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
                DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'))
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))')
                  ,'=','Culminado'
                )
                ->count(DB::raw('funcionarios.id'));
              }              

              if ($estatus=='Jubilado') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'),
                DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
                DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
                DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'))
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))')
                  ,'=','Jubilado'
                )
                ->count(DB::raw('funcionarios.id'));
              }  

              if ($estatus=='No asignado') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->whereNull(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id))')
                )                                                          
                ->count(DB::raw('funcionarios.id'));
              }

            } else {
              if ($estatus=='') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->count(DB::raw('funcionarios.id'));
              }

              if ($estatus=='Activo') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
                DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
                DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
                DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'))
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))')
                  ,'=','Activo'
                )
                ->count(DB::raw('funcionarios.id'));
              }

              if ($estatus=='Culminado') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
                DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
                DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
                DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'))
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))')
                  ,'=','Culminado'
                )
                ->count(DB::raw('funcionarios.id'));              
              }

              if ($estatus=='Jubilado') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'),
                DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
                DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
                DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'))
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))')
                  ,'=','Jubilado'
                )
                ->count(DB::raw('funcionarios.id'));              
              }              

              if ($estatus=='No asignado') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->whereNull(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id))')
                )                                                                          
                ->count(DB::raw('funcionarios.id'));
              }

            }          
          }

//*
          //Carga los datos
            if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
              if ($estatus=='') {
                $objfuncionario
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                 ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                });
              }

              if ($estatus=='Activo') {
                $objfuncionario
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                  $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))')
                  ,'=','Activo'
                );
              }

              if ($estatus=='Culminado') {
                $objfuncionario
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                  $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))')
                  ,'=','Culminado'
                );
              }              

              if ($estatus=='Jubilado') {
                $objfuncionario
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                  $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))')
                  ,'=','Jubilado'
                );
              }   

              if ($estatus=='No asignado') {
                $objfuncionario
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->whereNull(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id))')
                );
              }

            } else {
              if ($estatus=='') {
                $objfuncionario
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                });
              }

              if ($estatus=='Activo') {
                $objfuncionario
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                  $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))')
                  ,'=','Activo'
                );
              }

              if ($estatus=='Culminado') {
                $objfuncionario
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                  $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))')
                  ,'=','Culminado'
                );
              }                                        

              if ($estatus=='Jubilado') {
                $objfuncionario
                ->where(DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operador,$valor)
                ->where(DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadorarea,$id_area)                            
                ->where(DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))'),$operadordivi,$id_divi)
                ->where(function ($query) use ($search) {
                  $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->where(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))')
                  ,'=','Jubilado'
                );
              }                                                      

              if ($estatus=='No asignado') {
                $objfuncionario
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                })
                ->whereNull(
                  DB::raw('(select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id))')
                );                
              }              
            }
         
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

          $objfuncionario->offset($start);
          $objfuncionario->limit($limit);
          $objfuncionario->orderBy($order, $dir);

          $objfuncionarios = $objfuncionario->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objfuncionarios;

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

      public function BuscarFuncionario(Request $request) {              
        try {
          $busqueda = $request->busqueda;
          $id_dep = $request->id_dep;

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
            ->where('dependencia.id_reg','=',Auth::user()->Dependencia->Region->id)
            ->orderBy('dependencia.nom_dep','asc')
            ->pluck('nombredependencia','id')->toArray();
          } else {
            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
            ->orderBy('dependencia.nom_dep','asc')
            ->pluck('nombredependencia','id')->toArray();            
          }

          if (count($objdependencia)>0) {                                
            $objdependencia = [''=>'Todos']+$objdependencia;                  
          } else {
            $objdependencia = [''=>'Todos'];
          }          

          $operador = '>';
          $valor = '0';
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $operador = '=';
            $valor = Auth::user()->id_dep;
          } else {
            if ($request->id_dep != "") {
              $operador = '=';
              $valor = $request->id_dep;
            } else {
              $operador = '>';
              $valor = '0';
            }
          }

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $objfuncionarios = funcionarios::select('funcionarios.*')
            ->where('funcionarios.id_dep',$operador,$valor)
            ->where(function($request) use ($busqueda) {
              $request->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
              ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
              ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');
            })
            ->orderBy('funcionarios.cedula','asc')
            ->limit($this->RegxPag)->get();
          } else {
            if ($id_dep=='') {
              $objfuncionarios = funcionarios::select('funcionarios.*')
              ->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
              ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
              ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%')            
              ->orderBy('funcionarios.cedula','asc')
              ->limit($this->RegxPag)->get();
            } else {
              $objfuncionarios = funcionarios::select('funcionarios.*')
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function($request) use ($busqueda) {
                $request->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');
              })
              ->orderBy('funcionarios.cedula','asc')
              ->limit($this->RegxPag)->get();
            }
          }         

          return view('funcionarios.index', array('objfuncionarios'=>$objfuncionarios, 'busqueda'=>$busqueda, 'objdependencia'=>$objdependencia, 'id_temp'=>$id_dep));                           

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Realiza La Consulta');
             }
             return redirect('/admin');
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
          $objtipificacion = new tipificaciones();
          $objcondicion_in = $objtipificacion->condiciones;
          $tempcondicion_id = "";

          $option_sexo = 'M';

          $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion, " - Region: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
          ->leftjoin('region','dependencia.id_reg','=','region.id')
          ->orderBy('dependencia.nom_dep','asc')
          ->limit($this->RegxPag)->pluck('nombredependencia','id')->toArray();

          if (count($objdependencia)>0) {                                
            $objdependencia = ['0'=>'Seleccionar']+$objdependencia;                  
          } else {
            $objdependencia = ['0'=>'Seleccionar'];
          }

          return view('funcionarios.crear', array('objcondicion_in'=>$objcondicion_in, 'tempcondicion_id'=>$tempcondicion_id, 'option_sexo'=>$option_sexo, 'objdependencia'=>$objdependencia));

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

      public function store(FuncionarioCreateRequest $request)
      {
        $predeterminado = "";
        if (file_exists ("images/fotografia.jpg")){ $predeterminado = "images/fotografia.jpg"; }        

        $data = $request->all();

        $data2 = $data;

        $fecha = $data2['fecha_nac']." 00:00:00";
        $dt = new DateTime($fecha);
        $data['fecha_nac'] = $dt->format('Y-m-d');

        $fecha = $data2['fecha_in']." 00:00:00";
        $dt = new DateTime($fecha);
        $data['fecha_in'] = $dt->format('Y-m-d');
        
        try {
            //inicio conversion imagen

            if ($data['capture']=='') {                                
              if (is_uploaded_file($_FILES['foto']['tmp_name'])) {                

                $ancho      = $data["ancho"];
                $alto       = $data["alto"];
                $tipoimagen = $data["tipoimagen"];
                $foto       = $data["foto"];
                
                if ($_FILES['foto']['type']=="image/jpg" || $_FILES['foto']['type']=="image/jpeg" || $_FILES['foto']['type']=="image/gif" || $_FILES['foto']['type']=="image/png") {          
                  // crea la variable para los extensiones de archivo
                  if ($_FILES['foto']['type']=="image/jpg"){ $ext = ".jpg";}
                  if ($_FILES['foto']['type']=="image/jpeg"){ $ext = ".jpeg";}
                  if ($_FILES['foto']['type']=="image/png"){ $ext = ".png";}
                  if ($_FILES['foto']['type']=="image/gif"){ $ext = ".gif";}

                  //desde aqui mueve y convierte en tamaño la imagen que se va a guardar
                  $destino = "tmpimagen/";

                  $idusuario = Auth::user()->id;

                  move_uploaded_file($_FILES ['foto']['tmp_name'], $destino."eU".$idusuario.$ext);

                  $imagenoriginal = $destino."eU".$idusuario.$ext;

                  $imagen_final = $imagenoriginal;

                  $tamanio = getimagesize($imagen_final);                                
                  if ($tamanio[0]>1000 || $tamanio>1000) {
                    $ancho_nuevo = 1000;
                    $alto_nuevo = 1000;
                    $convimg = new ConvertirImagen();
                    $convimg->redim ($imagenoriginal, $imagen_final, $ancho_nuevo, $alto_nuevo);
                  }

                  $info = getimagesize($imagen_final);
                  $data['foto'] = file_get_contents($imagen_final);

                  $data['alto'] = $info[1];
                  $data['ancho'] = $info[0];
                  $data['tipoimagen'] = $_FILES['foto']['type'];

                  $data2['foto'] = file_get_contents($imagen_final);

                  $data2['alto'] = $info[1];
                  $data2['ancho'] = $info[0];
                  $data2['tipoimagen'] = $_FILES['foto']['type'];                      

                  $data['nombre'] = strtoupper($data['nombre']);
                  $data['apellido'] = strtoupper($data['apellido']);

                  funcionarios::create($data);           

                  $ultimoid = DB::getPdo()->lastInsertId(); 

                  unlink($imagen_final);

                  Session::flash('message','El registro se creó con éxito');            
                  return redirect('/funcionarios');
                  //return redirect('/funcionarios/'.$ultimoid.'/edit');                                    

                } else { 

                  return redirect::route('empleado.create')->withInput()->withErrors("Debe Seleccionar La Imagen");

                }

              } else {

                $destino = "tmpimagen/";

                $idusuario = Auth::user()->id;
                $imagenoriginal = $destino."eU".$idusuario.'.jpg';

                copy($predeterminado, $imagenoriginal);

                $imagen_final = $imagenoriginal;
                $tamanio = getimagesize($imagen_final);

                if ($tamanio[0]>1000 || $tamanio>1000) {
                  $ancho_nuevo = 1000;
                  $alto_nuevo = 1000;
                  $convimg = new ConvertirImagen();
                  $convimg->redim ($imagenoriginal, $imagen_final, $ancho_nuevo, $alto_nuevo);
                }

                $info = getimagesize($imagen_final);
                $data['foto'] = file_get_contents($imagen_final);

                $data['alto'] = $info[1];
                $data['ancho'] = $info[0];
                $data['tipoimagen'] = 'image/jpg';              

                $data2['foto'] = file_get_contents($imagen_final);

                $data2['alto'] = $info[1];
                $data2['ancho'] = $info[0];
                $data2['tipoimagen'] = $_FILES['foto']['type'];                      

                $data['nombre'] = strtoupper($data['nombre']);
                $data['apellido'] = strtoupper($data['apellido']);

                funcionarios::create($data);

                $ultimoid = DB::getPdo()->lastInsertId(); 

                unlink($imagen_final);

                Session::flash('message','Registro se creo con éxito');
                return redirect('/funcionarios');
                //return redirect('/funcionarios/'.$ultimoid.'/edit');
              }

            } else {

              $data['foto'] = $data2['capture'];

              $data['alto'] = 240;
              $data['ancho'] = 320;
              $data['tipoimagen'] = 'capture/jpeg';

              $data['nombre'] = strtoupper($data['nombre']);
              $data['apellido'] = strtoupper($data['nombre']);

              funcionarios::create($data);

              $ultimoid = DB::getPdo()->lastInsertId(); 

              //Session::flash('message','Registro se creo con éxito');
              //return redirect('/funcionarios/'.$ultimoid.'/edit');

              Session::flash('message','Registro se creo con éxito');
              return redirect('/funcionarios');
            }                     
            //fin conversion imagen

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo crear el registro');
             }

             return redirect()->back()->withInput($data2);                            
             //return redirect('/funcionarios');
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
            $objfuncionarios = funcionarios::find($id);

            $objtipificacion = new tipificaciones();

            $objcondicion = $objtipificacion->condiciones;
            $objparentesco = $objtipificacion->parentesco;
            $tempparentesco_id = "";

            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion, " - Region: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
            ->leftjoin('region','dependencia.id_reg','=','region.id')
            ->orderBy('dependencia.nom_dep','asc')
            ->limit($this->RegxPag)->pluck('nombredependencia','id')->toArray();

            if (count($objdependencia)>0) {                                
              $objdependencia = ['0'=>'Seleccionar']+$objdependencia;                  
            } else {
              $objdependencia = ['0'=>'Seleccionar'];
            }

            if (isset($objfuncionarios)) { 
              $idusuario = Auth::user()->id;                                   

              Session::put('dateconcurrente', $objfuncionarios['updated_at']);
              Session::save();

              $option_sexo = $objfuncionarios['sexo'];

              return view('funcionarios.editar', array('objfuncionarios'=>$objfuncionarios, 'objcondicion'=>$objcondicion, 'idusuario'=>$idusuario, 'option_sexo'=>$option_sexo, 'objparentesco'=>$objparentesco, 'tempparentesco_id'=>$tempparentesco_id, 'objdependencia'=>$objdependencia));
            }                  

          } catch (Exception $e) {                    
            Session::flash('message-error','Solicitud Invalida');               
            return redirect('/funcionarios');
          }
        
          return redirect('/funcionarios');
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
          $request = new FuncionarioUpdateRequest();       //Instancia de la clase que valida        
          $request->setId($id);                             //Se indica el valor del id del registro que se va actualizar

          $data = $input->all();                            //Datos recuperados desde el formulario con request  
          
        try {

            $objfuncionarios = funcionarios::find($id);       //Se busca el registro
            
            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              //return redirect::route('funcionarios.edit',[$objfuncionarios->id])->withInput()->withErrors($validation->messages());
              return redirect()->back()->withInput($data)->withErrors($validation->messages());
              //return back()->withInput($data)->withErrors($validation->messages());

            } else {

              if ($data['capture']==''){                 
                if (is_uploaded_file($_FILES['foto']['tmp_name'])) {                
                  $ancho      = $data["ancho"];
                  $alto       = $data["alto"];
                  $tipoimagen = $data["tipoimagen"];
                  $foto       = $data["foto"];

                  if ($_FILES['foto']['type']=="image/jpg" || $_FILES['foto']['type']=="image/jpeg" || $_FILES['foto']['type']=="image/gif" || $_FILES['foto']['type']=="image/png") {          
                    //crea la variable para la extension del archivo
                    if ($_FILES['foto']['type']=="image/jpg"){ $ext = ".jpg";}
                    if ($_FILES['foto']['type']=="image/jpeg"){ $ext = ".jpeg";}
                    if ($_FILES['foto']['type']=="image/png"){ $ext = ".png";}
                    if ($_FILES['foto']['type']=="image/gif"){ $ext = ".gif";}
                                
                    $destino = "tmpimagen/";

                    $idusuario = Auth::user()->id;

                    move_uploaded_file($_FILES ['foto']['tmp_name'], $destino."eU".$idusuario.$ext);

                    $imagenoriginal = $destino."eU".$idusuario.$ext;

                    $imagen_final = $imagenoriginal;
                                
                    $tamanio = getimagesize($imagen_final);  

                    if ($tamanio[0]>1000 || $tamanio>1000) {
                      $ancho_nuevo = 1000;
                      $alto_nuevo = 1000;

                      $convimg = new ConvertirImagen();
                      $convimg->redim ($imagenoriginal, $imagen_final, $ancho_nuevo, $alto_nuevo);
                    }

                    $info = getimagesize($imagen_final);
                    $data['foto'] = file_get_contents($imagen_final);

                    $data['alto'] = $info[1];
                    $data['ancho'] = $info[0];
                    $data['tipoimagen'] = $_FILES['foto']['type'];                     

                    $afectados = DB::table('funcionarios')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                    ->update(
                    [
                      'cedula'=>$data['cedula'],
                      'rif'=>$data['rif'],
                      'nombre'=>strtoupper($data['nombre']),
                      'apellido'=>strtoupper($data['apellido']),
                      'fecha_in'=>DB::raw("STR_TO_DATE('".$data['fecha_in']."','%Y-%m-%d')"),                      
                      'fecha_nac'=>DB::raw("STR_TO_DATE('".$data['fecha_nac']."','%Y-%m-%d')"),
                      'correo_i'=>$data['correo_i'],
                      'correo_alt'=>$data['correo_alt'],
                      'twitter'=>$data['twitter'],
                      'telefono_p'=>$data['telefono_p'],
                      'telefono_cont'=>$data['telefono_cont'],
                      'sexo'=>$data['sexo'],
                      'direccion'=>$data['direccion'],
                      'condicion_in'=>$data['condicion_in'],
                      'id_dep'=>$data['id_dep'],
                      'tipo_s' =>$data['tipo_s'],
                      'alergia'=>$data['alergia'],
                      'alto'=>$data['alto'],
                      'ancho'=>$data['ancho'],
                      'tipoimagen'=>$data['tipoimagen'],
                      'foto'=>$data['foto'],                      
                      'updated_at'=>DB::raw('NOW()')
                    ]
                    );

                    unlink($imagen_final);

                    if ($afectados == 1) {
                      Session::flash('message','Actualización exitosa');
                      return redirect('/funcionarios');
                    } else {
                      $objf = funcionarios::find($id);
                      if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/funcionarios');
                      } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/funcionarios');
                      }
                    }

                  } else {          
                    return redirect::route('funcionarios.edit')->withInput()->withErrors("No ha seleccionado la imagen");
                  }

                } else {

                  if(($data["tipoimagen"]=='capture/jpeg') || ($data["tipoimagen"]=='')) {
                  } else {
                    if ($data["tipoimagen"]=="image/jpg"){ $ext = ".jpg";}
                    if ($data["tipoimagen"]=="image/jpeg"){ $ext = ".jpeg";}
                    if ($data["tipoimagen"]=="image/png"){ $ext = ".png";}
                    if ($data["tipoimagen"]=="image/gif"){ $ext = ".gif";}

                    $destino = "tmpimagen/";

                    $idusuario = Auth::user()->id;

                    $imagen_final = $destino."eU".$idusuario.$ext;
                  }

                  $afectados = DB::table('funcionarios')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                  ->update(
                    [
                      'cedula'=>$data['cedula'],
                      'rif'=>$data['rif'],
                      'nombre'=>strtoupper($data['nombre']),
                      'apellido'=>strtoupper($data['apellido']),
                      'fecha_in'=>DB::raw("STR_TO_DATE('".$data['fecha_in']."','%Y-%m-%d')"),                      
                      'fecha_nac'=>DB::raw("STR_TO_DATE('".$data['fecha_nac']."','%Y-%m-%d')"),
                      'correo_i'=>$data['correo_i'],
                      'correo_alt'=>$data['correo_alt'],
                      'twitter'=>$data['twitter'],
                      'telefono_p'=>$data['telefono_p'],
                      'telefono_cont'=>$data['telefono_cont'],
                      'sexo'=>$data['sexo'],
                      'direccion'=>$data['direccion'],
                      'condicion_in'=>$data['condicion_in'],
                      'tipo_s' =>$data['tipo_s'],
                      'alergia'=>$data['alergia'],                      
                      'id_dep'=>$data['id_dep'],
                      //'alto'=>$data['alto'],
                      //'ancho'=>$data['ancho'],
                      //'tipoimagen'=>$data['tipoimagen'],                      
                      //'foto'=>$data['foto'],                      
                      'updated_at'=>DB::raw('NOW()')
                  ]
                  );
        
                  if(($data["tipoimagen"]=='capture/jpeg') || ($data["tipoimagen"]=='')) {
                  } else {
                     unlink($imagen_final);
                  }

                  if ($afectados == 1) {
                    Session::flash('message','Actualización éxitosa');
                    return redirect('/funcionarios');
                  } else {
                    $objd = funcionarios::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                      Session::flash('message','Actualización exitosa');
                      return redirect('/funcionarios');
                    } else {
                      Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicha información, vuelva a realizar la operación para ver los datos nuevos');
                      return redirect('/empleado');
                    }
                  }
                }

              } else {

                if(($data["tipoimagen"]=='capture/jpeg') || ($data["tipoimagen"]=='')){
                } else {
                  if ($data["tipoimagen"]=="image/jpg"){ $ext = ".jpg";}
                  if ($data["tipoimagen"]=="image/jpeg"){ $ext = ".jpeg";}
                  if ($data["tipoimagen"]=="image/png"){ $ext = ".png";}
                  if ($data["tipoimagen"]=="image/gif"){ $ext = ".gif";}

                  $destino = "tmpimagen/";

                  $idusuario = Auth::user()->id;

                  $imagen_final = $destino."eU".$idusuario.$ext;
                }

                $data['foto'] = $data['capture'];

                $data['alto'] = 320;
                $data['ancho'] = 240;
                $data['tipoimagen'] = "capture/jpeg";

                $afectados = DB::table('funcionarios')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                    [
                      'cedula'=>$data['cedula'],
                      'rif'=>$data['rif'],
                      'nombre'=>strtoupper($data['nombre']),
                      'apellido'=>strtoupper($data['apellido']),
                      'fecha_in'=>DB::raw("STR_TO_DATE('".$data['fecha_in']."','%Y-%m-%d')"),
                      'fecha_nac'=>DB::raw("STR_TO_DATE('".$data['fecha_nac']."','%Y-%m-%d')"),
                      'correo_i'=>$data['correo_i'],
                      'correo_alt'=>$data['correo_alt'],
                      'twitter'=>$data['twitter'],
                      'telefono_p'=>$data['telefono_p'],
                      'telefono_cont'=>$data['telefono_cont'],
                      'sexo'=>$data['sexo'],
                      'direccion'=>$data['direccion'],
                      'condicion_in'=>$data['condicion_in'],
                      'tipo_s' =>$data['tipo_s'],
                      'alergia'=>$data['alergia'],                      
                      'id_dep'=>$data['id_dep'],
                      'alto'=>$data['alto'],
                      'ancho'=>$data['ancho'],
                      'tipoimagen'=>$data['tipoimagen'],
                      'foto'=>$data['foto'],                      
                      'updated_at'=>DB::raw('NOW()')
                   ]);

                if(($data["tipoimagen"]=='capture/jpeg') || ($data["tipoimagen"]=='')){
                } else {
                  unlink($imagen_final);
                }
        
                if ($afectados == 1) {
                  Session::flash('message','Actualización exitosa');
                  return redirect('/funcionarios');
                } else {
                  $objf = funcionarios::find($id);
                  if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                    Session::flash('message','Actualización exitosa');
                    return redirect('/funcionarios');
                  } else {
                    Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicha información, vuelva a realizar la operación para ver los datos nuevos');
                    return redirect('/funcionarios');
                  }
                }
              } 

            }

         } catch (Exception $e) {    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'Intenta escribir un valor que está registrado.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {                
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Actualizar el Registro');
             }

             return redirect('/funcionarios');
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

            $afectados =  funcionarios::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }        
            return redirect('/funcionarios');

          } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Asociado a Otros Datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Eliminar el Registro');
            }

            return redirect('/funcionarios');        
          }
      }

      public function VerFotoFuncionario(Request $request)
      {
        if ($request->ajax()) {
          $id = $request->id;          

          try {                  
            $result = funcionarios::buscarfotofuncionario($id);
            $tipoimagen = $result['tipoimagen'];
            $foto = $result['foto'];
            $urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
            $result['foto'] = $urlfoto;
            return response()->json($result);              
          } catch (Exception $e) {    
            return response()->json("Error");
          }
        } else {
          return response()->json("Error");              
        }  
      }

      public function VerFamiliares(Request $request)
      {

        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');
          $id_func = $request->id_func;

          if (Auth::user()->hasAnyPermission('10_Editar_funcionarios')) {
            $editar1 = "familiares.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('10_Borrar_funcionarios')) {
            $borrar1 = "familiares.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'cedula',
            1 => 'apellido',            
            2 => 'nombre',            
            3 => 'fecha_nac',                        
            4 => 'edad',            
            5 => 'parentesco',            
            6 => 'editar',
            7 => 'borrar'            
          );

          $objfamiliar =familiares::query();

          $objfamiliar = familiares::select('familiares.id','familiares.cedula','familiares.apellido','familiares.nombre',DB::raw('date_format(familiares.fecha_nac, "%d-%m-%Y") as fecha_nacf'),'familiares.fecha_nac','familiares.parentesco',
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));

          //Cuenta registros
          $totalregistros = familiares::select('familiares.id')
          ->where('familiares.id_func','=',$id_func)
          ->count('familiares.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = familiares::select('familiares.id')
            ->where('familiares.id_func','=',$id_func)
            ->count(DB::raw('familiares.id'));
          }

          //Carga los datos
          $objfamiliar
          ->where('familiares.id_func','=',$id_func);

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
            $order = 'familiares.cedula';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objfamiliar->offset($start);
          $objfamiliar->limit($limit);
          $objfamiliar->orderBy($order, $dir);

          $objfamiliares = $objfamiliar->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objfamiliares;

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

    public function BuscarElFuncionario(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try { 
              $objfuncionarios = funcionarios::select('cedula', 'nombre', 'apellido', 'id')
              ->orwhere('cedula', 'like', '%'. $b . '%')
              ->orwhere( 'nombre', 'like', '%' . $b . '%')
              ->orwhere( 'apellido', 'like', '%' . $b . '%')                                   
              ->orderBy('nombre','asc')
              ->orderBy('apellido','asc')
              ->limit($this->RegxPag)->get()->toArray();

              return response()->json($objfuncionarios);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }

    public function ImprimirCargaFamiliar(Request $request) 
    {              
        try{ 
            $calcularedad = new calcularedad();           
            if(isset($request->id_dep_)){
              if ($request->id_dep_=="") {
                $nombredependencia = "";                            
              } else {
                $objdependencia = dependencia::find($request->id_dep_);
                $nombredependencia = $objdependencia->nom_dep." - ".$objdependencia->ubicacion;              
              }
            } else {              
              $nombredependencia = "";                            
            }

          $busqueda = $request->ibusqueda;
          $id_dep = $request->id_dep_;

          $operador = '>';
          $valor = '0';
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $operador = '=';
            $valor = Auth::user()->id_dep;
          } else {
            if ($request->id_dep != "") {
              $operador = '=';
              $valor = $request->id_dep;
            } else {
              $operador = '>';
              $valor = '0';
            }
          }

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
              $objfuncionarios = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido',DB::raw('(ROW_NUMBER() OVER(ORDER BY funcionarios.cedula)) AS numreg'))
              ->with('familiares')
              ->whereHas('familiares')              
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function($request) use ($busqueda) {
                $request->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');
              })
              ->groupBy('id','cedula','nombre','apellido')              
              ->orderBy('funcionarios.cedula','asc')
              ->get()->toArray();
          } else {
            if ($id_dep=='') {
              $objfuncionarios = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido', DB::raw('(ROW_NUMBER() OVER(ORDER BY cedula)) AS numreg'))
              ->with('familiares')
              ->whereHas('familiares')              
              ->where(function ($query) use ($busqueda) {
                $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');            
              })
              ->groupBy('id','cedula','nombre','apellido')
              ->orderBy('funcionarios.cedula','asc')
              ->get()->toArray();
            } else {
              $objfuncionarios = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido',DB::raw('(ROW_NUMBER() OVER(ORDER BY funcionarios.cedula)) AS numreg'))
              ->with('familiares')
              ->whereHas('familiares')              
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function ($query) use ($busqueda) {
                $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');            
              })
              ->groupBy('id','cedula','nombre','apellido')              
              ->orderBy('funcionarios.cedula','asc')
              ->get()->toArray();
            }
          }

          return PDF::loadView('funcionarios.imprimircargafamiliar', ['objfuncionarios' =>$objfuncionarios, 'nombredependencia'=>$nombredependencia, 'calcularedad'=>$calcularedad])->setPaper("A4", "protraid")->set_option("isPhpEnabled", true)->stream(); //setPaper("8.5x14", "landscape")->stream();
           
        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Realiza La Consulta');
             }
             return redirect('/admin');
        }         
    }           

    public function ImprimirHijos12(Request $request) 
    {              
        try{ 
            $calcularedad = new calcularedad();           
            if(isset($request->id_dep_2)){
              if ($request->id_dep_2=="") {
                $nombredependencia = "";                            
              } else {
                $objdependencia = dependencia::find($request->id_dep_2);
                $nombredependencia = $objdependencia->nom_dep." - ".$objdependencia->ubicacion;              
              }
            } else {              
              $nombredependencia = "";                            
            }

          $busqueda = $request->ibusqueda2;
          $id_dep = $request->id_dep_2;

          $operador = '>';
          $valor = '0';
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $operador = '=';
            $valor = Auth::user()->id_dep;
          } else {
            if ($request->id_dep != "") {
              $operador = '=';
              $valor = $request->id_dep;
            } else {
              $operador = '>';
              $valor = '0';
            }
          }

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
              $objfuncionarios = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido',DB::raw('(ROW_NUMBER() OVER(ORDER BY funcionarios.cedula)) AS numreg'))
              ->with(['familiares'=>function($query) {
                $query->select('*', DB::raw('TIMESTAMPDIFF(YEAR,familiares.fecha_nac,CURDATE()) AS edad'))->where(DB::raw('TIMESTAMPDIFF(YEAR,fecha_nac,CURDATE())'),'<',$this->edadminima);
              }])
              ->whereHas('familiares',function ($query) {
                $query->where(DB::raw('TIMESTAMPDIFF(YEAR,fecha_nac,CURDATE())'),'<',$this->edadminima); 
              })              
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function($request) use ($busqueda) {
                $request->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');
              })
              ->groupBy('id','cedula','nombre','apellido')              
              ->orderBy('funcionarios.cedula','asc')
              ->get()->toArray();
          } else {
            if ($id_dep=='') {
              $objfuncionarios = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido', DB::raw('(ROW_NUMBER() OVER(ORDER BY cedula)) AS numreg'))
              ->with(['familiares'=>function($query) {
                $query->select('*', DB::raw('TIMESTAMPDIFF(YEAR,familiares.fecha_nac,CURDATE()) AS edad'))->where(DB::raw('TIMESTAMPDIFF(YEAR,fecha_nac,CURDATE())'),'<',$this->edadminima);
              }])
              ->whereHas('familiares',function ($query) {
                $query->where(DB::raw('TIMESTAMPDIFF(YEAR,fecha_nac,CURDATE())'),'<',$this->edadminima); 
              })              
              ->where(function ($query) use ($busqueda) {
                $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');            
              })
              ->groupBy('id','cedula','nombre','apellido')
              ->orderBy('funcionarios.cedula','asc')
              ->get()->toArray();
            } else {
              $objfuncionarios = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido',DB::raw('(ROW_NUMBER() OVER(ORDER BY funcionarios.cedula)) AS numreg'))
              ->with(['familiares'=>function($query) {
                $query->select('*', DB::raw('TIMESTAMPDIFF(YEAR,familiares.fecha_nac,CURDATE()) AS edad'))->where(DB::raw('TIMESTAMPDIFF(YEAR,fecha_nac,CURDATE())'),'<',$this->edadminima);
              }])
              ->whereHas('familiares',function ($query) {
                $query->where(DB::raw('TIMESTAMPDIFF(YEAR,fecha_nac,CURDATE())'),'<',$this->edadminima); 
              })              
              ->where('funcionarios.id_dep',$operador,$valor)
              ->where(function ($query) use ($busqueda) {
                $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');            
              })
              ->groupBy('id','cedula','nombre','apellido')              
              ->orderBy('funcionarios.cedula','asc')
              ->get()->toArray();
            }
          }

          return PDF::loadView('funcionarios.imprimirhijos12', ['objfuncionarios' =>$objfuncionarios, 'nombredependencia'=>$nombredependencia, 'calcularedad'=>$calcularedad])->setPaper("A4", "protraid")->set_option("isPhpEnabled", true)->stream(); //setPaper("8.5x14", "landscape")->stream();
           
        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Realiza La Consulta');
             }
             return redirect('/admin');
        }         
    }    


    public function SubirArchivo(Request $request) {

      try {
        if (isset($_FILES['adjuntararchivox'])) {

            if ($_FILES['adjuntararchivox']['type']=="application/pdf" 
              || $_FILES['adjuntararchivox']['type']=="application/msword"
              || $_FILES['adjuntararchivox']['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
              || $_FILES['adjuntararchivox']['type']=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"              
              || $_FILES['adjuntararchivox']['type']=="application/vnd.oasis.opendocument.presentation"
              || $_FILES['adjuntararchivox']['type']=="application/vnd.oasis.opendocument.spreadsheet"
              || $_FILES['adjuntararchivox']['type']=="application/vnd.oasis.opendocument.text"
              || $_FILES['adjuntararchivox']['type']=="application/vnd.ms-powerpoint"
              || $_FILES['adjuntararchivox']['type']=="application/vnd.ms-excel"
              || $_FILES['adjuntararchivox']['type']=="application/zip"
              || $_FILES['adjuntararchivox']['type']=="application/rar"              
              || $_FILES['adjuntararchivox']['type']=="application/rar"
              || $_FILES['adjuntararchivox']['type']=="image/jpeg"              
              || $_FILES['adjuntararchivox']['type']=="image/png"              
              || $_FILES['adjuntararchivox']['type']=="image/jpg"              
              || $_FILES['adjuntararchivox']['type']=="image/bmp"              
              ) { 

                if ($_FILES['adjuntararchivox']['type']=="image/jpg"){ $ext = ".jpg";}
                if ($_FILES['adjuntararchivox']['type']=="image/jpeg"){ $ext = ".jpeg";}
                if ($_FILES['adjuntararchivox']['type']=="image/png"){ $ext = ".png";}
                if ($_FILES['adjuntararchivox']['type']=="image/bmp"){ $ext = ".bmp";}
                if ($_FILES['adjuntararchivox']['type']=="application/pdf"){ $ext = ".pdf";}
                if ($_FILES['adjuntararchivox']['type']=="application/vnd.oasis.opendocument.presentation"){ $ext = ".odp";}
                if ($_FILES['adjuntararchivox']['type']=="application/vnd.oasis.opendocument.spreadsheet"){ $ext = ".ods";}
                if ($_FILES['adjuntararchivox']['type']=="application/vnd.oasis.opendocument.text"){ $ext = ".odt";}                                
                if ($_FILES['adjuntararchivox']['type']=="application/msword"){ $ext = ".doc";}
                if ($_FILES['adjuntararchivox']['type']=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){ $ext = ".docx";}            
                if ($_FILES['adjuntararchivox']['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){ $ext = ".xlsx";}                                            
                if ($_FILES['adjuntararchivox']['type']=="application/vnd.ms-powerpoint"){ $ext = ".ppt";}
                if ($_FILES['adjuntararchivox']['type']=="application/vnd.ms-excel"){ $ext = ".xls";}
                if ($_FILES['adjuntararchivox']['type']=="application/zip"){ $ext = ".zip";}
                if ($_FILES['adjuntararchivox']['type']=="application/zip"){ $ext = ".rar";}

                $obj = null;
                if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
                  $obj = funcionarios::select('cedula')->where('id','=',$request['idupload_func'])->where('id_dep','=',Auth::user()->id_dep)->get()->first()->toArray();                  
                } else {
                  $obj = funcionarios::select('cedula')->where('id','=',$request['idupload_func'])->get()->first()->toArray();
                }

                if ($obj == null) {
                  $res = "Error";
                } else {
                  $des = ['id_func'=>'', 'nom_archivo'=>''];
                  $des["nom_archivo"] = $_FILES['adjuntararchivox']['name'];
                  $des["id_func"] = $request['idupload_func'];                
                  $destino = env('APP_CARPETA_ADJUNTOS');

                  $carpeta = $destino.trim($obj['cedula']);

                  if (!file_exists($carpeta)) {
                    mkdir($carpeta, 0777, true);
                  }

                  $obja = adjuntos::select('nom_archivo')->where('id_func','=',$request['idupload_func'])->where('nom_archivo', 'like', '%'.$_FILES['adjuntararchivox']['name'].'%')->get()->toArray();

                  if (count($obja)>0) {
                    $res = "Archivo Existe";
                  } else {
                    if (move_uploaded_file($_FILES['adjuntararchivox']['tmp_name'], $carpeta.'/'.$_FILES['adjuntararchivox']['name'])){
                      adjuntos::create($des);
                      $res = "Archivo Agregado";
                    }else{
                      $res = "Archivo No Se Pudo Agregar";
                    }
                  }                 
                }                
            } else {
              $res = "Formato de Archivo No Permitido";
            }
        } else {
          $res = "No Hay Archivo Seleccionado";
        }

        return response()->json($res);

      } catch (Exception $e) {                            
        return response()->json('Error');        
      }

    }

  public function VerAdjuntos(Request $request){
    try {
      if ($request->ajax()) {

        $id = $request->id;

        $objf = null;
        if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
          $objf = funcionarios::select('cedula')->where('id','=',$id)->where('id_dep','=',Auth::user()->id_dep)->get()->first()->toArray();                  
        } else {
          $objf = funcionarios::select('cedula')->where('id','=',$id)->get()->first()->toArray();
        }

        if ($objf == null) {
          return response()->json('Error');
        } else {
          $obj = adjuntos::where('id_func','=',$id)->get();              
          return response()->json($obj);
        }

      }
    } catch (Exception $e) {                            
       return response()->json('Error');        
    }
  }

  public function BorrarAdjuntos(Request $request){
    try {
      if ($request->ajax()) {
        $objf = null;

        $id = $request->id;
        $obj = adjuntos::where('id','=',$id)->get()->first()->toArray();      

        if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
          $objf = funcionarios::select('cedula')->where('id','=',$obj['id_func'])->where('id_dep','=',Auth::user()->id_dep)->get()->first()->toArray();
        } else {
          $objf = funcionarios::select('cedula')->where('id','=',$obj['id_func'])->get()->first()->toArray();
        }

        if ($objf == null) {
          return response()->json("Error");
        } else {
          $destino = env('APP_CARPETA_ADJUNTOS').trim($objf['cedula']).'/'; 
          unlink($destino.$obj['nom_archivo']);
          $afectados =  adjuntos::destroy($id);                
          return response()->json("Ok");
        }

      }
    } catch (Exception $e) {                            
       return response()->json('Error');        
    }    
  }
   
  public function DescargaAdjuntos(Request $request, $id) {
    try {
        $obj = adjuntos::where('id','=',$id)->get()->first()->toArray();
        $objf = null;
        if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
          $objf = funcionarios::select('cedula')->where('id','=',$obj['id_func'])->where('id_dep','=',Auth::user()->id_dep)->get()->first()->toArray();
        } else {
          $objf = funcionarios::select('cedula')->where('id','=',$obj['id_func'])->get()->first()->toArray();
        }

        if ($objf == null) {
          return response()->json('Error');        
        } else {

          $archivo = $obj['nom_archivo'];
          $destino = env('APP_CARPETA_ADJUNTOS').trim($objf['cedula']).'/'; 
          $filename = $destino . $archivo;
          $cadena = $obj['nom_archivo'];
          $separador = ".";
          $separada = explode($separador, $cadena);
    
          $ext = '.'.$separada[count($separada)-1];

            if ($ext==".jpg") {  $h = "image/jpg"; }
            if ($ext==".jpeg") { $h = "image/jpeg"; }
            if ($ext==".png") {  $h = "image/png"; }
            if ($ext==".bmp") {  $h = "image/bmp"; }
            if ($ext==".pdf") {  $h = "application/pdf"; }
            if ($ext==".mpeg") { $h = "video/mpeg"; }
            if ($ext==".mp4") { $h = "video/mp4"; }
            if ($ext==".odp") {  $h = "application/vnd.oasis.opendocument.presentation"; }
            if ($ext==".ods") {  $h = "application/vnd.oasis.opendocument.spreadsheet"; }
            if ($ext==".odt") {  $h = "application/vnd.oasis.opendocument.text"; }
            if ($ext==".doc") {  $h = "application/msword"; }
            if ($ext==".ppt") {  $h = "application/vnd.ms-powerpoint"; }
            if ($ext==".xls") {  $h = "application/vnd.ms-excel"; }
            if ($ext==".docx") {  $h = "application/vnd.openxmlformats-officedocument.wordprocessingml.document"; }
            if ($ext==".xlsx") {  $h = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; }            
            if ($ext==".zip") {  $h = "application/zip"; }
            if ($ext==".rar") {  $h = "application/rar"; }
            if ($ext==".rar") {  $h = "application/octet-stream"; }

            $outname=$archivo;          
            $headers = [
                        'Content-Type: '.$h,
                        'Expires: -1',
                        'Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT',
                        'Content-Transfer-Encoding: binary',
                        'Cache-Control: no-store, no-cache, must-revalidate',
                        'Pragma: no-cache',
                        'Content-Disposition: attachment; filename='.$outname.";\n\n"
                       ];
           
            return response()->download($filename, $archivo, $headers);

        }
 
    } catch (Exception $e) {                            
       return response()->json('Error');        
    }                
  }
}
  