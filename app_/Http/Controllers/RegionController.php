<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\region;
use App\Http\Controllers\Controller;

use App\Http\Requests\RegionCreateRequest;
use App\Http\Requests\RegionUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class RegionController extends Controller
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

        $this->middleware('role_or_permission:1_Buscar_region|1_Ver_region|1_Crear_region|1_Editar_region|1_Borrar_region', ['only' => ['index']]);
        $this->middleware('role_or_permission:1_Region', ['only' => ['index']]);
        $this->middleware('role_or_permission:1_Ver_region', ['only' => ['VerRegion']]);
        $this->middleware('role_or_permission:1_Buscar_region', ['only' => ['BuscarRegion','getRegion']]);
        $this->middleware('role_or_permission:1_Crear_region', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:1_Editar_region', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:1_Borrar_region', ['only' => ['destroy']]);        

    }  

    public function index()
    {     
        try{

          return view('region.index', array('busqueda'=>''));

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

    public function getRegion(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('1_Ver_region')) {
            $ver1 = "region.id";
          } else {
            $ver1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('1_Editar_region')) {
            $editar1 = "region.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('1_Borrar_region')) {
            $borrar1 = "region.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'nom_reg',
            1 => 'direccion',
            2 => 'telf_reg',            
            3 => 'ver',
            4 => 'editar',
            5 => 'borrar'            
          );

          $objregion =region::query();

          //para jsindexversion1          
          $objregion->select('region.id','region.nom_reg','region.direccion','region.telf_reg',
          DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'));

          //Cuenta registros
          $totalregistros = region::select('region.id','region.nom_reg')
          ->where('region.nom_reg', 'like', '%'.$search.'%')
          ->count('region.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = region::select('region.id','region.nom_reg')
            ->where('region.nom_reg', 'like', '%'.$search.'%')
            ->count(DB::raw('region.id'));
          }

          //Carga los datos
          $objregion->where('region.nom_reg', 'like', '%'.$search.'%');
         
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
            $order = 'region.nom_reg';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objregion->offset($start);
          $objregion->limit($limit);
          $objregion->orderBy($order, $dir);

          $objregiones = $objregion->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objregiones;
          
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
        return view('region.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegionCreateRequest $request)
    {
        try{
            $data = $request->all();

            region::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/region');

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
            $objregion = region::find($id);
    
            Session::put('dateconcurrente', $objregion['updated_at']);
            Session::save();

            return view('region.editar',compact('objregion'));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/region');

        }
        
        return redirect('/region');        
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
            $request = new RegionUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $objregion = region::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {

                $afectados = DB::table('region')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'nom_reg'=>$data['nom_reg'],
                      'direccion'=>$data['direccion'],
                      'telf_reg'=>$data['telf_reg'],
                      'coordenadas'=>$data['coordenadas'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/region');
                } else {
                    $objf = region::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/region');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/region');
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

            return redirect('/region');
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

            $afectados =  region::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }        
            return redirect('/region');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/region');        
        }       
    }

    public function VerRegion(Request $request) {
        if ($request->ajax()) {
          $id = $request->id;          

          try {                  
            $result = region::buscarregion($id);
            return response()->json($result);              
          } catch (Exception $e) {    
            return response()->json("Error");
          }
        } else {
          return response()->json("Error");              
        } 
    }

    public function BuscarRegion(Request $request) {
        try{

          $busqueda = $request->busqueda;
          $objregion = region::where('nom_reg', 'like', '%'. $busqueda . '%')
            ->orderBy('nom_reg','asc')
            ->limit($this->RegxPag)->get();

          return view('region.index', array('objregion'=>$objregion, 'busqueda'=>$busqueda));                           

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
}