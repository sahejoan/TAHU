<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use SimpleSoftwareIO\QrCode\QrCodeServiceProvider;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\funcionarios;
use App\Models\dependencia;
use App\Models\familiares;
use App\Models\imprimirQR;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\tipificaciones;
use App\Http\Requests\FuncionarioCreateRequest;
use App\Http\Requests\FuncionarioUpdateRequest; 
 
use App\Http\Controllers;
use PDF;
use  Barryvdh\DomPDF\ServiceProvider;
use Auth;
class QRCodeController extends Controller
{	
  public $RegxPag = 1000;
  public QrCode $qrCode;
	public function __construct()
  {
        $this->middleware('auth');

        $this->middleware('role_or_permission:12_Ver_Asistencias', ['only' => ['index']]);        
        $this->middleware('role_or_permission:12_Listar_Asistencia', ['only' => ['index']]);        
        $this->middleware('role_or_permission:20_Generaqr', ['only' => ['listadoqr','getListadoqr','pdf','pdfs','tarjetapdf']]);        
        $this->middleware('role_or_permission:10_Crear_funcionarios|10_Editar_funcionarios', ['only' => ['crearQr']]);        
  }  

  public function index() {
      try {
        $link = "Bienvenidos: SENIAT Region Los Llanos ";
        $lineadetiempo = env('APP_LINEA_DE_TIEMPO');
        return view('qrcodes.index',array('lineadetiempo'=>$lineadetiempo));
      } catch (Exception $e) {    
        Session::flash('message-error','Ocurrio un problema, no se pudo generar el reporte');
        return redirect('/admin');
      }     
  }

  public function create() {
  }

  public function edit($id) {
  }

  public function store(Request $request) {
  }

  public function update(Request $request) {
  }

  public function destroy($id) {
  }

  public function show($id) {
  }


  //Genera vista para opciones de impresion QR 
  public function listadoqr(Request $request)
  {    
    try {
      $buscar = $request['buscarv'];

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
        $objdependencia = ['0'=>'Todos']+$objdependencia;                  
      } else {
        $objdependencia = ['0'=>'Todos'];
      }

      $objfuncionarios = funcionarios::orwhere('cedula', 'like', '%'. $buscar . '%')
      ->orwhere( 'nombre', 'like', '%' . $buscar . '%')
      ->orwhere( 'apellido', 'like', '%' . $buscar . '%')                                   
      ->orderBy('apellido','asc')
      ->orderBy('nombre','asc')
      ->limit($this->RegxPag)->get();

      $imprimirQR = new imprimirQR();      
      return view('qrcodes.listadoqr',array('objfuncionarios'=>$objfuncionarios, 'objdependencia'=>$objdependencia, 'id_temp'=>'', 'buscar'=>$buscar)); 
    } catch (Exception $e) {    
      Session::flash('message-error','Ocurrio un problema, no se pudo generar el reporte');
      return redirect('/admin');
    }
  }

    public function getListadoqr(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');
          $id_dep = $request->id_dep;

          //para jsindexversion1
          $ver1 = "funcionarios.id";

          // datatable column index  => database column name
          $columns = array(          
            0 => 'cedula',
            1 => 'apellido',
            2 => 'nombre',            
            3 => 'ver',
          );

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

          $objfuncionario =  funcionarios::query();

          //para jsindexversion1          
          $objfuncionario->select('funcionarios.id','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido',
          DB::raw($ver1.' as ver'));

          //Cuenta registros
          $totalregistros = funcionarios::select('funcionarios.id')
          ->where('funcionarios.id_dep',$operador,$valor)
          ->where(function ($query) use ($search) {
            $query->orWhere('funcionarios.nombre', 'like', '%'.$search.'%')
            ->orWhere('funcionarios.apellido', 'like', '%'.$search.'%')
            ->orWhere('funcionarios.cedula', 'like', '%'.$search.'%');
          })
          ->count('funcionarios.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = funcionarios::select('funcionarios.id')
            ->where('funcionarios.id_dep',$operador,$valor)            
            ->where(function ($query) use ($search) {
              $query->orWhere('funcionarios.nombre', 'like', '%'.$search.'%')
              ->orWhere('funcionarios.apellido', 'like', '%'.$search.'%')
              ->orWhere('funcionarios.cedula', 'like', '%'.$search.'%');
            })
            ->count(DB::raw('funcionarios.id'));
          }

          //Carga los datos
          $objfuncionario
          ->where('funcionarios.id_dep',$operador,$valor)
          ->where(function ($query) use ($search) {
            $query->orWhere('funcionarios.nombre', 'like', '%'.$search.'%')
            ->orWhere('funcionarios.apellido', 'like', '%'.$search.'%')
            ->orWhere('funcionarios.cedula', 'like', '%'.$search.'%');
          });

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

  //Generar Lista de Qr en PDF formato por lotes de varias pagina tipo carta
  public function ListaPdf(Request $request)
  {    
    try {
      $buscar = $request['buscarl'];

      $id_dep = $request['id_depl'];
      $operador = ">";
      $valor = "0";
      if ($id_dep==0) {
        $operador = ">";
        $valor = "0";
      } else {
        $operador = "=";
        $valor = $id_dep;
      }

      $imprimirQR = new imprimirQR();

      $objfuncionarios = funcionarios::select('funcionarios.nuevoqr','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
      ->where(function ($query) use ($buscar) {
        $query->orwhere('cedula', 'like', '%'. $buscar . '%')
        ->orwhere( 'nombre', 'like', '%' . $buscar . '%')
        ->orwhere( 'apellido', 'like', '%' . $buscar . '%');                                   
      })
      ->where('funcionarios.id_dep',$operador,$valor)
      ->orderBy('apellido','asc')
      ->orderBy('nombre','asc')
      ->get()->toArray();

      return PDF::loadView('qrcodes.pdf', ['objfuncionarios' =>$objfuncionarios, 'buscar'=>$buscar, 'imprimirQR'=>$imprimirQR])->setPaper("A4", "portrait")->set_option("isPhpEnabled", true)->stream();
      //return $pdf->stream();              
    } catch (Exception $e) {    
      Session::flash('message-error','Ocurrio un problema, no se pudo generar el reporte');
      return redirect('/');
    }  
  }

  //Genera Lista de QR en PDF formato tarjeta
  public function ListaPdfs(Request $request)
  {
    try { 
      $buscar = $request['buscarls'];

      $id_dep = $request['id_depls'];
      $operador = ">";
      $valor = "0";
      if ($id_dep==0) {
        $operador = ">";
        $valor = "0";
      } else {
        $operador = "=";
        $valor = $id_dep;
      }

      $imprimirQR = new imprimirQR();

      $objfuncionarios = funcionarios::select('funcionarios.nuevoqr','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
      ->where(function ($query) use ($buscar) {
        $query->orwhere('cedula', 'like', '%'. $buscar . '%')
        ->orwhere( 'nombre', 'like', '%' . $buscar . '%')
        ->orwhere( 'apellido', 'like', '%' . $buscar . '%');                                   
      })
      ->where('funcionarios.id_dep',$operador,$valor)
      ->orderBy('apellido','asc')
      ->orderBy('nombre','asc')
      ->get()->toArray();

      $paper_size = array(0,0,159,255);
      return PDF::loadView('qrcodes.tarjetaqrpdfs', ['objfuncionarios' =>$objfuncionarios, 'imprimirQR'=>$imprimirQR])->setPaper($paper_size)->stream();
      //return $pdf->stream();              
    } catch (Exception $e) {    
      Session::flash('message-error','Ocurrio un problema, no se pudo generar el reporte');
      return redirect('/');
    }  
  }

  //Genera Lista de QR en PDF formato tarjeta
  public function ListaPdfsAgrupados(Request $request)
  {
    try { 
      $buscar = $request['buscarlsg'];

      $id_dep = $request['id_deplsg'];
      $operador = ">";
      $valor = "0";
      if ($id_dep==0) {
        $operador = ">";
        $valor = "0";
      } else {
        $operador = "=";
        $valor = $id_dep;
      }

      $imprimirQR = new imprimirQR();

      $objfuncionarios = funcionarios::select('funcionarios.nuevoqr','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')
      ->where(function ($query) use ($buscar) {
        $query->orwhere('cedula', 'like', '%'. $buscar . '%')
        ->orwhere( 'nombre', 'like', '%' . $buscar . '%')
        ->orwhere( 'apellido', 'like', '%' . $buscar . '%');                                   
      })
      ->where('funcionarios.id_dep',$operador,$valor)
      ->orderBy('apellido','asc')
      ->orderBy('nombre','asc')
      ->get()->toArray();

      return PDF::loadView('qrcodes.grupostarjetaqrpdfs', ['objfuncionarios' =>$objfuncionarios, 'imprimirQR'=>$imprimirQR])->setPaper("A4", "landscape")->stream();
      //return $pdf->stream();              
    } catch (Exception $e) {    
      Session::flash('message-error','Ocurrio un problema, no se pudo generar el reporte');
      return redirect('/');
    }  
  }

  //Genera impresion Qr en PDF de solo una tarjeta
  public function tarjetapdf($id)
  {
    try { 
      $imprimirQR = new imprimirQR();

      $objfuncionarios = funcionarios::select('funcionarios.nuevoqr','funcionarios.cedula','funcionarios.nombre','funcionarios.apellido')->where('id', '=', $id)
      ->orderBy('apellido','asc')
      ->orderBy('nombre','asc')
      ->get()->toArray();

      $paper_size = array(0,0,159,255);

      return PDF::loadView('qrcodes.tarjetaqrpdf', ['objfuncionarios' =>$objfuncionarios, 'imprimirQR'=>$imprimirQR])->setPaper($paper_size)->stream();
      //return $pdf->stream();              
    } catch (Exception $e) {    
      Session::flash('message-error','Ocurrio un problema, no se pudo generar el reporte');
      return redirect('/');
    }
  }
  
  public function crearQr(Request $request)
  {
      if ($request->ajax()) {
        try {
          $cedula = $request->cedula;
          $nombre = $request->nombre;
          $apellido = $request->apellido;

          $writer = new PngWriter();

          $text = $cedula;
          $etiqueta = $cedula.'--'.$nombre.'--'.$apellido;

          $qrCode = new QrCode($text);
          $qrCode->setSize(300);   
          $qrCode->setEncoding(new Encoding('UTF-8'));
          $qrCode->setMargin(10);
          $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelLow());
          $qrCode->setRoundBlockSizeMode(new RoundBlockSizeModeMargin());
          $qrCode->setForegroundColor(new Color(0, 0, 0));
          $qrCode->setBackgroundColor(new Color(255, 255, 255));

          //$logo = Logo::create(env('APP_URL_NAME').'\\image\\qrcode\\'.'logo.png')
          //->setResizeToWidth(100);

          if (env('APP_IMAGEN_QR')==true) {
            $logo = Logo::create(env('APP_URL_NAME').'\\image\\qrcode\\'.'logo.png')
            ->setResizeToWidth(110);
          } else {
            $logo=null;
          }

          $label = Label::create($etiqueta)
          ->setTextColor(new Color(255, 0, 0))
          ->setFont(new NotoSans(7));

          $result = $writer->write($qrCode, $logo, $label);

          // Directly output the QR code
          //header('Content-Type: '.$result->getMimeType());
          //echo $result->getString();

          // Save it to a file
          //$result->saveToFile(env('APP_URL_NAME').'\\image\\qrcode\\'.'qrcode.png');

          $dataUri = $result->getDataUri();

          return response()->json($dataUri);              

        } catch (Exception $e) {    

          return response()->json("Error");              
          
        }        
      } else {
        return response()->json("Error");              
      }    
   }  
}
