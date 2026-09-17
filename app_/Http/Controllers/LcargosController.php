<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\lcargos;
use App\Http\Controllers\Controller;

use App\Http\Requests\LcargosCreateRequest;
use App\Http\Requests\LcargosUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class LcargosController extends Controller
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

        $this->middleware('role_or_permission:6_Buscar_cargo|6_Ver_cargo|6_Crear_cago|6_Editar_cargo|6_Borrar_cargo', ['only' => ['index']]);
        $this->middleware('role_or_permission:6_Cargo', ['only' => ['index']]);
        $this->middleware('role_or_permission:6_Buscar_cargo', ['only' => ['BuscarLcargos','getLcargos']]);
        $this->middleware('role_or_permission:6_Crear_cargo', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:6_Editar_cargo', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:6_Borrar_cargo', ['only' => ['destroy']]);                        

    }  

    public function index()
    {     
        try{

          return view('lcargos.index', array('busqueda'=>''));

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

    public function getLcargos(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('6_Editar_cargo')) {
            $editar1 = "lcargo.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('6_Borrar_cargo')) {
            $borrar1 = "lcargo.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'descripcion',
            1 => 'editar',
            2 => 'borrar'            
          );

          $objlcargo =lcargos::query();

          //para jsindexversion1          
          $objlcargo->select('lcargo.id','lcargo.descripcion',
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));                

          //Cuenta registros
          $totalregistros = lcargos::select('lcargo.id','lcargo.descripcion')
          ->where('lcargo.descripcion', 'like', '%'.$search.'%')
          ->count('lcargo.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = lcargos::select('lcargo.id','lcargo.descripcion')
            ->where('lcargo.descripcion', 'like', '%'.$search.'%')
            ->count(DB::raw('lcargo.id'));
          }

          //Carga los datos
          $objlcargo
          ->where('lcargo.descripcion', 'like', '%'.$search.'%');          

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
            $order = 'lcargo.descripcion';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objlcargo->offset($start);
          $objlcargo->limit($limit);
          $objlcargo->orderBy($order, $dir);

          $objlcargos = $objlcargo->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objlcargos;
          
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
        return view('lcargos.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LcargosCreateRequest $request)
    {
        try{
            $data = $request->all();

            lcargos::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/lcargos');

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
            $objlcargos = lcargos::find($id);
    
            Session::put('dateconcurrente', $objlcargos['updated_at']);
            Session::save();

            return view('lcargos.editar',compact('objlcargos'));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/lcargos');

        }
        
        return redirect('/lcargos');        
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
            $request = new LcargosUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $objlcargos = lcargos::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {

                $afectados = DB::table('lcargo')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'descripcion'=>$data['descripcion'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/lcargos');
                } else {
                    $objf = lcargos::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/lcargos');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/lcargos');
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

            return redirect('/lcargos');
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

            $afectados =  lcargos::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }        
            return redirect('/lcargos');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/lcargos');        
        }       
    }

    public function BuscarLcargos(Request $request) {
        try{

          $busqueda = $request->busqueda;
          $objlcargos = lcargos::where('descripcion', 'like', '%'. $busqueda . '%')
            ->orderBy('descripcion','asc')
            ->limit($this->RegxPag)->get();

          return view('lcargos.index', array('objlcargos'=>$objlcargos, 'busqueda'=>$busqueda));                           

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

    public function BuscarElCargo(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {                  
                $objlcargo = lcargos::select('descripcion','id')
                ->whereRaw('descripcion LIKE ?',['%'.$b.'%'])
                ->orderBy('descripcion','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objlcargo);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
            return response()->json("Error");
        }
    }            
}