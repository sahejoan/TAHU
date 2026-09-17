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

class scan2BusquedaController extends Controller
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
        $this->middleware('role_or_permission:12_Capturar_Asistencia', ['only' => ['index','VerDatosFuncionario','VerDatosFuncionario2','RegistrarDatosFuncionario','VerDatosFuncionario8','RegistrarDatosFuncionario8','VerDatosFuncionario82']]);        
        $this->middleware('role_or_permission:12_Listar_Asistencia', ['only' => ['BuscarAsistencia','ImprimirAsistencia']]);        
        $this->middleware('role_or_permission:12_Registrar_Permiso_Asistencia', ['only' => ['RegistrarPermiso']]);                
    }  

    public function index(Request $request)
    {    
        try {
          $objhoraactual = DB::select('select now() as horaactual;');          
          $horaactual = $objhoraactual[0]->horaactual;

          $objfechacarbon = new fechacarbon();

          $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
          ->where('dependencia.id_reg','=',Auth::user()->Dependencia->Region->id)
          ->orderBy('dependencia.nom_dep','asc')
          ->pluck('nombredependencia','id')->toArray();

          $objdivision = division::select('id','descripcion')->where('actual','=','1')
          ->orderBy('descripcion','asc')
          ->pluck('descripcion','id')->toArray();

          if (count($objdependencia)>0) {                                
            $objdependencia = ['0'=>'Todos']+$objdependencia;                  
          } else {
            $objdependencia = ['0'=>'Todos'];
          }

          if (count($objdivision)>0) {                                
            $objdivision = ['0'=>'Todos']+$objdivision;                  
          } else {
            $objdivision = ['0'=>'Todos'];
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

          $objfuncionarios = [''=>'Escribir [Enter Busca]'];

          $idesde =Carbon::now()->format('Y-m-d');
          $ihasta =Carbon::now()->format('Y-m-d');
          $objasistencia = asistencia::select('asistencia.id', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
            'asistencia.hora_e3', 'asistencia.hora_s3',
            'asistencia.hora_e4', 'asistencia.hora_s4',
            'asistencia.hora_e5', 'asistencia.hora_s5',
            'asistencia.hora_e6', 'asistencia.hora_s6',
            'asistencia.hora_e7', 'asistencia.hora_s7',
            'asistencia.hora_e8', 'asistencia.hora_s8',
            'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
            ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                
            ->where('asistencia.id_dep', $operador, $valor)
            ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$idesde)
            ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$ihasta)
            ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')
            ->orderBy('funcionarios.apellido','asc')                
            ->orderBy('funcionarios.nombre','asc')                
            ->limit($this->RegxPag)->get()->toArray();

          return view('qrcodes.show', array('objasistencia'=>$objasistencia, 'busqueda'=>'', 'objfechacarbon'=>$objfechacarbon, 'desde'=>'', 'hasta'=>'', 'objdependencia'=>$objdependencia, 'objdivision'=>$objdivision, 'id_temp'=>'0', 'id_tempdiv'=>'0','horaactual'=>$horaactual,'objfuncionarios'=>$objfuncionarios));

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

    public function VerDatosFuncionario(Request $request)
    {
        if ($request->ajax()) {
          $cedula = $request->cedula;          

          try { 
            //Con el qr de la cedula  
            /*
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )->where('cedula','=',$cedula)->get()->first();
            */

            //Con el nuevo qr
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )
                ->orwhere('nuevoqr','=',$cedula)
                ->orwhere('cedula','=',$cedula)->get()->first();


            if (!isset($result)) {
              return response()->json("No tiene designación activa");
            }
            $result = $result->toArray();

            $tipoimagen = $result['tipoimagen'];
            $foto = $result['foto'];
            $urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
            $result['foto'] = $urlfoto;

            $id_func = $result['id']; 
            $cedula = $result['cedula'];
            $apellido = $result['apellido'];
            $nombre = $result['nombre'];
            $foto = $result['foto'];

            $objdesignacion = designacion::select('id_dep')->where('estatus','=','Activo')->where('id_func','=',$id_func)->get()->first();

            if (isset($objdesignacion)) {
              $id_depf = $objdesignacion['id_dep'];
            } else {
              if ($result['id_dep']==0){
                $id_depf = 0;
                return response()->json("No tiene designación activa");                
              } else {
                $id_depf = $result['id_dep'];
              }
            }        

            $fecha = new fechacarbon();

            $hora_e = $fecha->fecha_tiempo_actual();
            $hora_ee = $fecha->fecha_tiempo_actual_str();
            $hora_s = "";
            $hora_ss = "";

            $te = env('APP_TIEMPO_ESPERA_NUEVA_ASISTENCIA');
            $sw = 0;

            if ( count(asistencia::entrada($id_func, $hora_e)) == 0 ) {                
                $datosasistencia = ['hora_e'=>$hora_e, 'id_func'=>$id_func, 'id_dep'=>$id_depf];
                asistencia::create($datosasistencia);
                $ultimoid = DB::getPdo()->lastInsertId();
                $sw = 1;
                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>'', 'hora_e2'=>'', 'hora_s2'=>'', 'foto'=>$foto, 'id_asistencia'=>$ultimoid]);
            } else {
                $objasistencia = [];

                $hora_t = $fecha->fecha_tiempo_actual();                
                $hora_tt = $fecha->fecha_tiempo_actual_str();

                $objasistencia = asistencia::salida($id_func, $hora_t);

                if (count($objasistencia) > 0) {
                    for($i=0; $i < count($objasistencia); $i++) {
                        $hora_e = new carbon($objasistencia[$i]->hora_e);
                        $hora_ee = $hora_e->format('d-m-Y h:i:s');

                        if (empty($objasistencia[$i]->hora_s)) {
                            $hora_s = "";
                            $hora_ss = "";
                        } else {
                            $hora_s = new carbon($objasistencia[$i]->hora_s);
                            $hora_ss = $hora_s->format('d-m-Y h:i:s');
                        }

                        if (empty($objasistencia[$i]->hora_e2)) {
                            $hora_e2 = "";
                            $hora_ee2 = "";
                        } else {
                            $hora_e2 = new carbon($objasistencia[$i]->hora_e2);
                            $hora_ee2 = $hora_e2->format('d-m-Y h:i:s');
                        }

                        if (empty($objasistencia[$i]->hora_s2)) {
                            $hora_s2 = "";
                            $hora_ss2 = "";
                        } else {
                            $hora_s2 = new carbon($objasistencia[$i]->hora_s2);
                            $hora_ss2 = $hora_s2->format('d-m-Y h:i:s');
                        }

                        $id = $objasistencia[$i]->id;
                        $sw = 1;
                        break;
                    }
                    if ($sw == 1) {

                        if ($hora_s == "") {
                            $registrado = asistencia::select('id')->where('id','=',$id)
                            ->where('hora_e','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                            if (count($registrado)>0) {
                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                ->where('hora_e','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                ->update(
                                [
                                'hora_s'=> DB::raw('NOW()'),
                                'updated_at'=>DB::raw('NOW()')
                                ]
                                );
                                if ($afectados != 1) { 
                                    $hora_s = ""; $hora_ss = "";
                                    $hora_e2 = ""; $hora_ee2 = "";
                                    $hora_s2 = ""; $hora_ss2 = "";
                                    return response()->json('registrado');                                    
                                } else {
                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_tt, 'hora_e2'=>$hora_ee2, 'hora_s2'=>$hora_ss2, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                }                              
                            } else {
                                return response()->json('registrado');     
                            } 

                        } else {

                            if ($hora_e2 == "") {

                                $registrado = asistencia::select('id')->where('id','=',$id)
                                ->where('hora_s','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                                if (count($registrado)>0) {
                                    $afectados = DB::table('asistencia')->where('id','=',$id)
                                    ->where('hora_s','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                    ->update(
                                    [
                                    'hora_e2'=> DB::raw('NOW()'),
                                    'updated_at'=>DB::raw('NOW()')
                                    ]
                                    );
                                    if ($afectados != 1) { 
                                        $hora_s = ""; $hora_ss = "";
                                        $hora_e2 = ""; $hora_ee2 = "";
                                        $hora_s2 = ""; $hora_ss2 = "";
                                        return response()->json('registrado');                                    
                                    } else {
                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_ss, 'hora_e2'=>$hora_tt, 'hora_s2'=>$hora_ss2, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                    }                              
                                } else {
                                    return response()->json('registrado');     
                                }

                            } else {

                                if ($hora_s2 == "") {

                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                    ->where('hora_e2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                    if (count($registrado)>0) {
                                        $afectados = DB::table('asistencia')->where('id','=',$id)
                                        ->where('hora_e2','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                        ->update(
                                        [
                                        'hora_s2'=> DB::raw('NOW()'),
                                        'updated_at'=>DB::raw('NOW()')
                                        ]
                                        );
                                        if ($afectados != 1) { 
                                            $hora_s = ""; $hora_ss = "";
                                            $hora_e2 = ""; $hora_ee2 = "";
                                            $hora_s2 = ""; $hora_ss2 = "";
                                            return response()->json('registrado');                                    
                                        } else {
                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_ss, 'hora_e2'=>$hora_ee2, 'hora_s2'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                        }                              
                                    } else {
                                        return response()->json('registrado');     
                                    }
                                } else {
                                  return response()->json('registrado');  
                                }

                            }
                        }

                    } else {
                      return response()->json('registrado');  
                    }
                }
            }
            return response()->json('Error');            
          } catch (Exception $e) {    
            return response()->json('Error');              
          }
        } else {
          return response()->json("Error");              
        }  
    }

    public function VerDatosFuncionario2(Request $request)
    {
        if ($request->ajax()) {
          $cedula = $request->cedula;          

          try {
            //Con el qr de la cedula   
            /*
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )->where('cedula','=',$cedula)->get()->first();
            */

            //Con el nuevo qr de la cedula   
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )
            ->orwhere('nuevoqr','=',$cedula)
            ->orwhere('cedula','=',$cedula)->get()->first();

            if (!isset($result)) {
              return response()->json("No tiene designación activa");
            }
            $result = $result->toArray();

            $tipoimagen = $result['tipoimagen'];
            $foto = $result['foto'];
            $urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
            $result['foto'] = $urlfoto;

            $id_func = $result['id']; 
            $cedula = $result['cedula'];
            $apellido = $result['apellido'];
            $nombre = $result['nombre'];
            $foto = $result['foto'];

            $objdesignacion = designacion::select('id_dep')->where('estatus','=','Activo')->where('id_func','=',$id_func)->get()->first();

            if (isset($objdesignacion)) {
              $id_depf = $objdesignacion['id_dep'];
            } else {
              if ($result['id_dep']==0){
                $id_depf = 0;
                return response()->json("No tiene designación activa");                
              } else {
                $id_depf = $result['id_dep'];
              }
            } 

            $fecha = new fechacarbon();

            $hora_e = $fecha->fecha_tiempo_actual();
            $hora_ee = $fecha->fecha_tiempo_actual_str();
            $hora_s = "";
            $hora_ss = "";

            $te = env('APP_TIEMPO_ESPERA_NUEVA_ASISTENCIA');
            $sw = 0;

            if ( count(asistencia::entrada($id_func, $hora_e)) == 0 ) {                
                $datosasistencia = ['hora_e'=>$hora_e, 'id_func'=>$id_func, 'id_dep'=>$id_depf];
                $sw = 1;
                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>'', 'hora_e2'=>'', 'hora_s2'=>'', 'foto'=>$foto]);
            } else {
                $objasistencia = [];

                $hora_t = $fecha->fecha_tiempo_actual();                
                $hora_tt = $fecha->fecha_tiempo_actual_str();

                $objasistencia = asistencia::salida($id_func, $hora_t);

                if (count($objasistencia) > 0) {
                    for($i=0; $i < count($objasistencia); $i++) {
                        $hora_e = new carbon($objasistencia[$i]->hora_e);
                        $hora_ee = $hora_e->format('d-m-Y h:i:s');

                        if (empty($objasistencia[$i]->hora_s)) {
                            $hora_s = "";
                            $hora_ss = "";
                        } else {
                            $hora_s = new carbon($objasistencia[$i]->hora_s);
                            $hora_ss = $hora_s->format('d-m-Y h:i:s');
                        }

                        if (empty($objasistencia[$i]->hora_e2)) {
                            $hora_e2 = "";
                            $hora_ee2 = "";
                        } else {
                            $hora_e2 = new carbon($objasistencia[$i]->hora_e2);
                            $hora_ee2 = $hora_e2->format('d-m-Y h:i:s');
                        }

                        if (empty($objasistencia[$i]->hora_s2)) {
                            $hora_s2 = "";
                            $hora_ss2 = "";
                        } else {
                            $hora_s2 = new carbon($objasistencia[$i]->hora_s2);
                            $hora_ss2 = $hora_s2->format('d-m-Y h:i:s');
                        }

                        $id = $objasistencia[$i]->id;
                        $sw = 1;
                        break;
                    }
                    if ($sw == 1) {

                        if ($hora_s == "") {
                            $registrado = asistencia::select('id')->where('id','=',$id)
                            ->where('hora_e','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                            if (count($registrado)>0) {
                                $afectados = 1;
                                if ($afectados != 1) { 
                                    $hora_s = ""; $hora_ss = "";
                                    $hora_e2 = ""; $hora_ee2 = "";
                                    $hora_s2 = ""; $hora_ss2 = "";
                                    return response()->json('registrado');                                    
                                } else {
                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_tt, 'hora_e2'=>$hora_ee2, 'hora_s2'=>$hora_ss2, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                }                              
                            } else {
                                return response()->json('registrado');     
                            } 

                        } else {

                            if ($hora_e2 == "") {

                                $registrado = asistencia::select('id')->where('id','=',$id)
                                ->where('hora_s','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                                if (count($registrado)>0) {
                                    $afectados = 1;
                                    if ($afectados != 1) { 
                                        $hora_s = ""; $hora_ss = "";
                                        $hora_e2 = ""; $hora_ee2 = "";
                                        $hora_s2 = ""; $hora_ss2 = "";
                                        return response()->json('registrado');                                    
                                    } else {
                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_ss, 'hora_e2'=>$hora_tt, 'hora_s2'=>$hora_ss2, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                    }                              
                                } else {
                                    return response()->json('registrado');     
                                }

                            } else {

                                if ($hora_s2 == "") {

                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                    ->where('hora_e2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                    if (count($registrado)>0) {
                                        $afectados = 1;
                                        if ($afectados != 1) { 
                                            $hora_s = ""; $hora_ss = "";
                                            $hora_e2 = ""; $hora_ee2 = "";
                                            $hora_s2 = ""; $hora_ss2 = "";
                                            return response()->json('registrado');                                    
                                        } else {
                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_ss, 'hora_e2'=>$hora_ee2, 'hora_s2'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                        }                              
                                    } else {
                                        return response()->json('registrado');     
                                    }
                                } else {
                                  return response()->json('registrado');  
                                }

                            }
                        }

                    } else {
                      return response()->json('registrado');  
                    }
                }
            }
            return response()->json('Error');            
          } catch (Exception $e) {    
            return response()->json('Error');              
          }
        } else {
          return response()->json("Error");              
        }  
    }

    public function VerDatosFuncionario82(Request $request)
    {
        if ($request->ajax()) {
          $cedula = $request->cedula;          

          try {   
            //Con el qr de la cedula
            /*
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )->where('cedula','=',$cedula)->get()->first();
            */

            //Con el nuevoqr
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto')
                ->orwhere('nuevoqr','=',$cedula)
                ->orwhere('cedula','=',$cedula)
                ->get()->first();

            if (!isset($result)) {
              return response()->json("No tiene designación activa");
            }
            $result = $result->toArray();


            $tipoimagen = $result['tipoimagen'];
            $foto = $result['foto'];
            $urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
            $result['foto'] = $urlfoto;

            $id_func = $result['id']; 
            $cedula = $result['cedula'];
            $apellido = $result['apellido'];
            $nombre = $result['nombre'];
            $foto = $result['foto'];

            $objdesignacion = designacion::select('id_dep')->where('estatus','=','Activo')->where('id_func','=',$id_func)->get()->first();

            if (isset($objdesignacion)) {
              $id_depf = $objdesignacion['id_dep'];
            } else {
              if ($result['id_dep']==0){
                $id_depf = 0;
                return response()->json("No tiene designación activa");                
              } else {
                $id_depf = $result['id_dep'];
              }
            } 

            $fecha = new fechacarbon();

            $hora_e = $fecha->fecha_tiempo_actual();
            $hora_ee = $fecha->fecha_tiempo_actual_str();
            $hora_s = "";
            $hora_ss = "";

            $te = env('APP_TIEMPO_ESPERA_NUEVA_ASISTENCIA');
            $sw = 0;

            if ( count(asistencia::entrada($id_func, $hora_e)) == 0 ) {                
                $datosasistencia = ['hora_e'=>$hora_e, 'id_func'=>$id_func, 'id_dep'=>$id_depf];
                $sw = 1;
                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_ee, 'foto'=>$foto]);
            } else {
                $objasistencia = [];

                $hora_t = $fecha->fecha_tiempo_actual();                
                $hora_tt = $fecha->fecha_tiempo_actual_str();

                $objasistencia = asistencia::salida($id_func, $hora_t);

                if (count($objasistencia) > 0) {
                    for($i=0; $i < count($objasistencia); $i++) {
                        $hora_e = new carbon($objasistencia[$i]->hora_e);
                        $hora_ee = $hora_e->format('d-m-Y h:i:s');

                        if (empty($objasistencia[$i]->hora_s)) { $hora_s = ""; $hora_ss = ""; } 
                        else { $hora_s = new carbon($objasistencia[$i]->hora_s); $hora_ss = $hora_s->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e2)) { $hora_e2 = ""; $hora_ee2 = ""; } 
                        else { $hora_e2 = new carbon($objasistencia[$i]->hora_e2); $hora_ee2 = $hora_e2->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s2)) { $hora_s2 = ""; $hora_ss2 = ""; } 
                        else { $hora_s2 = new carbon($objasistencia[$i]->hora_s2); $hora_ss2 = $hora_s2->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e3)) { $hora_e3 = ""; $hora_ee3 = ""; } 
                        else { $hora_e3 = new carbon($objasistencia[$i]->hora_e3); $hora_ee3 = $hora_e3->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s3)) { $hora_s3 = ""; $hora_ss3 = ""; } 
                        else { $hora_s3 = new carbon($objasistencia[$i]->hora_s3); $hora_ss3 = $hora_s3->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e4)) { $hora_e4 = ""; $hora_ee4 = ""; } 
                        else { $hora_e4 = new carbon($objasistencia[$i]->hora_e4); $hora_ee4 = $hora_e4->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s4)) { $hora_s4 = ""; $hora_ss4 = ""; } 
                        else { $hora_s4 = new carbon($objasistencia[$i]->hora_s4); $hora_ss4 = $hora_s4->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e5)) { $hora_e5 = ""; $hora_ee5 = ""; } 
                        else { $hora_e5 = new carbon($objasistencia[$i]->hora_e5); $hora_ee5 = $hora_e5->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s5)) { $hora_s5 = ""; $hora_ss5 = ""; } 
                        else { $hora_s5 = new carbon($objasistencia[$i]->hora_s5); $hora_ss5 = $hora_s5->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e6)) { $hora_e6 = ""; $hora_ee6 = ""; } 
                        else { $hora_e6 = new carbon($objasistencia[$i]->hora_e6); $hora_ee6 = $hora_e6->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s6)) { $hora_s6 = ""; $hora_ss6 = ""; } 
                        else { $hora_s6 = new carbon($objasistencia[$i]->hora_s6); $hora_ss6 = $hora_s6->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e7)) { $hora_e7 = ""; $hora_ee7 = ""; } 
                        else { $hora_e7 = new carbon($objasistencia[$i]->hora_e7); $hora_ee7 = $hora_e7->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s7)) { $hora_s7 = ""; $hora_ss7 = ""; } 
                        else { $hora_s7 = new carbon($objasistencia[$i]->hora_s7); $hora_ss7 = $hora_s7->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e8)) { $hora_e8 = ""; $hora_ee8 = ""; } 
                        else { $hora_e8 = new carbon($objasistencia[$i]->hora_e8); $hora_ee8 = $hora_e8->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s8)) { $hora_s8 = ""; $hora_ss8 = ""; } 
                        else { $hora_s8 = new carbon($objasistencia[$i]->hora_s8); $hora_ss8 = $hora_s8->format('d-m-Y h:i:s'); }

                        $id = $objasistencia[$i]->id;
                        $sw = 1;
                        break;
                    }
                    if ($sw == 1) {

                        if ($hora_s == "") {
                            $registrado = asistencia::select('id')->where('id','=',$id)
                            ->where('hora_e','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                            if (count($registrado)>0) {
                                $afectados = 1;
                                if ($afectados != 1) { 
                                    return response()->json('registrado');                                    
                                } else {
                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                }                              
                            } else {
                                return response()->json('registrado');     
                            } 

                        } else {

                            if ($hora_e2 == "") {

                                $registrado = asistencia::select('id')->where('id','=',$id)
                                ->where('hora_s','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                                if (count($registrado)>0) {
                                    $afectados = 1;
                                    if ($afectados != 1) { 
                                        return response()->json('registrado');                                    
                                    } else {
                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                    }                              
                                } else {
                                    return response()->json('registrado');     
                                }

                            } else {

                                if ($hora_s2 == "") {

                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                    ->where('hora_e2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                    if (count($registrado)>0) {
                                        $afectados = 1;
                                        if ($afectados != 1) { 
                                            return response()->json('registrado');                                    
                                        } else {
                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                        }                              
                                    } else {
                                        return response()->json('registrado');     
                                    }
                                } else {

                                    if ($hora_e3 == "") {

                                        $registrado = asistencia::select('id')->where('id','=',$id)
                                        ->where('hora_s2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                        if (count($registrado)>0) {
                                            $afectados = 1;
                                            if ($afectados != 1) { 
                                                return response()->json('registrado');                                    
                                            } else {
                                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                            }                              
                                        } else {
                                            return response()->json('registrado');     
                                        }
                                    } else {

                                        if ($hora_s3 == "") {

                                            $registrado = asistencia::select('id')->where('id','=',$id)
                                            ->where('hora_e3','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                            if (count($registrado)>0) {
                                                $afectados = 1;
                                                if ($afectados != 1) { 
                                                    return response()->json('registrado');                                    
                                                } else {
                                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                }                              
                                            } else {
                                                return response()->json('registrado');     
                                            }
                                        } else {

                                            if ($hora_e4 == "") {

                                                $registrado = asistencia::select('id')->where('id','=',$id)
                                                ->where('hora_s3','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                if (count($registrado)>0) {
                                                    $afectados = 1;
                                                    if ($afectados != 1) { 
                                                        return response()->json('registrado');                                    
                                                    } else {
                                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                    }                              
                                                } else {
                                                    return response()->json('registrado');     
                                                }
                                            } else {

                                                if ($hora_s4 == "") {

                                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                                    ->where('hora_e4','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                    if (count($registrado)>0) {
                                                        $afectados = 1;
                                                        if ($afectados != 1) { 
                                                            return response()->json('registrado');                                    
                                                        } else {
                                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                        }                              
                                                    } else {
                                                        return response()->json('registrado');     
                                                    }
                                                } else {

                                                    if ($hora_e5 == "") {

                                                        $registrado = asistencia::select('id')->where('id','=',$id)
                                                        ->where('hora_s4','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                        if (count($registrado)>0) {
                                                            $afectados = 1;
                                                            if ($afectados != 1) { 
                                                                return response()->json('registrado');                                    
                                                            } else {
                                                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                            }                              
                                                        } else {
                                                            return response()->json('registrado');     
                                                        }
                                                    } else {

                                                        if ($hora_s5 == "") {

                                                            $registrado = asistencia::select('id')->where('id','=',$id)
                                                            ->where('hora_e5','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                            if (count($registrado)>0) {
                                                                $afectados = 1;
                                                                if ($afectados != 1) { 
                                                                    return response()->json('registrado');                                    
                                                                } else {
                                                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                }                              
                                                            } else {
                                                                return response()->json('registrado');     
                                                            }
                                                        } else {

                                                            if ($hora_e6 == "") {

                                                                $registrado = asistencia::select('id')->where('id','=',$id)
                                                                ->where('hora_s5','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                if (count($registrado)>0) {
                                                                    $afectados = 1;
                                                                    if ($afectados != 1) { 
                                                                        return response()->json('registrado');                                    
                                                                    } else {
                                                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                    }                              
                                                                } else {
                                                                    return response()->json('registrado');     
                                                                }
                                                            } else {

                                                                if ($hora_s6 == "") {

                                                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                                                    ->where('hora_e6','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                    if (count($registrado)>0) {
                                                                        $afectados = 1;
                                                                        if ($afectados != 1) { 
                                                                            return response()->json('registrado');                                    
                                                                        } else {
                                                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                        }                              
                                                                    } else {
                                                                        return response()->json('registrado');     
                                                                    }
                                                                } else {

                                                                    if ($hora_e7 == "") {

                                                                        $registrado = asistencia::select('id')->where('id','=',$id)
                                                                        ->where('hora_s6','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                        if (count($registrado)>0) {
                                                                            $afectados = 1;
                                                                            if ($afectados != 1) { 
                                                                                return response()->json('registrado');                                    
                                                                            } else {
                                                                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                            }                              
                                                                        } else {
                                                                            return response()->json('registrado');     
                                                                        }
                                                                    } else {

                                                                        if ($hora_s7 == "") {

                                                                            $registrado = asistencia::select('id')->where('id','=',$id)
                                                                            ->where('hora_e7','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                            if (count($registrado)>0) {
                                                                                $afectados = 1;
                                                                                if ($afectados != 1) { 
                                                                                    return response()->json('registrado');                                    
                                                                                } else {
                                                                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                                }                              
                                                                            } else {
                                                                                return response()->json('registrado');     
                                                                            }
                                                                        } else {

                                                                            if ($hora_e8 == "") {

                                                                                $registrado = asistencia::select('id')->where('id','=',$id)
                                                                                ->where('hora_s7','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                                if (count($registrado)>0) {
                                                                                    $afectados = 1;
                                                                                    if ($afectados != 1) { 
                                                                                        return response()->json('registrado');                                    
                                                                                    } else {
                                                                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                                    }                              
                                                                                } else {
                                                                                    return response()->json('registrado');     
                                                                                }
                                                                            } else {

                                                                                if ($hora_s8 == "") {

                                                                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                                                                    ->where('hora_e8','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                                    if (count($registrado)>0) {
                                                                                        $afectados = 1;
                                                                                        if ($afectados != 1) { 
                                                                                            return response()->json('registrado');                                    
                                                                                        } else {
                                                                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                                        }                              
                                                                                    } else {
                                                                                        return response()->json('registrado');     
                                                                                    }
                                                                                } else {
                                                                                    return response()->json('registrado');  
                                                                                }                                                                            
                                                                            }                                                                            
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } 
                                            }                                             
                                        } 
                                    }                                                                      
                                }
                            }
                        }

                    } else {
                      return response()->json('registrado');  
                    }
                }
            }
            return response()->json('Error');            
          } catch (Exception $e) {    
            return response()->json('Error');              
          }
        } else {
          return response()->json("Error");              
        }  
    }
    public function RegistrarDatosFuncionario(Request $request)
    {
        if ($request->ajax()) {
          $cedula = $request->cedula;          

          try {   

            //Con el qr por cedula se usa este
            /*
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )->where('cedula','=',$cedula)->get()->first();
            */

            //Con el nuevo qr por url se usa este            
            $result = funcionarios::select(
                'id','cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )
            ->orwhere('nuevoqr','=',$cedula)
            ->orwhere('nuevoqr','=',$cedula)->get()->first();
            

            if (!isset($result)) {
              return response()->json("No tiene designación activa");
            }
            $result = $result->toArray();

            $tipoimagen = $result['tipoimagen'];
            $foto = $result['foto'];
            $urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
            $result['foto'] = $urlfoto;

            $id_func = $result['id']; 
            $cedula = $result['cedula'];
            $apellido = $result['apellido'];
            $nombre = $result['nombre'];
            $foto = $result['foto'];
            
            $objdesignacion = designacion::select('id_dep')->where('estatus','=','Activo')->where('id_func','=',$id_func)->get()->first();

            if (isset($objdesignacion)) {
              $id_depf = $objdesignacion['id_dep'];
            } else {
              if ($result['id_dep']==0){
                $id_depf = 0;
                return response()->json("No tiene designación activa");                
              } else {
                $id_depf = $result['id_dep'];
              }
            } 

            $fecha = new fechacarbon();

            $hora_e = $fecha->fecha_tiempo_actual();
            $hora_ee = $fecha->fecha_tiempo_actual_str();
            $hora_s = "";
            $hora_ss = "";

            $te = env('APP_TIEMPO_ESPERA_NUEVA_ASISTENCIA');
            $sw = 0;

            if ( count(asistencia::entrada($id_func, $hora_e)) == 0 ) {                
                $datosasistencia = ['hora_e'=>$hora_e, 'id_func'=>$id_func, 'id_dep'=>$id_depf];
                asistencia::create($datosasistencia);
                $ultimoid = DB::getPdo()->lastInsertId();
                $sw = 1;
                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>'', 'hora_e2'=>'', 'hora_s2'=>'', 'foto'=>$foto, 'id_asistencia'=>$ultimoid]);
            } else {
                $objasistencia = [];

                $hora_t = $fecha->fecha_tiempo_actual();                
                $hora_tt = $fecha->fecha_tiempo_actual_str();

                $objasistencia = asistencia::salida($id_func, $hora_t);

                if (count($objasistencia) > 0) {
                    for($i=0; $i < count($objasistencia); $i++) {
                        $hora_e = new carbon($objasistencia[$i]->hora_e);
                        $hora_ee = $hora_e->format('d-m-Y h:i:s');

                        if (empty($objasistencia[$i]->hora_s)) {
                            $hora_s = "";
                            $hora_ss = "";
                        } else {
                            $hora_s = new carbon($objasistencia[$i]->hora_s);
                            $hora_ss = $hora_s->format('d-m-Y h:i:s');
                        }

                        if (empty($objasistencia[$i]->hora_e2)) {
                            $hora_e2 = "";
                            $hora_ee2 = "";
                        } else {
                            $hora_e2 = new carbon($objasistencia[$i]->hora_e2);
                            $hora_ee2 = $hora_e2->format('d-m-Y h:i:s');
                        }

                        if (empty($objasistencia[$i]->hora_s2)) {
                            $hora_s2 = "";
                            $hora_ss2 = "";
                        } else {
                            $hora_s2 = new carbon($objasistencia[$i]->hora_s2);
                            $hora_ss2 = $hora_s2->format('d-m-Y h:i:s');
                        }

                        $id = $objasistencia[$i]->id;
                        $sw = 1;
                        break;
                    }
                    if ($sw == 1) {

                        if ($hora_s == "") {
                            $registrado = asistencia::select('id')->where('id','=',$id)
                            ->where('hora_e','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                            if (count($registrado)>0) {
                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                ->where('hora_e','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                ->update(
                                [
                                'hora_s'=> DB::raw('NOW()'),
                                'updated_at'=>DB::raw('NOW()')
                                ]
                                );
                                if ($afectados != 1) { 
                                    $hora_s = ""; $hora_ss = "";
                                    $hora_e2 = ""; $hora_ee2 = "";
                                    $hora_s2 = ""; $hora_ss2 = "";
                                    return response()->json('registrado');                                    
                                } else {
                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_tt, 'hora_e2'=>$hora_ee2, 'hora_s2'=>$hora_ss2, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                }                              
                            } else {
                                return response()->json('registrado');     
                            } 

                        } else {

                            if ($hora_e2 == "") {

                                $registrado = asistencia::select('id')->where('id','=',$id)
                                ->where('hora_s','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                                if (count($registrado)>0) {
                                    $afectados = DB::table('asistencia')->where('id','=',$id)
                                    ->where('hora_s','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                    ->update(
                                    [
                                    'hora_e2'=> DB::raw('NOW()'),
                                    'updated_at'=>DB::raw('NOW()')
                                    ]
                                    );
                                    if ($afectados != 1) { 
                                        $hora_s = ""; $hora_ss = "";
                                        $hora_e2 = ""; $hora_ee2 = "";
                                        $hora_s2 = ""; $hora_ss2 = "";
                                        return response()->json('registrado');                                    
                                    } else {
                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_ss, 'hora_e2'=>$hora_tt, 'hora_s2'=>$hora_ss2, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                    }                              
                                } else {
                                    return response()->json('registrado');     
                                }

                            } else {

                                if ($hora_s2 == "") {

                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                    ->where('hora_e2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                    if (count($registrado)>0) {
                                        $afectados = DB::table('asistencia')->where('id','=',$id)
                                        ->where('hora_e2','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                        ->update(
                                        [
                                        'hora_s2'=> DB::raw('NOW()'),
                                        'updated_at'=>DB::raw('NOW()')
                                        ]
                                        );
                                        if ($afectados != 1) { 
                                            $hora_s = ""; $hora_ss = "";
                                            $hora_e2 = ""; $hora_ee2 = "";
                                            $hora_s2 = ""; $hora_ss2 = "";
                                            return response()->json('registrado');                                    
                                        } else {
                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_e'=>$hora_ee, 'hora_s'=>$hora_ss, 'hora_e2'=>$hora_ee2, 'hora_s2'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                        }                              
                                    } else {
                                        return response()->json('registrado');     
                                    }
                                } else {
                                  return response()->json('registrado');  
                                }

                            }
                        }

                    } else {
                      return response()->json('registrado');  
                    }
                }
            }
            return response()->json('Error');            
          } catch (Exception $e) {    
            return response()->json("Error");
          }
        } else {
          return response()->json("Error");              
        }  
    }

    public function VerDatosFuncionario8(Request $request) {
      if ($request->ajax()) {
        $cedula = $request->cedula;          

        try {
          //Con el qr de la cedula   
          /*
          $result = funcionarios::select(
            'id', 'cedula', 'apellido', 'nombre',
            'tipo_s', 'alergia','id_dep',
            'ancho', 'alto', 'tipoimagen', 'foto'
          )->where('cedula','=',$cedula)->get()->first();
          */

          //Con el nuevo qr
          $result = funcionarios::select(
            'id', 'cedula', 'apellido', 'nombre',
            'tipo_s', 'alergia','id_dep',
            'ancho', 'alto', 'tipoimagen', 'foto'
          )
          ->orwhere('nuevoqr','=',$cedula)
          ->orwhere('cedula','=',$cedula)->get()->first();


          if (!isset($result)) {
            return response()->json("No tiene designación activa");
          }
          $result = $result->toArray();

          $tipoimagen = $result['tipoimagen'];
          $foto = $result['foto'];
          $urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
          $result['foto'] = $urlfoto;

          $id_func = $result['id']; 
          $cedula = $result['cedula'];
          $apellido = $result['apellido'];
          $nombre = $result['nombre'];
          $foto = $result['foto'];

          $objdesignacion = designacion::select('id_dep')->where('estatus','=','Activo')->where('id_func','=',$id_func)->get()->first();

          if (isset($objdesignacion)) {
            $id_depf = $objdesignacion['id_dep'];
          } else {
            if ($result['id_dep']==0){
              $id_depf = 0;
              return response()->json("No tiene designación activa");                
            } else {
              $id_depf = $result['id_dep'];
            }
          } 

          $fecha = new fechacarbon();

          $hora_e = $fecha->fecha_tiempo_actual();
          $hora_ee = $fecha->fecha_tiempo_actual_str();
          $hora_s = "";
          $hora_ss = "";

          $te = env('APP_TIEMPO_ESPERA_NUEVA_ASISTENCIA');
          $sw = 0;

          if ( count(asistencia::entrada($id_func, $hora_e)) == 0 ) {                
            $datosasistencia = ['hora_e'=>$hora_e, 'id_func'=>$id_func, 'id_dep'=>$id_depf];
            asistencia::create($datosasistencia);
            $ultimoid = DB::getPdo()->lastInsertId();
            $sw = 1;
            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_ee, 'foto'=>$foto, 'id_asistencia'=>$ultimoid]);
          } else {
            $objasistencia = [];

            $hora_t = $fecha->fecha_tiempo_actual();                
            $hora_tt = $fecha->fecha_tiempo_actual_str();

            $objasistencia = asistencia::salida($id_func, $hora_t);

            if (count($objasistencia) > 0) {
              for($i=0; $i < count($objasistencia); $i++) {
                $hora_e = new carbon($objasistencia[$i]->hora_e);
                $hora_ee = $hora_e->format('d-m-Y h:i:s');

                if (empty($objasistencia[$i]->hora_s)) { $hora_s = ""; $hora_ss = ""; } 
                else { $hora_s = new carbon($objasistencia[$i]->hora_s); $hora_ss = $hora_s->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_e2)) { $hora_e2 = ""; $hora_ee2 = ""; }
                else { $hora_e2 = new carbon($objasistencia[$i]->hora_e2); $hora_ee2 = $hora_e2->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_s2)) { $hora_s2 = ""; $hora_ss2 = ""; } 
                else { $hora_s2 = new carbon($objasistencia[$i]->hora_s2); $hora_ss2 = $hora_s2->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_e3)) { $hora_e3 = ""; $hora_ee3 = ""; } 
                else { $hora_e3 = new carbon($objasistencia[$i]->hora_e3); $hora_ee3 = $hora_e3->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_s3)) { $hora_s3 = ""; $hora_ss3 = ""; } 
                else { $hora_s3 = new carbon($objasistencia[$i]->hora_s3); $hora_ss3 = $hora_s3->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_e4)) { $hora_e4 = ""; $hora_ee4 = ""; } 
                else { $hora_e4 = new carbon($objasistencia[$i]->hora_e4); $hora_ee4 = $hora_e4->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_s4)) { $hora_s4 = ""; $hora_ss4 = ""; } 
                else { $hora_s4 = new carbon($objasistencia[$i]->hora_s4); $hora_ss4 = $hora_s4->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_e5)) { $hora_e5 = ""; $hora_ee5 = ""; } 
                else { $hora_e5 = new carbon($objasistencia[$i]->hora_e5); $hora_ee5 = $hora_e5->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_s5)) { $hora_s5 = ""; $hora_ss5 = ""; } 
                else { $hora_s5 = new carbon($objasistencia[$i]->hora_s5); $hora_ss5 = $hora_s5->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_e6)) { $hora_e6 = ""; $hora_ee6 = ""; } 
                else { $hora_e6 = new carbon($objasistencia[$i]->hora_e6); $hora_ee6 = $hora_e6->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_s6)) { $hora_s6 = ""; $hora_ss6 = ""; } 
                else { $hora_s6 = new carbon($objasistencia[$i]->hora_s6); $hora_ss6 = $hora_s6->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_e7)) { $hora_e7 = ""; $hora_ee7 = "";} 
                else { $hora_e7 = new carbon($objasistencia[$i]->hora_e7); $hora_ee7 = $hora_e7->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_s7)) { $hora_s7 = ""; $hora_ss7 = ""; } 
                else { $hora_s7 = new carbon($objasistencia[$i]->hora_s7); $hora_ss7 = $hora_s7->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_e8)) { $hora_e8 = ""; $hora_ee8 = ""; } 
                else { $hora_e8 = new carbon($objasistencia[$i]->hora_e8); $hora_ee8 = $hora_e8->format('d-m-Y h:i:s'); }

                if (empty($objasistencia[$i]->hora_s8)) { $hora_s8 = ""; $hora_ss8 = ""; } 
                else { $hora_s8 = new carbon($objasistencia[$i]->hora_s8); $hora_ss8 = $hora_s8->format('d-m-Y h:i:s'); }

                $id = $objasistencia[$i]->id;
                $sw = 1;
                break;
              }
              if ($sw == 1) {
                if ($hora_s == "") {
                  $registrado = asistencia::select('id')->where('id','=',$id)
                  ->where('hora_e','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                  if (count($registrado)>0) {
                    $afectados = DB::table('asistencia')->where('id','=',$id)
                    ->where('hora_e','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                    ->update(
                      [
                        'hora_s'=> DB::raw('NOW()'),
                        'updated_at'=>DB::raw('NOW()')
                      ]
                    );
                    if ($afectados != 1) { 
                      $hora_s = ""; $hora_ss = "";
                      return response()->json('registrado');                                    
                    } else {
                      return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                    }                              
                  } else {
                    return response()->json('registrado');     
                  } 
                } else {
                  if ($hora_e2 == "") {
                    $registrado = asistencia::select('id')->where('id','=',$id)
                    ->where('hora_s','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                    if (count($registrado)>0) {
                      $afectados = DB::table('asistencia')->where('id','=',$id)
                      ->where('hora_s','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                      ->update(
                        [
                          'hora_e2'=> DB::raw('NOW()'),
                          'updated_at'=>DB::raw('NOW()')
                        ]
                      );
                      if ($afectados != 1) { 
                        $hora_s = ""; $hora_ss = "";
                        $hora_e2 = ""; $hora_ee2 = "";
                        return response()->json('registrado');                                    
                      } else {
                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                      }                              
                    } else {
                      return response()->json('registrado');     
                    }
                  } else {
                    if ($hora_s2 == "") {
                      $registrado = asistencia::select('id')->where('id','=',$id)
                      ->where('hora_e2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                      if (count($registrado)>0) {
                        $afectados = DB::table('asistencia')->where('id','=',$id)
                        ->where('hora_e2','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                        ->update(
                          [
                            'hora_s2'=> DB::raw('NOW()'),
                            'updated_at'=>DB::raw('NOW()')
                          ]
                        );
                        if ($afectados != 1) { 
                          $hora_s2 = ""; $hora_ss2 = "";
                          return response()->json('registrado');                                    
                        } else {
                          return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                        }                              
                      } else {
                        return response()->json('registrado');     
                      }
                    } else {

                      if ($hora_e3 == "") {
                        $registrado = asistencia::select('id')->where('id','=',$id)
                        ->where('hora_s2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                        if (count($registrado)>0) {
                          $afectados = DB::table('asistencia')->where('id','=',$id)
                          ->where('hora_s2','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                          ->update(
                            [
                              'hora_e3'=> DB::raw('NOW()'),
                              'updated_at'=>DB::raw('NOW()')
                            ]
                          );
                          if ($afectados != 1) { 
                            $hora_e3 = ""; $hora_ee3 = "";
                            return response()->json('registrado');                                    
                          } else {
                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                          }                              
                        } else {
                          return response()->json('registrado');     
                        }
                      } else {
                        if ($hora_s3 == "") {
                          $registrado = asistencia::select('id')->where('id','=',$id)
                          ->where('hora_e3','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                          if (count($registrado)>0) {
                            $afectados = DB::table('asistencia')->where('id','=',$id)
                            ->where('hora_e3','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                            ->update(
                              [
                                'hora_s3'=> DB::raw('NOW()'),
                                'updated_at'=>DB::raw('NOW()')
                              ]
                            );
                            if ($afectados != 1) { 
                              $hora_s3 = ""; $hora_ss3 = "";
                              return response()->json('registrado');                                    
                            } else {
                              return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                            }                              
                          } else {
                            return response()->json('registrado');     
                          }
                        } else {
                          if ($hora_e4 == "") {
                            $registrado = asistencia::select('id')->where('id','=',$id)
                            ->where('hora_s3','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                            if (count($registrado)>0) {
                              $afectados = DB::table('asistencia')->where('id','=',$id)
                              ->where('hora_s3','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                              ->update(
                                [
                                  'hora_e4'=> DB::raw('NOW()'),
                                  'updated_at'=>DB::raw('NOW()')
                                ]
                              );
                              if ($afectados != 1) { 
                                $hora_e4 = ""; $hora_ee4 = "";
                                return response()->json('registrado');                                    
                              } else {
                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                              }                              
                            } else {
                              return response()->json('registrado');     
                            }
                          } else {                            
                            if ($hora_s4 == "") {
                              $registrado = asistencia::select('id')->where('id','=',$id)
                              ->where('hora_e4','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                              if (count($registrado)>0) {
                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                ->where('hora_e4','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                ->update(
                                  [
                                    'hora_s4'=> DB::raw('NOW()'),
                                    'updated_at'=>DB::raw('NOW()')
                                  ]
                                );
                                if ($afectados != 1) { 
                                  $hora_s4 = ""; $hora_ss4 = "";
                                  return response()->json('registrado');                                    
                                } else {
                                  return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                }                              
                              } else {
                                return response()->json('registrado');     
                              }
                            } else {
                              if ($hora_e5 == "") {
                                $registrado = asistencia::select('id')->where('id','=',$id)
                                ->where('hora_s4','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                if (count($registrado)>0) {
                                  $afectados = DB::table('asistencia')->where('id','=',$id)
                                  ->where('hora_s4','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                  ->update(
                                    [
                                      'hora_e5'=> DB::raw('NOW()'),
                                      'updated_at'=>DB::raw('NOW()')
                                    ]
                                  );
                                  if ($afectados != 1) { 
                                    $hora_e5 = ""; $hora_ee5 = "";
                                    return response()->json('registrado');                                    
                                  } else {
                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                  }                              
                                } else {
                                  return response()->json('registrado');     
                                }
                              } else {
                                if ($hora_s5 == "") {
                                  $registrado = asistencia::select('id')->where('id','=',$id)
                                  ->where('hora_e5','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                  if (count($registrado)>0) {
                                    $afectados = DB::table('asistencia')->where('id','=',$id)
                                    ->where('hora_e5','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                    ->update(
                                      [
                                        'hora_s5'=> DB::raw('NOW()'),
                                        'updated_at'=>DB::raw('NOW()')
                                      ]
                                    );
                                    if ($afectados != 1) { 
                                      $hora_s5 = ""; $hora_ss5 = "";
                                      return response()->json('registrado');                                    
                                    } else {
                                      return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                    }                              
                                  } else {
                                    return response()->json('registrado');     
                                  }
                                } else {
                                  if ($hora_e6 == "") {
                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                    ->where('hora_s5','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                    if (count($registrado)>0) {
                                      $afectados = DB::table('asistencia')->where('id','=',$id)
                                      ->where('hora_s5','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                      ->update(
                                        [
                                          'hora_e6'=> DB::raw('NOW()'),
                                          'updated_at'=>DB::raw('NOW()')
                                        ]
                                      );
                                      if ($afectados != 1) { 
                                        $hora_e6 = ""; $hora_ee6 = "";
                                        return response()->json('registrado');                                    
                                      } else {
                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                      }                              
                                    } else {
                                      return response()->json('registrado');     
                                    }
                                  } else {
                                    if ($hora_s6 == "") {
                                      $registrado = asistencia::select('id')->where('id','=',$id)
                                      ->where('hora_e6','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                      if (count($registrado)>0) {
                                        $afectados = DB::table('asistencia')->where('id','=',$id)
                                        ->where('hora_e6','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                        ->update(
                                          [
                                            'hora_s6'=> DB::raw('NOW()'),
                                            'updated_at'=>DB::raw('NOW()')
                                          ]
                                        );
                                        if ($afectados != 1) { 
                                          $hora_s6 = ""; $hora_ss6 = "";
                                          return response()->json('registrado');                                    
                                        } else {
                                          return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                        }                              
                                      } else {
                                        return response()->json('registrado');     
                                      }
                                    } else {
                                      if ($hora_e7 == "") {
                                        $registrado = asistencia::select('id')->where('id','=',$id)
                                        ->where('hora_s6','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                        if (count($registrado)>0) {
                                          $afectados = DB::table('asistencia')->where('id','=',$id)
                                          ->where('hora_s6','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                          ->update(
                                            [
                                              'hora_e7'=> DB::raw('NOW()'),
                                              'updated_at'=>DB::raw('NOW()')
                                            ]
                                          );
                                          if ($afectados != 1) { 
                                            $hora_e7 = ""; $hora_ee7 = "";
                                            return response()->json('registrado');                                    
                                          } else {
                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                          }                              
                                        } else {
                                          return response()->json('registrado');     
                                        }
                                      } else {
                                        if ($hora_s7 == "") {
                                          $registrado = asistencia::select('id')->where('id','=',$id)
                                          ->where('hora_e7','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                          if (count($registrado)>0) {
                                            $afectados = DB::table('asistencia')->where('id','=',$id)
                                            ->where('hora_e7','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                            ->update(
                                              [
                                                'hora_s7'=> DB::raw('NOW()'),
                                                'updated_at'=>DB::raw('NOW()')
                                              ]
                                            );
                                            if ($afectados != 1) { 
                                              $hora_s7 = ""; $hora_ss7 = "";
                                              return response()->json('registrado');                                    
                                            } else {
                                              return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                            }                              
                                          } else {
                                            return response()->json('registrado');     
                                          }
                                        } else {
                                          if ($hora_e8 == "") {
                                            $registrado = asistencia::select('id')->where('id','=',$id)
                                            ->where('hora_s7','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                            if (count($registrado)>0) {
                                              $afectados = DB::table('asistencia')->where('id','=',$id)
                                              ->where('hora_s7','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                              ->update(
                                                [
                                                  'hora_e8'=> DB::raw('NOW()'),
                                                  'updated_at'=>DB::raw('NOW()')
                                                ]
                                              );
                                              if ($afectados != 1) { 
                                                $hora_e8 = ""; $hora_ee8 = "";
                                                return response()->json('registrado');                                    
                                              } else {
                                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                              }                              
                                            } else {
                                              return response()->json('registrado');     
                                            }
                                          } else {
                                            if ($hora_s8 == "") {
                                              $registrado = asistencia::select('id')->where('id','=',$id)
                                              ->where('hora_e8','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                              if (count($registrado)>0) {
                                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                                ->where('hora_e8','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                ->update(
                                                  [
                                                    'hora_s8'=> DB::raw('NOW()'),
                                                    'updated_at'=>DB::raw('NOW()')
                                                  ]
                                                );
                                                if ($afectados != 1) { 
                                                  $hora_s8 = ""; $hora_ss8 = "";
                                                  return response()->json('registrado');                                    
                                                } else {
                                                  return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt,'foto'=>$foto, 'id_asistencia'=>$id]);
                                                }                              
                                              } else {
                                                return response()->json('registrado');     
                                              }
                                            } else {
                                              return response()->json('registrado');  
                                            }
                                          }
                                        }
                                      }
                                    }                                    
                                  }
                                }
                              }                              
                            }
                          }
                        }
                      }
                    }
                  }
                }
              } else {
                return response()->json('registrado');  
              }
            }
          }
          return response()->json('Error');            
        } catch (Exception $e) {    
          return response()->json('Error');              
        }
      } else {
        return response()->json("Error");              
      }  
    }

    public function RegistrarDatosFuncionario8(Request $request)
    {
        if ($request->ajax()) {            
          $cedula = $request->cedula;          

          try {
            //Con el qr de la cedula
            /*   
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )->where('cedula','=',$cedula)->get()->first();
            */

            //Con el nuevo qr
            $result = funcionarios::select(
                'id', 'cedula', 'apellido', 'nombre',
                'tipo_s', 'alergia','id_dep',
                'ancho', 'alto', 'tipoimagen', 'foto'
                )
            ->orwhere('nuevoqr','=',$cedula)
            ->orwhere('cedula','=',$cedula)->get()->first();


            if (!isset($result)) {
              return response()->json("No tiene designación activa");
            }
            $result = $result->toArray();

            $tipoimagen = $result['tipoimagen'];
            $foto = $result['foto'];
            $urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
            $result['foto'] = $urlfoto;

            $id_func = $result['id']; 
            $cedula = $result['cedula'];
            $apellido = $result['apellido'];
            $nombre = $result['nombre'];
            $foto = $result['foto'];

            $objdesignacion = designacion::select('id_dep')->where('estatus','=','Activo')->where('id_func','=',$id_func)->get()->first();

            if (isset($objdesignacion)) {
              $id_depf = $objdesignacion['id_dep'];
            } else {
              if ($result['id_dep']==0){
                $id_depf = 0;
                return response()->json("No tiene designación activa");                
              } else {
                $id_depf = $result['id_dep'];
              }
            } 

            $fecha = new fechacarbon();

            $hora_e = $fecha->fecha_tiempo_actual();
            $hora_ee = $fecha->fecha_tiempo_actual_str();
            $hora_s = "";
            $hora_ss = "";

            $te = env('APP_TIEMPO_ESPERA_NUEVA_ASISTENCIA');
            $sw = 0;

            if ( count(asistencia::entrada($id_func, $hora_e)) == 0 ) {                
                $datosasistencia = ['hora_e'=>$hora_e, 'id_func'=>$id_func, 'id_dep'=>$id_depf];
                asistencia::create($datosasistencia);
                $ultimoid = DB::getPdo()->lastInsertId();
                $sw = 1;
                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_ee, 'foto'=>$foto, 'id_asistencia'=>$ultimoid]);
            } else {
                $objasistencia = [];

                $hora_t = $fecha->fecha_tiempo_actual();                
                $hora_tt = $fecha->fecha_tiempo_actual_str();

                $objasistencia = asistencia::salida($id_func, $hora_t);

                if (count($objasistencia) > 0) {
                    for($i=0; $i < count($objasistencia); $i++) {
                        $hora_e = new carbon($objasistencia[$i]->hora_e);
                        $hora_ee = $hora_e->format('d-m-Y h:i:s');

                        if (empty($objasistencia[$i]->hora_s)) { $hora_s = ""; $hora_ss = ""; }
                        else { $hora_s = new carbon($objasistencia[$i]->hora_s); $hora_ss = $hora_s->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e2)) { $hora_e2 = ""; $hora_ee2 = ""; } 
                        else { $hora_e2 = new carbon($objasistencia[$i]->hora_e2); $hora_ee2 = $hora_e2->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s2)) { $hora_s2 = ""; $hora_ss2 = ""; } 
                        else { $hora_s2 = new carbon($objasistencia[$i]->hora_s2); $hora_ss2 = $hora_s2->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e3)) { $hora_e3 = ""; $hora_ee3 = ""; } 
                        else { $hora_e3 = new carbon($objasistencia[$i]->hora_e3); $hora_ee3 = $hora_e3->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s3)) { $hora_s3 = ""; $hora_ss3 = ""; } 
                        else { $hora_s3 = new carbon($objasistencia[$i]->hora_s3); $hora_ss3 = $hora_s3->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e4)) { $hora_e4 = ""; $hora_ee4 = ""; } 
                        else { $hora_e4 = new carbon($objasistencia[$i]->hora_e4); $hora_ee4 = $hora_e4->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s4)) { $hora_s4 = ""; $hora_ss4 = ""; } 
                        else { $hora_s4 = new carbon($objasistencia[$i]->hora_s4); $hora_ss4 = $hora_s4->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e5)) { $hora_e5 = ""; $hora_ee5 = ""; } 
                        else { $hora_e5 = new carbon($objasistencia[$i]->hora_e5); $hora_ee5 = $hora_e5->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s5)) { $hora_s5 = ""; $hora_ss5 = ""; } 
                        else { $hora_s5 = new carbon($objasistencia[$i]->hora_s5); $hora_ss5 = $hora_s5->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e6)) { $hora_e6 = ""; $hora_ee6 = ""; } 
                        else { $hora_e6 = new carbon($objasistencia[$i]->hora_e6); $hora_ee6 = $hora_e6->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s6)) { $hora_s6 = ""; $hora_ss6 = ""; } 
                        else { $hora_s6 = new carbon($objasistencia[$i]->hora_s6); $hora_ss6 = $hora_s6->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e7)) { $hora_e7 = ""; $hora_ee7 = ""; } 
                        else { $hora_e7 = new carbon($objasistencia[$i]->hora_e7); $hora_ee7 = $hora_e7->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s7)) { $hora_s7 = ""; $hora_ss7 = ""; } 
                        else { $hora_s7 = new carbon($objasistencia[$i]->hora_s7); $hora_ss7 = $hora_s7->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_e8)) { $hora_e8 = ""; $hora_ee8 = ""; } 
                        else { $hora_e8 = new carbon($objasistencia[$i]->hora_e8); $hora_ee8 = $hora_e8->format('d-m-Y h:i:s'); }

                        if (empty($objasistencia[$i]->hora_s8)) { $hora_s8 = ""; $hora_ss8 = ""; } 
                        else { $hora_s8 = new carbon($objasistencia[$i]->hora_s8); $hora_ss8 = $hora_s8->format('d-m-Y h:i:s'); }

                        $id = $objasistencia[$i]->id;
                        $sw = 1;
                        break;
                    }
                    if ($sw == 1) {

                        if ($hora_s == "") {
                            $registrado = asistencia::select('id')->where('id','=',$id)
                            ->where('hora_e','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                            if (count($registrado)>0) {
                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                ->where('hora_e','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                ->update(
                                [
                                'hora_s'=> DB::raw('NOW()'),
                                'updated_at'=>DB::raw('NOW()')
                                ]
                                );
                                if ($afectados != 1) { 
                                    return response()->json('registrado');                                    
                                } else {
                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                }                              
                            } else {
                                return response()->json('registrado');     
                            } 

                        } else {

                            if ($hora_e2 == "") {

                                $registrado = asistencia::select('id')->where('id','=',$id)
                                ->where('hora_s','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();

                                if (count($registrado)>0) {
                                    $afectados = DB::table('asistencia')->where('id','=',$id)
                                    ->where('hora_s','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                    ->update(
                                    [
                                    'hora_e2'=> DB::raw('NOW()'),
                                    'updated_at'=>DB::raw('NOW()')
                                    ]
                                    );
                                    if ($afectados != 1) { 
                                        return response()->json('registrado');                                    
                                    } else {
                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                    }                              
                                } else {
                                    return response()->json('registrado');     
                                }

                            } else {

                                if ($hora_s2 == "") {

                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                    ->where('hora_e2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                    if (count($registrado)>0) {
                                        $afectados = DB::table('asistencia')->where('id','=',$id)
                                        ->where('hora_e2','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                        ->update(
                                        [
                                        'hora_s2'=> DB::raw('NOW()'),
                                        'updated_at'=>DB::raw('NOW()')
                                        ]
                                        );
                                        if ($afectados != 1) { 
                                            return response()->json('registrado');                                    
                                        } else {
                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                        }                              
                                    } else {
                                        return response()->json('registrado');     
                                    }
                                } else {

                                    if ($hora_e3 == "") {

                                        $registrado = asistencia::select('id')->where('id','=',$id)
                                        ->where('hora_s2','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                        if (count($registrado)>0) {
                                            $afectados = DB::table('asistencia')->where('id','=',$id)
                                            ->where('hora_s2','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                            ->update(
                                            [
                                            'hora_e3'=> DB::raw('NOW()'),
                                            'updated_at'=>DB::raw('NOW()')
                                            ]
                                            );
                                            if ($afectados != 1) { 
                                                return response()->json('registrado');                                    
                                            } else {
                                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                            }                              
                                        } else {
                                            return response()->json('registrado');     
                                        }
                                    } else {
                                        if ($hora_s3 == "") {

                                            $registrado = asistencia::select('id')->where('id','=',$id)
                                            ->where('hora_e3','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                            if (count($registrado)>0) {
                                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                                ->where('hora_e3','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                ->update(
                                                [
                                                'hora_s3'=> DB::raw('NOW()'),
                                                'updated_at'=>DB::raw('NOW()')
                                                ]
                                                );
                                                if ($afectados != 1) { 
                                                    return response()->json('registrado');                                    
                                                } else {
                                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                }                              
                                            } else {
                                                return response()->json('registrado');     
                                            }
                                        } else {
                                            if ($hora_e4 == "") {

                                                $registrado = asistencia::select('id')->where('id','=',$id)
                                                ->where('hora_s3','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                if (count($registrado)>0) {
                                                    $afectados = DB::table('asistencia')->where('id','=',$id)
                                                    ->where('hora_s3','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                    ->update(
                                                    [
                                                    'hora_e4'=> DB::raw('NOW()'),
                                                    'updated_at'=>DB::raw('NOW()')
                                                    ]
                                                    );
                                                    if ($afectados != 1) { 
                                                        return response()->json('registrado');                                    
                                                    } else {
                                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                    }                              
                                                } else {
                                                    return response()->json('registrado');     
                                                }
                                            } else {

                                                if ($hora_s4 == "") {

                                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                                    ->where('hora_e4','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                    if (count($registrado)>0) {
                                                        $afectados = DB::table('asistencia')->where('id','=',$id)
                                                        ->where('hora_e4','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                        ->update(
                                                        [
                                                        'hora_s4'=> DB::raw('NOW()'),
                                                        'updated_at'=>DB::raw('NOW()')
                                                        ]
                                                        );
                                                        if ($afectados != 1) { 
                                                            return response()->json('registrado');                                    
                                                        } else {
                                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                        }                              
                                                    } else {
                                                        return response()->json('registrado');     
                                                    }
                                                } else {

                                                    if ($hora_e5 == "") {
                                                        $registrado = asistencia::select('id')->where('id','=',$id)
                                                        ->where('hora_s4','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                        if (count($registrado)>0) {
                                                            $afectados = DB::table('asistencia')->where('id','=',$id)
                                                            ->where('hora_s4','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                            ->update(
                                                            [
                                                            'hora_e5'=> DB::raw('NOW()'),
                                                            'updated_at'=>DB::raw('NOW()')
                                                            ]
                                                            );
                                                            if ($afectados != 1) { 
                                                                return response()->json('registrado');                                    
                                                            } else {
                                                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                            }                              
                                                        } else {
                                                            return response()->json('registrado');     
                                                        }
                                                    } else {

                                                        if ($hora_s5 == "") {
                                                            $registrado = asistencia::select('id')->where('id','=',$id)
                                                            ->where('hora_e5','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                            if (count($registrado)>0) {
                                                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                                                ->where('hora_e5','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                                ->update(
                                                                [
                                                                'hora_s5'=> DB::raw('NOW()'),
                                                                'updated_at'=>DB::raw('NOW()')
                                                                ]
                                                                );
                                                                if ($afectados != 1) { 
                                                                    return response()->json('registrado');                                    
                                                                } else {
                                                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                }                              
                                                            } else {
                                                                return response()->json('registrado');     
                                                            }
                                                        } else {

                                                            if ($hora_e6 == "") {
                                                                $registrado = asistencia::select('id')->where('id','=',$id)
                                                                ->where('hora_s5','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                if (count($registrado)>0) {
                                                                    $afectados = DB::table('asistencia')->where('id','=',$id)
                                                                    ->where('hora_s5','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                                    ->update(
                                                                    [
                                                                    'hora_e6'=> DB::raw('NOW()'),
                                                                    'updated_at'=>DB::raw('NOW()')
                                                                    ]
                                                                    );
                                                                    if ($afectados != 1) { 
                                                                        return response()->json('registrado');                                    
                                                                    } else {
                                                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                    }                              
                                                                } else {
                                                                    return response()->json('registrado');     
                                                                }
                                                            } else {

                                                                if ($hora_s6 == "") {
                                                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                                                    ->where('hora_e6','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                    if (count($registrado)>0) {
                                                                        $afectados = DB::table('asistencia')->where('id','=',$id)
                                                                        ->where('hora_e6','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                                        ->update(
                                                                        [
                                                                        'hora_s6'=> DB::raw('NOW()'),
                                                                        'updated_at'=>DB::raw('NOW()')
                                                                        ]
                                                                        );
                                                                        if ($afectados != 1) { 
                                                                            return response()->json('registrado');                                    
                                                                        } else {
                                                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                        }                              
                                                                    } else {
                                                                        return response()->json('registrado');     
                                                                    }
                                                                } else {

                                                                    if ($hora_e7 == "") {
                                                                        $registrado = asistencia::select('id')->where('id','=',$id)
                                                                        ->where('hora_s6','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                        if (count($registrado)>0) {
                                                                            $afectados = DB::table('asistencia')->where('id','=',$id)
                                                                            ->where('hora_s6','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                                            ->update(
                                                                            [
                                                                            'hora_e7'=> DB::raw('NOW()'),
                                                                            'updated_at'=>DB::raw('NOW()')
                                                                            ]
                                                                            );
                                                                            if ($afectados != 1) { 
                                                                                return response()->json('registrado');                                    
                                                                            } else {
                                                                                return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                            }                              
                                                                        } else {
                                                                            return response()->json('registrado');     
                                                                        }
                                                                    } else {

                                                                        if ($hora_s7 == "") {
                                                                            $registrado = asistencia::select('id')->where('id','=',$id)
                                                                            ->where('hora_e7','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                            if (count($registrado)>0) {
                                                                                $afectados = DB::table('asistencia')->where('id','=',$id)
                                                                                ->where('hora_e7','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                                                ->update(
                                                                                [
                                                                                'hora_s7'=> DB::raw('NOW()'),
                                                                                'updated_at'=>DB::raw('NOW()')
                                                                                ]
                                                                                );
                                                                                if ($afectados != 1) { 
                                                                                    return response()->json('registrado');                                    
                                                                                } else {
                                                                                    return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                                }                              
                                                                            } else {
                                                                                return response()->json('registrado');     
                                                                            }
                                                                        } else {

                                                                            if ($hora_e8 == "") {
                                                                                $registrado = asistencia::select('id')->where('id','=',$id)
                                                                                ->where('hora_s7','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                                if (count($registrado)>0) {
                                                                                    $afectados = DB::table('asistencia')->where('id','=',$id)
                                                                                    ->where('hora_s7','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                                                    ->update(
                                                                                    [
                                                                                    'hora_e8'=> DB::raw('NOW()'),
                                                                                    'updated_at'=>DB::raw('NOW()')
                                                                                    ]
                                                                                    );
                                                                                    if ($afectados != 1) { 
                                                                                        return response()->json('registrado');                                    
                                                                                    } else {
                                                                                        return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                                    }                              
                                                                                } else {
                                                                                    return response()->json('registrado');     
                                                                                }
                                                                            } else {

                                                                                if ($hora_s8 == "") {
                                                                                    $registrado = asistencia::select('id')->where('id','=',$id)
                                                                                    ->where('hora_e8','<=',DB::raw('DATE_SUB(now(), INTERVAL "$te" MINUTE)'))->get()->toArray();                                    

                                                                                    if (count($registrado)>0) {
                                                                                        $afectados = DB::table('asistencia')->where('id','=',$id)
                                                                                        ->where('hora_e8','<=', DB::raw('DATE_SUB(now(), INTERVAL '.$te.' MINUTE)'))
                                                                                        ->update(
                                                                                        [
                                                                                        'hora_s8'=> DB::raw('NOW()'),
                                                                                        'updated_at'=>DB::raw('NOW()')
                                                                                        ]
                                                                                        );
                                                                                        if ($afectados != 1) { 
                                                                                            return response()->json('registrado');                                    
                                                                                        } else {
                                                                                            return response()->json(['cedula'=>$cedula,'apellido'=>$apellido,'nombre'=>$nombre, 'hora_r'=>$hora_tt, 'foto'=>$foto, 'id_asistencia'=>$id]);
                                                                                        }                              
                                                                                    } else {
                                                                                        return response()->json('registrado');     
                                                                                    }
                                                                                } else {
                                                                                    return response()->json('registrado');  
                                                                                }

                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    } else {
                      return response()->json('registrado');  
                    }
                }
            }
            return response()->json('Error');            
          } catch (Exception $e) {    
            return response()->json("Error");
          }
        } else {
          return response()->json("Error");              
        }  
    }


    public function BuscarAsistencia(Request $request) 
    {    
        try {
            $tipo = $request->tipo;
            if ($tipo=='RETARDADOS') {
              $operadort=">=";
              $valort="08:01:00";
            } else {
              $operadort=">=";
              $valort="00:00:00";
            }
            // Modficado el rango de consulta de 7  Dias
            $rangodia = 7;
            $objhoraactual = DB::select('select now() as horaactual;');          
            $horaactual = $objhoraactual[0]->horaactual;

            $objfechacarbon = new fechacarbon();
            $busqueda = $request->busqueda;
            $desde = $request->desde;
            $hasta = $request->hasta;
            $id_temp = $request->id_dep;
            $id_tempdiv = $request->id_div;
            $id_div = $request->id_div;

            $objfuncionarios = [''=>'Escribir [Enter Busca]'];

            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
            ->where('dependencia.id_reg','=',Auth::user()->Dependencia->Region->id)
            ->orderBy('dependencia.nom_dep','asc')
            ->pluck('nombredependencia','id')->toArray();

            $objdivision = division::select('id','descripcion')
            ->where('actual','=','1')
            ->orderBy('descripcion','asc')
            ->pluck('descripcion','id')->toArray();

            if (count($objdivision)>0) {                                
              $objdivision = ['0'=>'Todos']+$objdivision;                  
            } else {
              $objdivision = ['0'=>'Todos'];
            }

            if (count($objdependencia)>0) {                                
              $objdependencia = ['0'=>'Todos']+$objdependencia;                  
            } else {
              $objdependencia = ['0'=>'Todos'];
            }

            $operador = '>';
            $valor = '0';
            if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
             $operador = '=';
             $valor = Auth::user()->id_dep;
            } else {
                if ($request->id_dep != "0") {
                    $operador = '=';
                    $valor = $request->id_dep;
                } else {
                    $operador = '>';
                    $valor = '0';
                }
            }

            if ($desde == "" && $hasta == "" ) {              

                $idesde =Carbon::now()->format('Y-m-d');
                $ihasta =Carbon::now()->format('Y-m-d');

                if ($id_div==0) { 
                  $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep',
                  'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                  'asistencia.hora_e3', 'asistencia.hora_s3',
                  'asistencia.hora_e4', 'asistencia.hora_s4',
                  'asistencia.hora_e5', 'asistencia.hora_s5',
                  'asistencia.hora_e6', 'asistencia.hora_s6',
                  'asistencia.hora_e7', 'asistencia.hora_s7',
                  'asistencia.hora_e8', 'asistencia.hora_s8',
                  'asistencia.id_func','asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
                  ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')
                  ->where('asistencia.id_dep', $operador, $valor)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$idesde)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$ihasta)
                  ->where(DB::raw("DATE_FORMAT(asistencia.hora_e,'%H:%i')"),$operadort,$valort)                  
                  ->where(function ($query) use ($busqueda) {
                    $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                    ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                    ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');                                   
                  })
                  ->orderBy('asistencia.id_dep','asc')
                  ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                  ->orderBy('funcionarios.apellido','asc')                
                  ->orderBy('funcionarios.nombre','asc')                
                  ->limit($this->RegxPag)->get()->toArray();
                } else {
                  $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                  'asistencia.hora_e3', 'asistencia.hora_s3',
                  'asistencia.hora_e4', 'asistencia.hora_s4',
                  'asistencia.hora_e5', 'asistencia.hora_s5',
                  'asistencia.hora_e6', 'asistencia.hora_s6',
                  'asistencia.hora_e7', 'asistencia.hora_s7',
                  'asistencia.hora_e8', 'asistencia.hora_s8',
                  'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido','designacion.id_divi')
                  ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')
                  ->leftjoin('designacion',function($join) {
                    $join->on('asistencia.id_func','=','designacion.id_func')
                    ->where('designacion.estatus','=','Activo');
                  })
                  ->where('designacion.id_divi', '=', $id_div)
                  ->where('asistencia.id_dep', $operador, $valor)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$idesde)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$ihasta)
                  ->where(DB::raw("DATE_FORMAT(asistencia.hora_e,'%H:%i')"),$operadort,$valort)                  
                  ->where(function ($query) use ($busqueda) {
                    $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                    ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                    ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');                                                     
                  })
                  ->orderBy('asistencia.id_dep','asc')
                  ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                  ->orderBy('funcionarios.apellido','asc')                
                  ->orderBy('funcionarios.nombre','asc')                
                  ->limit($this->RegxPag)->get()->toArray();
                }

                return view('qrcodes.show', array('objasistencia'=>$objasistencia, 'busqueda'=>$busqueda, 'objfechacarbon'=>$objfechacarbon, 'desde'=>'', 'hasta'=>'', 'objdependencia'=>$objdependencia, 'objdivision'=>$objdivision, 'id_temp'=>$id_temp, 'id_tempdiv'=>$id_tempdiv, 'horaactual'=>$horaactual, 'objfuncionarios'=>$objfuncionarios,'tipo'=>$tipo));                

            } else {

                $data = ['desde'=>$desde,'hasta'=>$hasta];
                $requestvalido = new PeriodoRequest();
                $validation = Validator::make($data, $requestvalido->rules(), $requestvalido->messages());

                if ($validation->fails()) {
                    if ($id_div==0) { 
                      $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                      'asistencia.hora_e3', 'asistencia.hora_s3',
                      'asistencia.hora_e4', 'asistencia.hora_s4',
                      'asistencia.hora_e5', 'asistencia.hora_s5',
                      'asistencia.hora_e6', 'asistencia.hora_s6',
                      'asistencia.hora_e7', 'asistencia.hora_s7',
                      'asistencia.hora_e8', 'asistencia.hora_s8',
                      'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
                      ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')
                      ->where('asistencia.id_dep', $operador, $valor)
                      ->where(DB::raw("DATE_FORMAT(asistencia.hora_e,'%H:%i')"),$operadort,$valort)
                      ->where(function ($query) use ($busqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');                                   
                      })
                      ->orderBy('asistencia.id_dep','asc')
                      ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                      ->orderBy('funcionarios.apellido','asc')                
                      ->orderBy('funcionarios.nombre','asc')                                    
                      ->limit($this->RegxPag)->get()->toArray();
                    } else {
                      $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                      'asistencia.hora_e3', 'asistencia.hora_s3',
                      'asistencia.hora_e4', 'asistencia.hora_s4',
                      'asistencia.hora_e5', 'asistencia.hora_s5',
                      'asistencia.hora_e6', 'asistencia.hora_s6',
                      'asistencia.hora_e7', 'asistencia.hora_s7',
                      'asistencia.hora_e8', 'asistencia.hora_s8',
                      'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido','designacion.id_divi')
                      ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')
                      ->leftjoin('designacion',function($join) {
                      $join->on('asistencia.id_func','=','designacion.id_func')
                      ->where('designacion.estatus','=','Activo');
                      })                    
                      ->where('designacion.id_divi', '=', $id_div)
                      ->where('asistencia.id_dep', $operador, $valor)
                      ->where(DB::raw("DATE_FORMAT(asistencia.hora_e,'%H:%i')"),$operadort,$valort)
                      ->where(function ($query) use ($busqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');                                   
                      })
                      ->orderBy('asistencia.id_dep','asc')
                      ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                      ->orderBy('funcionarios.apellido','asc')                
                      ->orderBy('funcionarios.nombre','asc')                                    
                      ->limit($this->RegxPag)->get()->toArray();
                    }
                    Session::flash('message-error','Formáto de fecha inválido para la consulta');               
                    
                    return view('qrcodes.show', array('objasistencia'=>$objasistencia, 'busqueda'=>$busqueda, 'objfechacarbon'=>$objfechacarbon, 'desde'=>'', 'hasta'=>'', 'objdependencia'=>$objdependencia, 'objdivision'=>$objdivision, 'id_temp'=>$id_temp, 'id_tempdiv'=>$id_tempdiv, 'horaactual'=>$horaactual, 'objfuncionarios'=>$objfuncionarios,'tipo'=>$tipo));
                } else {
                  $rangodefecha = area::select(DB::raw("TIMESTAMPDIFF(DAY,'".$desde."','".$hasta."') as dias"))->get()->first()->toArray();

                  if ($rangodefecha['dias']<=$rangodia) {

                    if ($id_div==0) {
                      $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                      'asistencia.hora_e3', 'asistencia.hora_s3',
                      'asistencia.hora_e4', 'asistencia.hora_s4',
                      'asistencia.hora_e5', 'asistencia.hora_s5',
                      'asistencia.hora_e6', 'asistencia.hora_s6',
                      'asistencia.hora_e7', 'asistencia.hora_s7',
                      'asistencia.hora_e8', 'asistencia.hora_s8',
                      'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
                      ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                    
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$desde)
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$hasta)
                      ->where(DB::raw("DATE_FORMAT(asistencia.hora_e,'%H:%i')"),$operadort,$valort)
                      ->where('asistencia.id_dep', $operador, $valor)
                      ->where(function ($query) use ($busqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');                                   
                      })
                      ->orderBy('asistencia.id_dep','asc')
                      ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                      ->orderBy('funcionarios.apellido','asc')                
                      ->orderBy('funcionarios.nombre','asc')                
                      ->limit($this->RegxPag)->get()->toArray();
                    } else {

                      $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                      'asistencia.hora_e3', 'asistencia.hora_s3',
                      'asistencia.hora_e4', 'asistencia.hora_s4',
                      'asistencia.hora_e5', 'asistencia.hora_s5',
                      'asistencia.hora_e6', 'asistencia.hora_s6',
                      'asistencia.hora_e7', 'asistencia.hora_s7',
                      'asistencia.hora_e8', 'asistencia.hora_s8',
                      'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido','designacion.id_divi','designacion.estatus')
                      ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                    
                      ->leftjoin('designacion', function ($join) {
                        $join->on('asistencia.id_func','=','designacion.id_func')->where('designacion.estatus','=','Activo');
                      })
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$desde)
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$hasta)
                      ->where(DB::raw("DATE_FORMAT(asistencia.hora_e,'%H:%i')"),$operadort,$valort)
                      ->where('designacion.id_divi', '=', $id_div)
                      ->where('asistencia.id_dep', $operador, $valor)
                      ->where(function ($query) use ($busqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $busqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $busqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $busqueda . '%');                                   
                      })
                      ->orderBy('asistencia.id_dep','asc')
                      ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                      ->orderBy('funcionarios.apellido','asc')                
                      ->orderBy('funcionarios.nombre','asc')                
                      ->limit($this->RegxPag)->get()->toArray();
                    }

                    return view('qrcodes.show', array('objasistencia'=>$objasistencia, 'busqueda'=>$busqueda, 'objfechacarbon'=>$objfechacarbon, 'desde'=>$desde, 'hasta'=>$hasta, 'objdependencia'=>$objdependencia, 'objdivision'=>$objdivision, 'id_temp'=>$id_temp, 'id_tempdiv'=>$id_tempdiv, 'horaactual'=>$horaactual, 'objfuncionarios'=>$objfuncionarios,'tipo'=>$tipo));

                  } else {
                    Session::flash('message-error','El rango de días para la consulta es de '.$rangodia.' días máximo');
                    return redirect('/buscar/scan2');
                  }

                }
            }
        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Realiza La Consulta');
             }
             return redirect('/');
        }         
    }

    public function ImprimirAsistencia(Request $request) 
    {        
        try{      

            $objhoraactual = DB::select('select now() as horaactual;');          
            $horaactual = $objhoraactual[0]->horaactual;

            $objfechacarbon = new fechacarbon();
            $ibusqueda = $request->ibusqueda;
            $idesde = $request->idesde;
            $ihasta = $request->ihasta;
            $tidesde = $request->idesde;
            $tihasta = $request->ihasta;
            $id_div = $request->id_div_;

            if(isset($request->id_dep_)){
              if ($request->id_dep_=="0") {
                $nombredependencia = "";                            
              } else {
                $objdependencia = dependencia::find($request->id_dep_);
                $nombredependencia = $objdependencia->nom_dep." - ".$objdependencia->ubicacion;              
              }
            } else {              
              $nombredependencia = "";                            
            }

            if(isset($request->id_div_)){
              if ($request->id_div_=="0") {
                $nombredivision = "";                            
              } else {
                $objdivision = division::find($request->id_div_);
                $nombredivision = $objdivision->descripcion;              
              }
            } else {              
              $nombredivision = "";                            
            }

            $operador = '>';
            $valor = '0';
            if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
             $operador = '=';
             $valor = Auth::user()->id_dep;
            } else {
                if ($request->id_dep_ != "0") {
                    $operador = '=';
                    $valor = $request->id_dep_;
                } else {
                    $operador = '>';
                    $valor = '0';
                }
            }            

            if ($idesde == "" && $ihasta == "" ) {
                $idesde =Carbon::now()->format('Y-m-d');
                $ihasta =Carbon::now()->format('Y-m-d');

                if ($id_div==0) { 
                  $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                  'asistencia.hora_e3', 'asistencia.hora_s3',
                  'asistencia.hora_e4', 'asistencia.hora_s4',
                  'asistencia.hora_e5', 'asistencia.hora_s5',
                  'asistencia.hora_e6', 'asistencia.hora_s6',
                  'asistencia.hora_e7', 'asistencia.hora_s7',
                  'asistencia.hora_e8', 'asistencia.hora_s8',
                  'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
                  ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                
                  ->where('asistencia.id_dep', $operador, $valor)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$idesde)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$ihasta)                
                  ->where(function ($query) use ($ibusqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $ibusqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $ibusqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $ibusqueda . '%');                                   
                  })
                  ->orderBy('asistencia.id_dep','asc')
                  ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                  ->orderBy('funcionarios.apellido','asc')                
                  ->orderBy('funcionarios.nombre','asc')                
                  ->limit($this->RegxPag)->get()->toArray();
                } else {
                  $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                  'asistencia.hora_e3', 'asistencia.hora_s3',
                  'asistencia.hora_e4', 'asistencia.hora_s4',
                  'asistencia.hora_e5', 'asistencia.hora_s5',
                  'asistencia.hora_e6', 'asistencia.hora_s6',
                  'asistencia.hora_e7', 'asistencia.hora_s7',
                  'asistencia.hora_e8', 'asistencia.hora_s8',
                  'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido','designacion.id_divi','designacion.estatus')
                  ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                
                  ->leftjoin('designacion', function ($join) {
                      $join->on('asistencia.id_func','=','designacion.id_func')->where('designacion.estatus','=','Activo');
                  })                  
                  ->where('designacion.id_divi','=',$id_div)
                  ->where('asistencia.id_dep', $operador, $valor)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$idesde)
                  ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$ihasta)                
                  ->where(function ($query) use ($ibusqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $ibusqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $ibusqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $ibusqueda . '%');                                   
                  })
                  ->orderBy('asistencia.id_dep','asc')
                  ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                  ->orderBy('funcionarios.apellido','asc')                
                  ->orderBy('funcionarios.nombre','asc')                
                  ->limit($this->RegxPag)->get()->toArray();
                }

                return PDF::loadView('qrcodes.imprimirasistencia', ['objasistencia' =>$objasistencia, 'objfechacarbon'=>$objfechacarbon, 'idesde'=>$tidesde, 'ihasta'=>$tihasta, 'nombredependencia'=>$nombredependencia, 'nombredivision'=>$nombredivision, 'horaactual'=>$horaactual])->setPaper("8.5x14", "landscape")->set_option("isPhpEnabled", true)->stream();
            } else {
                $data = ['desde'=>$idesde,'hasta'=>$ihasta];
                $requestvalido = new PeriodoRequest();
                $validation = Validator::make($data, $requestvalido->rules(), $requestvalido->messages());

                if ($validation->fails()) {
                  if ($id_div==0) { 
                    $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                    'asistencia.hora_e3', 'asistencia.hora_s3',
                    'asistencia.hora_e4', 'asistencia.hora_s4',
                    'asistencia.hora_e5', 'asistencia.hora_s5',
                    'asistencia.hora_e6', 'asistencia.hora_s6',
                    'asistencia.hora_e7', 'asistencia.hora_s7',
                    'asistencia.hora_e8', 'asistencia.hora_s8',
                    'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
                    ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                    
                    ->where('asistencia.id_dep', $operador, $valor)
                    ->where(function ($query) use ($ibusqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $ibusqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $ibusqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $ibusqueda . '%');                                   
                    })
                    ->orderBy('asistencia.id_dep','asc')
                    ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                    ->orderBy('funcionarios.apellido','asc')                
                    ->orderBy('funcionarios.nombre','asc')                
                    ->limit($this->RegxPag)->get()->toArray();
                  } else {
                    $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                    'asistencia.hora_e3', 'asistencia.hora_s3',
                    'asistencia.hora_e4', 'asistencia.hora_s4',
                    'asistencia.hora_e5', 'asistencia.hora_s5',
                    'asistencia.hora_e6', 'asistencia.hora_s6',
                    'asistencia.hora_e7', 'asistencia.hora_s7',
                    'asistencia.hora_e8', 'asistencia.hora_s8',
                    'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido','designacion.id_divi','designacion.estatus')
                    ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                    
                    ->leftjoin('designacion', function($join) {
                      $join->on('asistencia.id_func','=','designacion.id_func')->where('designacion.estatus','=','Activo');
                    })
                    ->where('designacion.id_divi','=',$id_div)
                    ->where('asistencia.id_dep', $operador, $valor)
                    ->where(function ($query) use ($ibusqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $ibusqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $ibusqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $ibusqueda . '%');                                   
                    })
                    ->orderBy('asistencia.id_dep','asc')
                    ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                    ->orderBy('funcionarios.apellido','asc')                
                    ->orderBy('funcionarios.nombre','asc')                
                    ->limit($this->RegxPag)->get()->toArray();
                  }

                  return PDF::loadView('qrcodes.imprimirasistencia', ['objasistencia' =>$objasistencia, 'objfechacarbon'=>$objfechacarbon, 'idesde'=>$tidesde, 'ihasta'=>$tihasta, 'nombredependencia'=>$nombredependencia, 'nombredivision'=>$nombredivision, 'horaactual'=>$horaactual])->setPaper("8.5x14", "landscape")->set_option("isPhpEnabled", true)->stream();

                } else {

                  $rangodefecha = area::select(DB::raw("TIMESTAMPDIFF(DAY,'".$idesde."','".$ihasta."') as dias"))->get()->first()->toArray();

                  if ($rangodefecha['dias']<=7) {

                    if ($id_div==0) { 
                      $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                      'asistencia.hora_e3', 'asistencia.hora_s3',
                      'asistencia.hora_e4', 'asistencia.hora_s4',
                      'asistencia.hora_e5', 'asistencia.hora_s5',
                      'asistencia.hora_e6', 'asistencia.hora_s6',
                      'asistencia.hora_e7', 'asistencia.hora_s7',
                      'asistencia.hora_e8', 'asistencia.hora_s8',
                      'asistencia.id_func', 'asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
                      ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                    
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$idesde)
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$ihasta)
                      ->where('asistencia.id_dep', $operador, $valor)
                      ->where(function ($query) use ($ibusqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $ibusqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $ibusqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $ibusqueda . '%');                                   
                      })
                      ->orderBy('asistencia.id_dep','asc')
                      ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                      ->orderBy('funcionarios.apellido','asc')                
                      ->orderBy('funcionarios.nombre','asc')                
                      ->limit($this->RegxPag)->get()->toArray();
                    } else {
                      $objasistencia = asistencia::select('asistencia.id', 'asistencia.id_dep', 'asistencia.hora_e', 'asistencia.hora_s', 'asistencia.hora_e2', 'asistencia.hora_s2', 
                      'asistencia.hora_e3', 'asistencia.hora_s3',
                      'asistencia.hora_e4', 'asistencia.hora_s4',
                      'asistencia.hora_e5', 'asistencia.hora_s5',
                      'asistencia.hora_e6', 'asistencia.hora_s6',
                      'asistencia.hora_e7', 'asistencia.hora_s7',
                      'asistencia.hora_e8', 'asistencia.hora_s8',
                      'asistencia.id_func','asistencia.observacion', 'funcionarios.cedula','funcionarios.nombre','funcionarios.apellido','designacion.id_divi','designacion.estatus')
                      ->leftjoin('funcionarios','asistencia.id_func','=','funcionarios.id')                    
                      ->leftjoin('designacion', function($join) {
                      $join->on('asistencia.id_func','=','designacion.id_func')->where('designacion.estatus','=','Activo');
                      })
                      ->where('designacion.id_divi','=',$id_div)                    
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'>=',$idesde)
                      ->where(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'<=',$ihasta)
                      ->where('asistencia.id_dep', $operador, $valor)
                      ->where(function ($query) use ($ibusqueda) {
                        $query->orwhere('funcionarios.cedula', 'like', '%'. $ibusqueda . '%')
                        ->orwhere( 'funcionarios.nombre', 'like', '%' . $ibusqueda . '%')
                        ->orwhere( 'funcionarios.apellido', 'like', '%' . $ibusqueda . '%');                                   
                      })
                      ->orderBy('asistencia.id_dep','asc')
                      ->orderBy(DB::raw("STR_TO_DATE(asistencia.hora_e,'%Y-%m-%d')"),'asc')                
                      ->orderBy('funcionarios.apellido','asc')                
                      ->orderBy('funcionarios.nombre','asc')                
                      ->limit($this->RegxPag)->get()->toArray();
                    }

                    return PDF::loadView('qrcodes.imprimirasistencia', ['objasistencia' =>$objasistencia, 'objfechacarbon'=>$objfechacarbon, 'idesde'=>$tidesde, 'ihasta'=>$tihasta, 'nombredependencia'=>$nombredependencia, 'nombredivision'=>$nombredivision, 'horaactual'=>$horaactual])->setPaper("8.5x14", "landscape")->set_option("isPhpEnabled", true)->stream();

                  } else {
                    Session::flash('message-error','El rango de días para la consulta es de 7 días máximo');
                    return redirect('/buscar/scan2');
                  }
                }
            }
        } catch (Exception $e) {  
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Realiza La Consulta');
             }
             return redirect('/');
        }         
    }

    public function GOAsistencia(Request $request) {
        if ($request->ajax()) {
          $id = $request->id;
          $observacion = $request->observacion;
          try {
            $objasistencia = asistencia::select('observacion')->where('id','=',$id)->get()->first()->toArray();

            if($objasistencia['observacion']==''){
            } else {
              $observacion = $objasistencia['observacion'].', '.$observacion;
            }


            $afectados = DB::table('asistencia')->where('id','=',$id)
                ->update(
                [
                      'observacion'=>$observacion,
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );
            if ($afectados>0) {
              return response()->json('Ok');
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

    public function RegistrarPermiso(Request $request) {
        if ($request->ajax()) {
          $id_func = $request->id_func;
          try {
            $result = funcionarios::select('id', 'id_dep')->where('id','=',$id_func)->get()->first();
            $id_dep = $result['id_dep'];

            $objfechaactual = DB::select('select curdate() as fechaactual;');          
            $fechaactual = $objfechaactual[0]->fechaactual;

            $data = [
              'id_func'=>$id_func,
              'id_dep'=>$id_dep,
              'hora_e'=>$fechaactual.' 08:00:00',
              'observacion'=>'Permiso aprobado'
              ];

            asistencia::create($data);
            return response()->json('Ok');

          } catch (Exception $e) {    
            return response()->json('Error');
          }          
        } else {
          return response()->json("Error");              
        }         
    }    
}
