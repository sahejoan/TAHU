<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\area;
use App\Models\division;
use App\Http\Controllers\Controller;

use App\Http\Requests\AreaCreateRequest;
use App\Http\Requests\AreaUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class AreaController extends Controller
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

        $this->middleware('role_or_permission:3_Buscar_area|3_Ver_area|3_Crear_area|3_Editar_area|3_Borrar_area', ['only' => ['index']]);
        $this->middleware('role_or_permission:3_Area', ['only' => ['index']]);
        $this->middleware('role_or_permission:3_Ver_area', ['only' => ['VerArea']]);
        $this->middleware('role_or_permission:3_Buscar_area', ['only' => ['BuscarArea','getArea']]);
        $this->middleware('role_or_permission:3_Crear_area', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:3_Editar_area', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:3_Borrar_area', ['only' => ['destroy']]);                        

        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion', ['only' => 'BuscarElAreaDivision']);                
        $this->middleware('role_or_permission:9_Crear_designacion|9_Editar_designacion', ['only' => 'BuscarElArea']);                

    }  

    public function index()
    {     
        try{

          return view('area.index', array('busqueda'=>''));

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

    public function getArea(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('3_Ver_area')) {
            $ver1 = "area.id";
          } else {
            $ver1 = "0";
          }                                    

          if (Auth::user()->hasAnyPermission('3_Editar_area')) {
            $editar1 = "area.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('3_Borrar_area')) {
            $borrar1 = "area.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'nom_area',
            1 => 'ubicacion',
            2 => 'telf_area',            
            3 => 'descripcion',            
            4 => 'ver',            
            5 => 'editar',
            6 => 'borrar'            
          );

          $objarea =area::query();

          //para jsindexversion1          
          $objarea->select('area.id','area.nom_area','area.ubicacion','area.telf_area','division.descripcion',
          DB::raw($ver1.' as ver'), DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'))                
          ->leftjoin('division','area.id_divi','=','division.id');

          //Cuenta registros
          $totalregistros = area::select('area.id','area.nom_area','area.ubicacion','area.telf_area','division.descripcion')
          ->leftjoin('division','area.id_divi','=','division.id')
          ->orWhere('area.nom_area', 'like', '%'.$search.'%')
          ->count('area.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = area::select('area.id','area.nom_area','area.ubicacion','area.telf_area','division.descripcion')
            ->leftjoin('division','area.id_divi','=','division.id')
            ->orWhere('area.nom_area', 'like', '%'.$search.'%')
            ->count(DB::raw('area.id'));
          }

          //Carga los datos
          $objarea
          ->orWhere('area.nom_area', 'like', '%'.$search.'%');

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
            $order = 'area.nom_area';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objarea->offset($start);
          $objarea->limit($limit);
          $objarea->orderBy($order, $dir);

          $objareas = $objarea->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objareas;
          
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

        try{
            $objdivision = division::select(DB::raw('concat(descripcion," - ",ubicacion) as descripcion'),'id')->orderBy('descripcion','asc')->limit($this->RegxPag)->pluck('descripcion','id')->toArray();

            if (count($objdivision)>0) {                                
                $objdivision = [''=>'Escribir [Enter Busca]']+$objdivision;                  
            } else {
                $objdivision = [''=>'Escribir [Enter Busca]'];
            }

            return view('area.crear', array('objdivision'=>$objdivision));

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
    public function store(AreaCreateRequest $request)
    {
        try{
            $data = $request->all();

            area::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/area');

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
            $objarea = area::find($id);

            if (isset($objarea['id'])) {
                if ($objarea['id']>0) {                    
                    $objdivision = division::select(DB::raw('concat(descripcion," - ",ubicacion) as descripcion'),'id')->where('id','=',$objarea['id_divi'])->orderBy('descripcion','asc')->limit($this->RegxPag)->pluck('descripcion','id')->toArray();
                    $objdivision = [''=>'Escribir [Enter Busca2]']+$objdivision;                                        
                }                
            } else {
                $objdivision = [''=>'Escribir [Enter Busca]'];
            }


            if (isset($objarea)) {
                Session::put('dateconcurrente', $objarea['updated_at']);
                Session::save();

                return view('area.editar',array('objarea'=>$objarea,'objdivision'=>$objdivision));
            } else {
                Session::flash('message-error','Solicitud invalida');               
                return redirect('/area');
            }                  

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/area');

        }
        
        return redirect('/area');        
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
            $request = new AreaUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                    //Datos recuperados desde el formulario con request  

            $objarea = area::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {

                $afectados = DB::table('area')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'nom_area'=>$data['nom_area'],
                      'ubicacion'=>$data['ubicacion'],
                      'telf_area'=>$data['telf_area'],
                      'coord_area'=>$data['coord_area'],
                      'id_divi'=>$data['id_divi'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/area');
                } else {
                    $objf = area::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/area');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/area');
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

            return redirect('/area');
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

            $afectados =  area::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message','Registro eliminado exitosamente'); 
            }        
            return redirect('/area');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/area');        
        }       
    }

    public function VerArea(Request $request) {
        if ($request->ajax()) {
          $id = $request->id;          

          try {                  
            $result = area::select('area.id','area.nom_area','area.ubicacion','area.telf_area','area.coord_area','division.descripcion')
            ->join('division','area.id_divi','=','division.id')
            ->where('area.id','=',$id)->get()->first();
            return response()->json($result);              
          } catch (Exception $e) {    
            return response()->json("Error");
          }
        } else {
          return response()->json("Error");              
        } 
    }

    public function BuscarArea(Request $request) {
        try{

            $busqueda = $request->busqueda;
            $objarea = area::where('nom_area', 'like', '%'. $busqueda . '%')
            ->orderBy('nom_area','asc')
            ->limit($this->RegxPag)->get();

          return view('area.index', array('objarea'=>$objarea, 'busqueda'=>$busqueda));                           

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

    public function BuscarElArea(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            if ($b=='*') { $b = ''; }

            try {                  
                $objarea = area::select('nom_area','id')
                ->whereRaw('nom_area LIKE ?',['%'.$b.'%'])
                ->orderBy('nom_area','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objarea);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }      

    public function BuscarElAreaDivision(Request $request){
        if ($request->ajax()) {
            $id_divi  = $request->id_div;

            try {                  
                $objarea = area::select('nom_area','id')
                ->where('id_divi','=',$id_divi)
                ->orderBy('nom_area','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objarea);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }    
}