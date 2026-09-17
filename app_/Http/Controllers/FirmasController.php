<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\firmas;
use App\Models\tipificaciones;
use App\Models\dependencia;
use App\Http\Controllers\Controller;

use App\Http\Requests\FirmasCreateRequest;
use App\Http\Requests\FirmasUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class FirmasController extends Controller
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

        $this->middleware('role_or_permission:8_Buscar_firmas|8_Ver_firmas|8_Crear_firmas|8_Editar_firmas|8_Borrar_firmas', ['only' => ['index']]);
        $this->middleware('role_or_permission:8_Firmas', ['only' => ['index']]);
        $this->middleware('role_or_permission:8_Buscar_firmas', ['only' => ['BuscarFirmas','getFirmas']]);
        $this->middleware('role_or_permission:8_Crear_firmas', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:8_Editar_firmas', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:8_Borrar_firmas', ['only' => ['destroy']]);                        
        
        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion', ['only' => 'BuscarLaFirma']);                
    }  

    public function index()
    {     
        try{

          return view('firmas.index', array('busqueda'=>''));

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

    public function getFirmas(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('8_Editar_firmas')) {
            $editar1 = "firmas.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('8_Borrar_firmas')) {
            $borrar1 = "firmas.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'firma',
            1 => 'ubicacion',            
            2 => 'actual',            
            3 => 'editar',
            4 => 'borrar'            
          );

          $objfirma =firmas::query();

          //para jsindexversion1          
          $objfirma->select('firmas.id','firmas.firma','firmas.actual','region.nom_reg','dependencia.nom_dep','dependencia.ubicacion',
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'))
          ->leftjoin('dependencia','firmas.id_dep','=','dependencia.id')
          ->leftjoin('region','dependencia.id_reg','=','region.id');

          //Cuenta registros
          $totalregistros = firmas::select('firmas.id','firmas.firma','region.nom_reg','dependencia.nom_dep','dependencia.ubicacion')
          ->leftjoin('dependencia','firmas.id_dep','=','dependencia.id')
          ->leftjoin('region','dependencia.id_reg','=','region.id')
          ->orWhere('firmas.firma', 'like', '%'.$search.'%')
          ->orWhere('dependencia.nom_dep', 'like', '%'.$search.'%')
          ->orWhere('region.nom_reg', 'like', '%'.$search.'%')
          ->orWhere('dependencia.ubicacion', 'like', '%'.$search.'%')
          ->count('firmas.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = firmas::select('firmas.id','firmas.firma','region.nom_reg','dependencia.nom_dep','dependencia.ubicacion')
            ->leftjoin('dependencia','firmas.id_dep','=','dependencia.id')
            ->leftjoin('region','dependencia.id_reg','=','region.id')
            ->orWhere('firmas.firma', 'like', '%'.$search.'%')
            ->orWhere('dependencia.nom_dep', 'like', '%'.$search.'%')
            ->orWhere('region.nom_reg', 'like', '%'.$search.'%')
            ->orWhere('dependencia.ubicacion', 'like', '%'.$search.'%')
            ->count(DB::raw('firmas.id'));
          }

          //Carga los datos
          $objfirma
          ->orWhere('firmas.firma', 'like', '%'.$search.'%')
          ->orWhere('dependencia.nom_dep', 'like', '%'.$search.'%')
          ->orWhere('region.nom_reg', 'like', '%'.$search.'%')
          ->orWhere('dependencia.ubicacion', 'like', '%'.$search.'%');

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
            $order = 'firmas.firma';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objfirma->offset($start);
          $objfirma->limit($limit);
          $objfirma->orderBy($order, $dir);

          $objfirmas = $objfirma->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objfirmas;
          
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
        $option_actual = 0;

        try {

            $objubicacion = dependencia::select(DB::raw('(concat(dependencia.nom_dep," ",dependencia.ubicacion, " - Region: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
            ->leftjoin('region','dependencia.id_reg','=','region.id')
            ->orderBy('dependencia.nom_dep','asc')
            ->limit($this->RegxPag)->pluck('nombredependencia','id')->toArray();

            if (count($objubicacion)>0) {                                
                $objubicacion = [''=>'Escribir [Enter Busca]']+$objubicacion;                  
            } else {
                $objubicacion = [''=>'Escribir [Enter Busca]'];
            }

            return view('firmas.crear', array('objubicacion'=>$objubicacion, 'option_actual'=>$option_actual));

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
    public function store(FirmasCreateRequest $request)
    {
        try{
            $data = $request->all();

            $data['firma'] = strtoupper($data['firma']);

            firmas::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/firmas');

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
            $objfirmas = firmas::find($id);
    
            $obj = new tipificaciones();
            $objubicacion = $obj->jefes;
            $option_actual = $objfirmas['actual'];

            if (isset($objfirmas)) { 
                Session::put('dateconcurrente', $objfirmas['updated_at']);
                Session::save();

                $option_actual = $objfirmas['actual'];

                if (isset($objfirmas['id'])) {                    
                    $objubicacion = dependencia::select(DB::raw('(concat(dependencia.nom_dep," ",dependencia.ubicacion, " - Región: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
                    ->where('dependencia.id','=',$objfirmas['id_dep'])
                    ->leftjoin('region','dependencia.id_reg','=','region.id')
                    ->orderBy('dependencia.nom_dep','asc')
                    ->pluck('nombredependencia','id')->toArray();
                } else {
                    $objubicacion = [''=>'Escribir [Enter Busca]'];
                }

                return view('firmas.editar',array('objfirmas'=>$objfirmas, 'objubicacion'=>$objubicacion, 'option_actual'=>$option_actual));
            }                  

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/firmas');

        }
        
        return redirect('/firmas');        
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
            $request = new FirmasUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $objfirmas = firmas::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {

                $afectados = DB::table('firmas')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'firma'=>strtoupper($data['firma']),
                      'actual'=>$data['actual'],
                      'id_dep'=>$data['id_dep'],
                      'provi_jefe'=>$data['provi_jefe'],
                      'fecha_provi'=>DB::raw("STR_TO_DATE('".$data['fecha_provi']."','%Y-%m-%d')"),                      
                      'gaceta_provi'=>$data['gaceta_provi'],
                      'fecha_gaceta'=>DB::raw("STR_TO_DATE('".$data['fecha_gaceta']."','%Y-%m-%d')"),                    
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/firmas');
                } else {
                    $objf = firmas::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/firmas');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/firmas');
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

            return redirect('/firmas');
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

            $afectados =  firmas::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }        
            return redirect('/firmas');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/firmas');        
        }       
    }

    public function BuscarFirmas(Request $request) {
        try{

            $busqueda = $request->busqueda;
            $objfirmas = firmas::where('firma', 'like', '%'. $busqueda . '%')
            ->orderBy('firma','asc')
            ->limit($this->RegxPag)->get();

          return view('firmas.index', array('objfirmas'=>$objfirmas, 'busqueda'=>$busqueda));                           

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

    public function BuscarLaFirma(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {                  
                $objfirmas = firmas::select('firma','id')
                ->orwhereRaw('firma LIKE ?',['%'.$b.'%'])
                ->orwhereRaw('ubicacion LIKE ?',['%'.$b.'%'])
                ->orderBy('firma','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objfirmas);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }      
}