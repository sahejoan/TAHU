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
use App\Models\cargofuncionario;
use App\Models\lcargos;
use App\Models\tipificaciones;
use App\Models\lfunciones;
use App\Models\funciones;
use App\Http\Controllers\Controller;

use App\Http\Requests\CargoFuncionarioCreateRequest;
use App\Http\Requests\CargoFuncionarioUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class CargoFuncionarioController extends Controller
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

        $this->middleware('role_or_permission:9_Ver_designacion|9_Designacion', ['only' => ['VerCargoFuncionario']]);
        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion|9_Borrar_designacion', ['only' => ['CrearCargoFuncionario','store','update','BorrarCargoFuncionario']]);        
    }  

    public function index()
    {     
             
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    public function CrearCargoFuncionario($id)
    {
        try {
            $objdesignacion = designacion::find($id);
            if (isset($objdesignacion['id'])) {

                $objcargofuncionario = cargofuncionario::select('id','id_desig')->where('id_desig','=',$id)->get()->first();

                if (!isset($objcargofuncionario)) {
                    $id_desig = $id;
                    $id_area = $objdesignacion['id_area'];

                    Session::put('id_desig', $objdesignacion['id']);
                    Session::save();

                    Session::put('id_area', $objdesignacion['id_area']);
                    Session::save();

                    $objtipificacion = new tipificaciones();        
                    $objestatuscontratado  = $objtipificacion->estatuscontratado ;
                    $objestatuscargo  = $objtipificacion->estatuscargo ;

                    $objlcargo = lcargos::select('descripcion','id')->orderBy('descripcion','asc')->limit($this->RegxPag)->pluck('descripcion','id')->toArray();
                    $objlfuncion = lfunciones::select('descripcion','id')->orderBy('descripcion','asc')->get();        

                    if (count($objlcargo)>0) {                                
                        $objlcargo = [''=>'Escribir [Enter Busca]']+$objlcargo;                  
                    } else {
                        $objlcargo = [''=>'Escribir [Enter Busca]'];
                    }

                    return view('cargofuncionario.crear',array('id_desig'=>$id_desig, 'id_area'=>$id_area, 'objestatuscontratado'=>$objestatuscontratado, 'objestatuscargo'=>$objestatuscargo, 'objlcargo'=>$objlcargo, 'objlfuncion'=>$objlfuncion));
                } else {
                    Session::flash('message-error','La designación de este funcionario tiene un cargo registrado');
                    return redirect('/designacion');
                }
            } else {                
                Session::flash('message-error','Solicitud invalida');               
                return redirect('/designacion');
            }
        } catch (Exception $e) {
            Session::flash('message-error','Solicitud invalida');               
            return redirect('/designacion');
        }
        
        return redirect('/designacion');                
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CargoFuncionarioCreateRequest $request)
    {
        try {
            $data = $request->all();

            $data2 = $data;

            $fecha = $data2['fecha_ex']." 00:00:00";
            $dt = new DateTime($fecha);
            $data['fecha_ex'] = $dt->format('Y-m-d');

            $fecha = $data2['fecha_inic']." 00:00:00";
            $dt = new DateTime($fecha);
            $data['fecha_ini'] = $dt->format('Y-m-d');

            if (!isset($data2['fecha_culm'])) {
                $fecha = $data2['fecha_culm']." 00:00:00";
                $dt = new DateTime($fecha);
                $data['fecha_culm'] = $dt->format('Y-m-d');
            } else {
                $data['fecha_culm'] = null;
            }
            
            $datacargo = ['id_desig'=>'','id_area'=>'','id_lcargo'=>'','fecha_ex'=>'','fecha_inic'=>'','fecha_culm'=>'','sta_contrato'=>'','sta_cargo'=>'','observaciones'=>''];
            $datacargo['id_desig']=Session::get('id_desig');
            $datacargo['id_area']=Session::get('id_area');
            $datacargo['id_lcargo']=$data['id_lcargo'];
            $datacargo['fecha_ex']=$data['fecha_ex'];
            $datacargo['fecha_inic']=$data['fecha_inic'];
            $datacargo['fecha_culm']=$data['fecha_culm'];
            $datacargo['sta_contrato']=$data['sta_contrato'];
            $datacargo['sta_cargo']=$data['sta_cargo'];
            $datacargo['observaciones']=$data['observaciones'];

            $distinct = $data['duallistbox_cargo'];
            $datafunciones = array_unique($distinct);

            DB::transaction(function() use ($datacargo, $datafunciones) {
                $nuevocargo = cargofuncionario::create($datacargo, $datafunciones);
                
                for ($i = 0; $i<count($datafunciones);$i++){
                    $nuevafuncion = funciones::create([
                        'id_cargo' => $nuevocargo->id,
                        'id_lfuncion' => $datafunciones[$i]
                    ]);
                }                
            });

            Session::flash('message','Registro se creo con éxito');    
            return redirect('/designacion');
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
            $request = new CargoFuncionarioUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();

            $data2 = $data;

            $objcargofuncionario = cargofuncionario::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion            

            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {
                $id_cargo = $objcargofuncionario['id'];

                $datacargo = ['id_lcargo'=>'','fecha_ex'=>'','fecha_inic'=>'','fecha_culm'=>'','sta_contrato'=>'','sta_cargo'=>'','observaciones'=>''];
                $datacargo['id_lcargo']=$data['id_lcargo'];
                $datacargo['fecha_ex']=$data['fecha_ex'];
                $datacargo['fecha_inic']=$data['fecha_inic'];
                $datacargo['fecha_culm']=$data['fecha_culm'];
                $datacargo['sta_contrato']=$data['sta_contrato'];
                $datacargo['sta_cargo']=$data['sta_cargo'];
                $datacargo['observaciones']=$data['observaciones'];

                $distinct = $data['duallistbox_cargo'];
                $datafunciones = array_unique($distinct);

                $objfunciones = funciones::where('id_cargo','=',$id)->get()->toArray();

                $afectado = null;
                $afectado = DB::transaction(function() use ($datacargo, $datafunciones, $objfunciones, $id_cargo) {
                    if ($datacargo['fecha_culm']!=null) {
                        $result1 = DB::table('cargo')->where('id','=',$id_cargo)->where('updated_at','=',Session::get('dateconcurrente'))
                        ->update(
                        [
                        'id_lcargo'=>$datacargo['id_lcargo'],
                        'fecha_ex'=>$datacargo['fecha_ex'],
                        'fecha_inic'=>$datacargo['fecha_inic'],
                        'fecha_culm'=>$datacargo['fecha_culm'],
                        'sta_contrato'=>$datacargo['sta_contrato'],
                        'sta_cargo'=>$datacargo['sta_cargo'],
                        'observaciones'=>$datacargo['observaciones'],
                        'updated_at'=>DB::raw('NOW()')
                        ]
                        );
                    } else {
                        $result1 = DB::table('cargo')->where('id','=',$id_cargo)->where('updated_at','=',Session::get('dateconcurrente'))
                        ->update(
                        [
                        'id_lcargo'=>$datacargo['id_lcargo'],
                        'fecha_ex'=>$datacargo['fecha_ex'],
                        'fecha_inic'=>$datacargo['fecha_inic'],
                        'sta_contrato'=>$datacargo['sta_contrato'],
                        'sta_cargo'=>$datacargo['sta_cargo'],
                        'observaciones'=>$datacargo['observaciones'],
                        'updated_at'=>DB::raw('NOW()')
                        ]
                        );                        
                    }

                    $result2 = 0;
                    for($i=0;$i<count($objfunciones);$i++){
                        $borrar = 1;
                        $idborrar = $objfunciones[$i]['id'];
                        foreach ($datafunciones as $value) {                        
                            if($objfunciones[$i]['id_lfuncion'] == $value){                            
                                $borrar = 0;
                            }
                        }
                        if ($borrar == 1){
                            $resultd = DB::table('funciones')->where('id','=',$idborrar)->delete();
                            $result2=$result2+1;
                        }                    
                    }

                    $result3 = 0;
                    foreach ($datafunciones as $value) {                        
                        $insertar = 1;
                        $idinsertar = $value;
                        for($i=0;$i<count($objfunciones);$i++){
                            if($value == $objfunciones[$i]['id_lfuncion']){                            
                                $insertar = 0;
                            }
                        }
                        if ($insertar == 1){
                            $nuevafuncion = funciones::create([
                                'id_cargo' => $id_cargo,
                                'id_lfuncion' => $idinsertar
                            ]);
                            $result3=$result3+1;
                        }                    
                    }
                    
                    return $result1+$result2+$result3;
                });

                if ($afectado > 0) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/designacion');
                } else {                    
                    Session::flash('message','Los datos no se actualizaron, Vuelva a realizar la operación');
                    return redirect('/designacion');                    
                }
            }            

            Session::flash('message','Registro se creo con éxito');    
            return redirect('/designacion');
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }

    public function BorrarCargoFuncionario($id_desig) {        
        if ($id_desig>0) {
            try {               
                $afectados = null;
                $objcargofuncionario = cargofuncionario::where('id_desig','=',$id_desig)->get()->first()->toArray();

                if (count($objcargofuncionario)>0) {                
                    $id_cargo = $objcargofuncionario['id'];
                } else {
                    return response()->json('Ok2');                 
                    $id_cargo = 0;
                }
                $afectados = DB::transaction(function() use ($id_cargo) {
                    $result1 =  funciones::where('id_cargo','=',$id_cargo)->delete();
                    $result2 =  cargofuncionario::where('id','=',$id_cargo)->delete();
                    return $result1+$result2;
                });
        
                if ($afectados > 0) 
                { 
                    return response()->json('Ok');
                } else {
                    return response()->json('Error');
                }            

            } catch (Exception $e) {    
                return response()->json('Error');
            }
        } else {
            return response()->json('Error');
        } 
    }

    public function EditarCargoFuncionario($id)
    {
        try {
            $objdesignacion = designacion::find($id);
            if (isset($objdesignacion['id'])) {
                $objcargofuncionario = cargofuncionario::where('id_desig','=',$id)->get()->first();

                if (isset($objcargofuncionario->id)) {
                    $id_desig = $id;
                    $id_area = $objdesignacion['id_area'];

                    Session::put('id_desig', $objdesignacion['id']);
                    Session::save();

                    Session::put('id_area', $objdesignacion['id_area']);
                    Session::save();

                    Session::put('dateconcurrente', $objcargofuncionario['updated_at']);
                    Session::save();

                    $objtipificacion = new tipificaciones();        
                    $objestatuscontratado  = $objtipificacion->estatuscontratado ;
                    $objestatuscargo  = $objtipificacion->estatuscargo ;

                    $objlcargo = lcargos::select('descripcion','id')->orderBy('descripcion','asc')->limit($this->RegxPag)->pluck('descripcion','id')->toArray();                    
                    $objlfuncion = lfunciones::select('descripcion','id',DB::raw('"0" as seleccionado'))->orderBy('descripcion','asc')->get()->toArray();        
                    $objfunciones = funciones::select('id','id_cargo','id_lfuncion')->where('id_cargo','=', $objcargofuncionario->id)->get()->toArray();

                    for($i=0;$i<count($objfunciones);$i++) {
                        for($j=0;$j<count($objlfuncion);$j++) {
                            if ($objfunciones[$i]['id_lfuncion'] == $objlfuncion[$j]['id']) {
                                $objlfuncion[$j]['seleccionado'] = "1";
                            }
                        }    
                    }

                    if (count($objlcargo)>0) {                                
                        $objlcargo = [''=>'Escribir [Enter Busca]']+$objlcargo;                  
                    } else {
                        $objlcargo = [''=>'Escribir [Enter Busca]'];
                    }

                    return view('cargofuncionario.editar',array('objcargofuncionario'=>$objcargofuncionario, 'id_desig'=>$id_desig, 'id_area'=>$id_area, 'objestatuscontratado'=>$objestatuscontratado, 'objestatuscargo'=>$objestatuscargo, 'objlcargo'=>$objlcargo, 'objlfuncion'=>$objlfuncion, 'objfunciones'=>$objfunciones));
                } else {
                    Session::flash('message-error','La designación de este funcionario no tiene un cargo registrado');
                    return redirect('/designacion');
                }
            } else {                
                Session::flash('message-error','Solicitud invalida');               
                return redirect('/designacion');
            }
        } catch (Exception $e) {                        
            Session::flash('message-error','Solicitud invalida');               
            return redirect('/designacion');
        }
        
        return redirect('/designacion');                
    }          

    public function VerCargoFuncionario($id) {        
        
        if ($id > 0) {

            try {                  
                $objdesignacion = designacion::find($id);
                
                if (isset($objdesignacion['id'])) {                    
                    $objcargofuncionario = cargofuncionario::where('id_desig','=',$id)->get()->first();

                    if (isset($objcargofuncionario['id'])) {
                        $id_desig = $id;
                        $id_area = $objdesignacion['id_area'];

                        $objlcargo = lcargos::select('descripcion','id')->where('id','=',$objcargofuncionario->id_lcargo)->get()->first()->toArray();                        
                        $objfunciones = funciones::select('funciones.id','funciones.id_cargo','funciones.id_lfuncion','lfuncion.descripcion')
                        ->join('lfuncion','funciones.id_lfuncion','=','lfuncion.id')
                        ->where('funciones.id_cargo','=', $objcargofuncionario->id)->get()->toArray();

                        $nom_cargo = $objlcargo['descripcion'];

                        $fecha_ex = $objcargofuncionario->fecha_ex;
                        $fecha_inic = $objcargofuncionario->fecha_inic;
                        $fecha_culm = $objcargofuncionario->fecha_culm;
                        $sta_cargo = $objcargofuncionario->sta_cargo;
                        $sta_contrato = $objcargofuncionario->sta_contrato;
                        $observaciones = $objcargofuncionario->observaciones;

                        $objresult = [
                            'nom_cargo' => $nom_cargo,
                            'fecha_ex' => $fecha_ex,
                            'fecha_inic' => $fecha_inic,
                            'fecha_culm' => $fecha_culm,
                            'sta_cargo' => $sta_cargo,
                            'sta_contrato' => $sta_contrato,
                            'observaciones' => $observaciones,
                            'listadefunciones' => $objfunciones
                            ];

                        return response()->json($objresult);
                                                                         
                    } else {
                        return response()->json('Error');                                                 
                    }

                } else {                
                    return response()->json('Error');                              
                }                

                return response()->json('Error');                                              

            } catch (Exception $e) {
                return response()->json('Error');              
            }

        } else {
          return response()->json("Error");
        }
    }
}