<?php namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\User;
use App\Models\User2;
use App\Models\funcionarios;
use App\Models\asistencia;
use App\Models\asistencia2;
use App\Models\asistenciamovil;
use App\Models\designacion;
use App\Models\transferirasistencia;
use App\Models\fechacarbon;
use DB;
use Cookie;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Str;

class ApiDescargarController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */

   public function __construct()
   {

   }

  public function index()
  {

  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request) {

  }

  public function DescargaUsuarios(Request $request)
  { 
  	$obj = User2::select('id','id_dep','email','password')->orderby('id')->get()->toArray();
  	if (isset($obj)){
  		if (count($obj)>0) {        
  			$objr = json_encode($obj);  		  	
  			return response()->json($objr,200);
  		} else {
        return response()->json('Null',200);
  		}		
  	} else {
		  return response()->json("Denegado",200);
  	}    
  }


  public function DescargaFuncionarios(Request $request)
  {
  	try {
    $obj = funcionarios::select('id','cedula','nombre','apellido','id_dep','ancho','alto','tipoimagen','foto')->where('id','>','0')->orderby('id')->get()->toArray();
  	if (isset($obj)){
  		if (count($obj)>0) {
        	for($i=0;$i<count($obj);$i++) {
          		//$tipoimagen = $obj[$i]['tipoimagen'];
              $tipoimagen = '';
          		if($tipoimagen!='') {
            		$foto = $obj[$i]['foto'];
            		//$urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
            		$urlfoto = base64_encode($foto);          
          		} else {
             		$urlfoto = '';
          		}

          		$obj[$i]['foto'] = $urlfoto;
        	}

  			$objr = json_encode($obj);  		  	
  			return response()->json($objr,200);
  		} else {
			return response()->json('Null',200);
  		}
  	} else {
		return response()->json("Denegado",200);
  	} 

    } catch (Exception $e) {
     return response()->json('Denegado',200);          
    }
  }


  public function DescargaelFuncionario(Request $request)
  {
    $cedula = $request['data'];

    $obj = funcionarios::select('id','cedula','nombre','apellido','id_dep','ancho','alto','tipoimagen','foto')->where('cedula','=',$cedula)->orderby('id')->get()->toArray();
    if (isset($obj)){
      if (count($obj)>0) {
          for($i=0;$i<count($obj);$i++) {
              $tipoimagen = $obj[$i]['tipoimagen'];
              if($tipoimagen!='') {
                $foto = $obj[$i]['foto'];
                //$urlfoto = "data:image/".$tipoimagen.";base64,".base64_encode($foto);
                $urlfoto = base64_encode($foto);          
              } else {
                $urlfoto = '';
              }

              $obj[$i]['foto'] = $urlfoto;
          }

        $objr = json_encode($obj);          
        return response()->json($objr,200);
      } else {
      return response()->json('Null',200);
      }
    } else {
    return response()->json("Denegado",200);
    }          
  }

  public function DescargaAsistencia(Request $request)
  {
    $lista = $request['data'];

    if (isset($lista)) { 
    } else {
      return response()->json("noOk",200);
    }

    try {      
      $fec = '';
      $consult = transferirasistencia::select('id','bloqueado',DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%m:%s") as ultimaactualizacion'))
      ->where('id','=','1')->get()->toArray();
      $responder = 0;

      if(count($consult)==1) {
        if ($consult[0]['bloqueado']==1) {
          $diferencia = transferirasistencia::select('bloqueado',DB::raw('(timestampdiff(minute, updated_at, now())) as diferencia'))->get()->toArray();

          if (($diferencia[0]['diferencia']>2) && ($diferencia[0]['bloqueado'] == 1)) {
            $res = DB::table('transferidoasistencia')
            ->where('id','=','1')      
            ->update([
              'id_user'=> '0',
              'bloqueado'=> '0',
              'updated_at'=>DB::raw('NOW()')
              ]
            );
          } else {
            return response()->json("noOk",200);                    
          }
        }

        $res = DB::table('transferidoasistencia')
        ->where('id','=','1')      
        ->update([
          'id_user'=> Auth::user()->id,
          'bloqueado'=> '1',
          'updated_at'=> DB::raw('now()')                
          ]
        );
        $responder=$res;
      }

      if ($responder == 0) {
        return response()->json("noOk",200);          
      }

      $consult = transferirasistencia::select('id','bloqueado',DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%m:%s") as ultimaactualizacion'))
      ->where('id','=','1')->get()->toArray();
      $fec = $consult[0]['ultimaactualizacion'];

      //si lista vacia
      if (isset($lista)) { 
        //iniciar transaccion
        //$connection = DB::connection('mysql');
        //$connection->beginTransaction();
        $hacercommit = 0;


        foreach ($lista as $p) {

          $objdesignacion = funcionarios::select('id_dep')->where('id','=',$p['id_func'])->get()->first()->toArray();

          if (isset($objdesignacion)) {             
            if(isset($objdesignacion['id_dep'])) {

              $obj = asistencia::select('id','id_func','id_dep','observacion','hora_e','hora_s','hora_e2','hora_s2','hora_e3','hora_s3','hora_e4','hora_s4','hora_e5','hora_s5','hora_e6','hora_s6','hora_e7','hora_s7','hora_e8','hora_s8')
              ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
              ->where('id_func','=',$p['id_func'])
              ->where('id_dep','=',$p['id_dep'])
              ->limit(1)->get()->toArray();

              $incluido = 0;

              //si registro de asistecia es cero se agrega
              if (count($obj)==0) {  

                $data = [
                  'hora_e'=>substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                  'id_func'=>$p['id_func'],
                  'id_dep'=>$p['id_dep'],
                  'o1'=>$p['observacion'],
                  'observacion'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                  'created_by'=>$p['created_by'],
                  'updated_by'=>$p['updated_by'],
                  'created_at'=>date("Y-m-d H:m:s", strtotime(substr($p['created_at'],0,10).' '.substr($p['created_at'],11,8))),
                  'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                ];
                asistencia2::create($data);

                $data2 = [
                  'hora'=>substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                  'id_func'=>$p['id_func'],
                  'id_dep'=>$p['id_dep'],
                  'observacion'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                  'enviado'=>'1',
                  'created_by'=>$p['created_by'],
                  'updated_by'=>$p['updated_by'],
                  'created_at'=>date("Y-m-d H:m:s", strtotime(substr($p['created_at'],0,10).' '.substr($p['created_at'],11,8))),
                  'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                ];
                asistenciamovil::create($data2);

                $incluido = 1;

              //si no s verifica para actualizar los campos
              } else {

                //inicio swicht1
                switch (substr($p['hora'],0,10).' '.substr($p['hora'],11,8)) {
                  case substr($obj[0]['hora_e'],0,10).' '.substr($obj[0]['hora_e'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s'],0,10).' '.substr($obj[0]['hora_s'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_e2'],0,10).' '.substr($obj[0]['hora_e2'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s2'],0,10).' '.substr($obj[0]['hora_s2'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_e3'],0,10).' '.substr($obj[0]['hora_e3'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s3'],0,10).' '.substr($obj[0]['hora_s3'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_e4'],0,10).' '.substr($obj[0]['hora_e4'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s4'],0,10).' '.substr($obj[0]['hora_s4'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_e5'],0,10).' '.substr($obj[0]['hora_e5'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s5'],0,10).' '.substr($obj[0]['hora_s5'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_e6'],0,10).' '.substr($obj[0]['hora_e6'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s6'],0,10).' '.substr($obj[0]['hora_s6'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_e7'],0,10).' '.substr($obj[0]['hora_e7'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s7'],0,10).' '.substr($obj[0]['hora_s7'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_e8'],0,10).' '.substr($obj[0]['hora_e8'],11,8): $incluido=1;
                    break;
                  case substr($obj[0]['hora_s8'],0,10).' '.substr($obj[0]['hora_s8'],11,8): $incluido=1;
                    break;                
                  default:
                    break;
                }//fin swicht1

                if ($incluido==0) {
                  $nulo=null;

                  $observacion = $obj[0]['observacion'];
                  if ($p['observacion']!=null) {
                    $observacion = $obj[0]['observacion'].', '.$p['observacion'];
                  }

                  $almovil=0;
                  //inicio swicht2
                  switch ($nulo) {                                  
                    case $obj[0]['hora_s']:                    
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o2'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion
                        ]
                      );
                      $almovil=1;
                      break;                    

                    case $obj[0]['hora_e2']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_e2'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o3'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_s2']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s2'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o4'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;
                      break;

                    case $obj[0]['hora_e3']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_e3'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o5'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_s3']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s3'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o6'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_e4']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_e4'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o7'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_s4']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s4'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o8'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;
                      break;

                    case $obj[0]['hora_e5']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_e5'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o9'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;  

                    case $obj[0]['hora_s5']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s5'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o10'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_e6']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_e6'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o11'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_s6']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s6'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o12'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_e7']:
                       $afectados = DB::table('asistencia')
                       ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                       ->where('id_func','=',$p['id_func'])
                       ->where('id_dep','=',$p['id_dep'])
                       ->update(
                        [
                          'hora_e7'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o13'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                       );
                      $almovil=1;                       
                      break;

                    case $obj[0]['hora_s7']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s7'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o14'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_e8']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_e8'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o15'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    case $obj[0]['hora_s8']:
                      $afectados = DB::table('asistencia')
                      ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'),'=',substr($p['hora'],0,10))
                      ->where('id_func','=',$p['id_func'])
                      ->where('id_dep','=',$p['id_dep'])
                      ->update(
                        [
                          'hora_s8'=> substr($p['hora'],0,10).' '.substr($p['hora'],11,8),
                          'updated_by'=>$p['updated_by'],
                          'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                          'o16'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                          'observacion'=>$observacion                                    
                        ]
                      );
                      $almovil=1;                      
                      break;

                    default:                      
                      break;
                  } //fin swicht2

                  if ($almovil==1){
                    $data2 = [
                      'hora'=>$p['hora'],
                      'id_func'=>$p['id_func'],
                      'id_dep'=>$p['id_dep'],                    
                      'observacion'=>$p['observacion']==null ? null:addslashes($p['observacion']),
                      'enviado'=>'1',
                      'created_by'=>$p['created_by'],
                      'updated_by'=>$p['updated_by'],
                      'created_at'=>date("Y-m-d H:m:s", strtotime(substr($p['created_at'],0,10).' '.substr($p['created_at'],11,8))),
                      'updated_at'=>date("Y-m-d H:m:s", strtotime(substr($p['updated_at'],0,10).' '.substr($p['updated_at'],11,8))),
                    ];
                    asistenciamovil::create($data2);
                  } //fin swicht2

                }

              }//fin si registro de asistecia es cero se agrega

            }

          }

        }

        //si es el miso usuario de la transaccion para hacer commict
        $consult = transferirasistencia::select('id','id_user','bloqueado',DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%m:%s") as ultimaactualizacion'))
        ->where('id','=','1')->get()->toArray();      
        if (($consult[0]['ultimaactualizacion']==$fec) && ($consult[0]['id_user']==Auth::user()->id)) {
          $hacercommit = 1;
        }

        //si hacercommit        
        $hacercommit = 1;       
        if ($hacercommit==1) {
          $connection=1;
          //si existe una connection
          if (isset($connection)) {
            //$connection->commit();

            //ordenar cada registro de asistencia los campos por fecha y hora
            foreach ($lista as $p) {
              $obj = asistencia2::select('id','id_func','id_dep','observacion',
                'hora_e','hora_s','hora_e2','hora_s2','hora_e3',
                'hora_s3','hora_e4','hora_s4','hora_e5','hora_s5',
                'hora_e6','hora_s6','hora_e7','hora_s7','hora_e8','hora_s8',
                'o1','o2','o3','o4','o5','o6','o7','o8','o9','o10','o11','o12','o13','o14','o15','o16'
              )
              ->where('id_func','=',$p['id_func'])
              ->where('id_dep','=',$p['id_dep'])
              ->where(DB::raw('DATE_FORMAT(hora_e, "%Y-%m-%d")'), '=', substr($p['hora'],0,10))
              ->get()->toArray();

              $ordenar = [   
                'hora_e'=> $obj[0]['hora_e'] == null ? 'a' : $obj[0]['hora_e'],
                'hora_s'=> $obj[0]['hora_s'] == null ? 'a' : $obj[0]['hora_s'],
                'hora_e2'=> $obj[0]['hora_e2'] == null ? 'a' : $obj[0]['hora_e2'],
                'hora_s2'=> $obj[0]['hora_s2'] == null ? 'a' : $obj[0]['hora_s2'],
                'hora_e3'=> $obj[0]['hora_e3'] == null ? 'a' : $obj[0]['hora_e3'],
                'hora_s3'=> $obj[0]['hora_s3'] == null ? 'a' : $obj[0]['hora_s3'],
                'hora_e4'=> $obj[0]['hora_e4'] == null ? 'a' : $obj[0]['hora_e4'],
                'hora_s4'=> $obj[0]['hora_s4'] == null ? 'a' : $obj[0]['hora_s4'],
                'hora_e5'=> $obj[0]['hora_e5'] == null ? 'a' : $obj[0]['hora_e5'],
                'hora_s5'=> $obj[0]['hora_s5'] == null ? 'a' : $obj[0]['hora_s5'],
                'hora_e6'=> $obj[0]['hora_e6'] == null ? 'a' : $obj[0]['hora_e6'],
                'hora_s6'=> $obj[0]['hora_s6'] == null ? 'a' : $obj[0]['hora_s6'],
                'hora_e7'=> $obj[0]['hora_e7'] == null ? 'a' : $obj[0]['hora_e7'],
                'hora_s7'=> $obj[0]['hora_s7'] == null ? 'a' : $obj[0]['hora_e7'],
                'hora_e8'=> $obj[0]['hora_e8'] == null ? 'a' : $obj[0]['hora_e8'],
                'hora_s8'=> $obj[0]['hora_s8'] == null ? 'a' : $obj[0]['hora_s8']
              ];

              $ordenar2 = [
                'hora_e'=>$obj[0]['o1'],
                'hora_s'=>$obj[0]['o2'],
                'hora_e2'=>$obj[0]['o3'],
                'hora_s2'=>$obj[0]['o4'],
                'hora_e3'=>$obj[0]['o5'],
                'hora_s3'=>$obj[0]['o6'],
                'hora_e4'=>$obj[0]['o7'],
                'hora_s4'=>$obj[0]['o8'],
                'hora_e5'=>$obj[0]['o9'],
                'hora_s5'=>$obj[0]['o10'],
                'hora_e6'=>$obj[0]['o11'],
                'hora_s6'=>$obj[0]['o12'],
                'hora_e7'=>$obj[0]['o13'],
                'hora_s7'=>$obj[0]['o14'],
                'hora_e8'=>$obj[0]['o15'],
                'hora_s8'=>$obj[0]['o16']             
              ];

              asort($ordenar);

              $data=[];
              $observacion='';
              $i = 1;
              $x = 0;

              $secuencia = [
                'hora_e',
                'hora_s',
                'hora_e2',
                'hora_s2',
                'hora_e3',
                'hora_s3',
                'hora_e4',
                'hora_s4',
                'hora_e5',
                'hora_s5',
                'hora_e6',
                'hora_s6',
                'hora_e7',
                'hora_s7',
                'hora_e8',
                'hora_s8' 
              ];

              foreach ($ordenar as $key => $val) {
                $data = $data + [$secuencia[$x]=>$val=='a' ? null : $val];
                $data = $data + ['o'.$i=>$ordenar2[$key]];
                if ($ordenar2[$key]!='') {
                  if ($observacion=='') {
                    $observacion = trim($ordenar2[$key]); 
                  } else {
                    $observacion = trim($observacion).', '.trim($ordenar2[$key]);
                  }           
                }             
                $i++;
                $x++;
              }

              $data = $data + ['observacion'=>$observacion];

              $res2 = DB::table('asistencia')
              ->where('id','=',$obj[0]['id'])      
              ->update(
                $data+['updated_at'=>DB::raw('NOW()')]               
              );             
            }
            //fin for ordenar

            $res = DB::table('transferidoasistencia')
              ->where('id','=','1')      
              ->where('id_user','=',Auth::user()->id)
              ->where('bloqueado','=','1')
              ->where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%m:%s")'),'=',$fec)
              ->update([
                'id_user'=> '0',
                'bloqueado'=> '0',
                'updated_at'=>DB::raw('NOW()')
                ]
            );             

            return response()->json("Ok",200);                
          
          //sino existe un connection
          } else {
            return response()->json("noOk",200);                          
          }

        //sino hacercommit
        } else {          
          return response()->json("noOk",200);                          

          //if (isset($connection)){
            //$connection->rollback();
            //return response()->json("noOk5",200);                          
          //} else {
            //return response()->json("noOk6",200);                          
          //}

        }

        return response()->json('noOk',200);
      
      }//fin si lista vacia

    } catch (Exception $e) {
      //if (isset($connection)){$connection->rollback();}
      return response()->json('noOk',200);          
    }
  }  

  public function Sincronizar() {
    $objfechacarbon = new fechacarbon();
    $obj = $objfechacarbon->fecha_tiempo_actual_str24();
    return response()->json($obj,200);          
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id) 
  {
    
  }

}
?>