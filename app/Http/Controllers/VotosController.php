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
use App\Models\division;
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

class VotosController extends Controller
{ 
      public $RegxPag = 1000;
      public $edadminima = 12;

      public function __construct()
      {
        $this->middleware('auth');

        $this->middleware('role_or_permission:600_Votos', ['only' => ['index']]);
        $this->middleware('role_or_permission:600_Votos', ['only' => ['getVotos']]);
        $this->middleware('role_or_permission:600_Votos', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:600_Votos', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:600_Votos', ['only' => ['destroy']]);                
        $this->middleware('role_or_permission:600_Votos', ['only' => 'BuscarElFuncionario','VerCentroVotacion','ActualizarCentroVotacion']);                
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

          return view('votos.index', array('busqueda'=>'', 'objdependencia'=>$objdependencia, 'id_temp'=>'', 'objarea'=>$objarea, 'objdivision'=>$objdivision,'id_tempa'=>'0', 'id_tempd'=>'0'));

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
  
    public function getVotos(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');
          $id_dep = $request->id_dep;
          $id_divi = $request->id_divi;
          $id_area = $request->id_area;          
          $estatus = $request['estatus'];
          $estatusvoto = $request['estatusvoto'];

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($id_dep != 0) {
              $id_dep = Auth::user()->Dependencia->Region->id;
            }            
          }

          if ($estatusvoto=="Participo") { $valorvoto = 1; } else { $valorvoto = 0; }

          if ($estatus=="Todos") { $estatus = '';}
          //para jsindexversion1
          $seleccionado1 = "funcionarios.id";

          if (Auth::user()->hasAnyPermission('600_Votos')) {
            $ver1 = "funcionarios.id";
          } else {
            $ver1 = "0";
          }                                    

          if (Auth::user()->hasAnyPermission('600_Votos')) {
            $editar1 = "funcionarios.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('600_Votos')) {
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
            9 => 'borrar'            
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

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($estatus=='') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'),'funcionarios.voto', 'funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }

            if ($estatus=='Activo') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'));
            }

            if ($estatus=='Culminado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'));
            }

            if ($estatus=='Culminado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'));
            }

            if ($estatus=='No asignados') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }
          } else {
            if ($estatus=='') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }
            if ($estatus=='Activo') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo"))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Activo")) as id_divi'));
            }            
            if ($estatus=='Culminado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado"))="Culminado","Culminado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Culminado")) as id_divi'));
            }
            if ($estatus=='Jubilado') {
              $objfuncionario = funcionarios::select(DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado"))="Jubilado","Jubilado","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'),
              DB::raw('(select designacion.id_dep from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_dep'),
              DB::raw('(select designacion.id_area from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_area'),
              DB::raw('(select designacion.id_divi from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus = "Jubilado")) as id_divi'));
            }
            if ($estatus=='No asignado') {
              $objfuncionario = funcionarios::select(
              DB::raw($seleccionado1.' as seleccionado'), 'funcionarios.id','funcionarios.voto','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre',DB::raw('date_format(funcionarios.fecha_nac, "%d-%m-%Y") as fecha_nac'),'funcionarios.telefono_p',DB::raw('date_format(funcionarios.fecha_in, "%d-%m-%Y") as fecha_in'),
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'),
              DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));
            }                        
          }

          //Cuenta registros
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($estatus=='') {
              $totalregistros = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
              DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
              ->where('funcionarios.voto','=',$valorvoto)
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
            $search = $request->input('search.value');

            //filtering
            if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
              if ($estatus=='') {
                $totalFiltered = funcionarios::select('funcionarios.id','funcionarios.cedula','funcionarios.apellido','funcionarios.nombre','funcionarios.fecha_nac','funcionarios.telefono_p','funcionarios.fecha_in',
                DB::raw('(if ((select designacion.estatus from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> "" and designacion.created_at = (select max(created_at) from designacion where designacion.id_func = funcionarios.id and designacion.estatus <> ""))="Activo","Activo","")) as estatus'))
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)
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
                ->where('funcionarios.voto','=',$valorvoto)                
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

          //Carga los datos
            if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
              if ($estatus=='') {
                $objfuncionario
                ->where('funcionarios.voto','=',$valorvoto)                
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                 ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                });
              }

              if ($estatus=='Activo') {
                $objfuncionario
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)                
                ->where('funcionarios.id_dep',$operador,$valor)
                ->where(function ($query) use ($search) {
                $query->orWhere('funcionarios.cedula', 'like', '%'.$search.'%')
                  ->orWhere(DB::raw('concat(funcionarios.nombre, " ",funcionarios.apellido)'), 'like', '%'.$search.'%');
                });
              }

              if ($estatus=='Activo') {
                $objfuncionario
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)                
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
                ->where('funcionarios.voto','=',$valorvoto)                
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

          $objfuncionarios = $objfuncionario->distinct()->get()->toArray();

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

      public function ContarVotos(Request $request) {        
        if ($request->ajax()) {
            try {
              $obj = funcionarios::select(DB::raw('(count(voto)) as totalvoto'),'id_dep')
              ->where('voto','=','1')
              ->groupBy('id_dep')
              ->get()->toArray();

              //gerencia 1
              //san juan 2
              //san fernando 3
              //altagracia 4
              //valle de la pascua 5              
              $objc = [
                ['votos' => 0,'total' => 0],
                ['votos' => 0,'total' => 154],
                ['votos' => 0,'total' => 37],
                ['votos' => 0,'total' => 31],
                ['votos' => 0,'total' => 18],
                ['votos' => 0,'total' => 24]
              ];

              foreach ($obj as $key) {                
                $id_dep = $key['id_dep'];
                $objc[$id_dep]['votos'] = $key['totalvoto']; 
              }

              return response()->json($objc);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
      }

      /**
       * Show the form for creating a new resource.
       *
       * @return \Illuminate\Http\Response
      */

      public function create()
      {
        
      }       

      public function store(FuncionarioCreateRequest $request)
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
          try {

            $afectados = DB::table('funcionarios')->where('id','=',$id)
              ->update(
                [
                      'voto'=>'1',
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

            if ($afectados > 0) 
            { 
                Session::flash('message','Registro actualizado exitosamente'); 
            }        
            return redirect('/votos');

          } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Asociado a Otros Datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Eliminar el Registro');
            }

            return redirect('/votos');        
          }
      }  

      public function BuscarElFuncionarioVoto(Request $request){
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

      public function VerCentroVotacion(Request $request){
        if ($request->ajax()) {
            $id  = $request->id;

            try { 
              $objfuncionarios = funcionarios::select('centrovotacion')
              ->orwhere('id', '=', $id)
              ->get()->first()->toArray();

              return response()->json($objfuncionarios);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
      }      

      public function ActualizarCentroVotacion(Request $request){
        if ($request->ajax()) {
            $id  = $request->id;
            $centrovotacion = $request->centrovotacion;

            try { 
                $afectados = DB::table('funcionarios')->where('id','=',$id)
                ->update(
                [
                      'centrovotacion'=>strtoupper($centrovotacion),
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {              
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
  