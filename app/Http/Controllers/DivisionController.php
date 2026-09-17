<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\division;
use App\Models\dependencia;
use App\Models\jefedependencia;
use App\Http\Controllers\Controller;

use App\Http\Requests\DivisionCreateRequest;
use App\Http\Requests\DivisionUpdateRequest;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;

class DivisionController extends Controller
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

        $this->middleware('role_or_permission:2_Buscar_division|2_Ver_division|2_Crear_division|2_Editar_division|2_Borrar_division', ['only' => ['index']]);
        $this->middleware('role_or_permission:2_Division', ['only' => ['index']]);
        $this->middleware('role_or_permission:2_Buscar_division', ['only' => ['BuscarDivision','getDivision']]);
        $this->middleware('role_or_permission:2_Crear_division', ['only' => ['create','store']]);
        $this->middleware('role_or_permission:2_Editar_division', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:2_Borrar_division', ['only' => ['destroy']]);                
    }  

    public function index()
    {   
        try{
          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
            ->where('dependencia.id_reg','=',Auth::user()->Dependencia->Region->id)
            ->orderBy('dependencia.nom_dep','asc')
            ->pluck('nombredependencia','id')->toArray();
          } else {
            $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion)) as nombredependencia'),'dependencia.id')
            ->orderBy('dependencia.nom_dep','asc')
            ->pluck('nombredependencia','id')->toArray();            

            if (count($objdependencia)>0) {                                
              $objdependencia = ['0'=>'Todos']+$objdependencia;                  
            } else {
              $objdependencia = ['0'=>'Todos'];
            }
          }
          return view('division.index', array('objdependencia'=>$objdependencia, 'id_temp'=>'','busqueda'=>''));

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

    public function getDivision(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');
          $id_dep = $request->id_dep;

          if (Auth::user()->hasAnyPermission('Solo_dependencia')) {
            if ($id_dep != 0) {
              $id_dep = Auth::user()->Dependencia->Region->id;
            }            
          }

          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('2_Editar_division')) {
            $editar1 = "division.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('2_Borrar_division')) {
            $borrar1 = "division.id";
           } else {
            $borrar1 = "0";
           }

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

          // datatable column index  => database column name
          $columns = array(          
            0 => 'descripcion',
            1 => 'nom_jefe',
            2 => 'actual',            
            3 => 'editar',
            4 => 'borrar'            
          );

          $objdivision =division::query();

          //para jsindexversion1          
          $objdivision->select('division.id','division.descripcion',
          DB::raw('concat(dependencia.nom_dep," ",dependencia.ubicacion) as ubicacion'),
          'division.id_jefedep',DB::raw('if(division.actual = 1, "Jéfe", if(division.actual = 0, "Coordinador", if(division.actual = 2, "Encargado", "Enlace"))) as actual'),'jefedependencia.nom_jefe',
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'))                
          ->leftjoin('jefedependencia','division.id_jefedep','=','jefedependencia.id')
          ->leftjoin('dependencia','division.ubicacion','=','dependencia.id');


          //Cuenta registros
          $totalregistros = division::select('division.id','division.descripcion','jefedependencia.nom_jefe')
          ->leftjoin('jefedependencia','division.id_jefedep','=','jefedependencia.id')
          ->leftjoin('dependencia','division.ubicacion','=','dependencia.id')
          ->where('division.ubicacion',$operador,$valor)
          ->where(function($query) use ($search){
            $query->orWhere('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
            ->orWhere('division.descripcion', 'like', '%'.$search.'%');
          })
          ->count('division.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = division::select('division.id','division.descripcion','division.id_jefedep',DB::raw('if(division.actual = 1, "Jefe", "Coordinador") as actual'),'jefedependencia.nom_jefe')
            ->leftjoin('jefedependencia','division.id_jefedep','=','jefedependencia.id')
            ->leftjoin('dependencia','division.ubicacion','=','dependencia.id')
            ->where('division.ubicacion',$operador,$valor)
            ->where(function($query) use ($search){
              $query->orWhere('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
              ->orWhere('division.descripcion', 'like', '%'.$search.'%');
            })
            ->count(DB::raw('division.id'));
          }

          //Carga los datos
          $objdivision
          ->where('division.ubicacion',$operador,$valor)
          ->where(function($query) use ($search){
            $query->orWhere('jefedependencia.nom_jefe', 'like', '%'.$search.'%')
            ->orWhere('division.descripcion', 'like', '%'.$search.'%');
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
            $order = 'division.descripcion';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $objdivision->offset($start);
          $objdivision->limit($limit);
          $objdivision->orderBy($order, $dir);

          $objdivisiones = $objdivision->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objdivisiones;

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
        $option_actual = 1;

        try{
            $objjefedependencia = jefedependencia::select('nom_jefe','id')->orderBy('nom_jefe','asc')->limit($this->RegxPag)->pluck('nom_jefe','id')->toArray();
            $objubicacion = dependencia::select(DB::raw('concat(nom_dep," ",ubicacion) as nom_dep'),'id')->orderBy('nom_dep','asc')->limit($this->RegxPag)->pluck('nom_dep','id')->toArray();

            if (count($objjefedependencia)>0) {                                
                $objjefedependencia = [''=>'Escribir [Enter Busca]']+$objjefedependencia;                  
            } else {
                $objjefedependencia = [''=>'Escribir [Enter Busca]'];
            }

            if (count($objubicacion)>0) {                                
                $objubicacion = [''=>'Seleccionar']+$objubicacion;                  
            } else {
                $objubicacion = [''=>'Seleccionar'];
            }

            return view('division.crear', array('objjefedependencia'=>$objjefedependencia, 'option_actual'=>$option_actual,'objubicacion'=>$objubicacion));

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
    public function store(DivisionCreateRequest $request)
    {
        try{

            $data = $request->all();

            $data['descripcion'] = strtoupper($data['descripcion']);
            $data['ubicacion'] = strtoupper($data['ubicacion']);

            division::create($data);
    
            Session::flash('message','Registro se creo con éxito');
            return redirect('/division');

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
            $objdivision = division::find($id);
            $option_actual = $objdivision['actual'];

            if (isset($objdivision['id'])) {
                if ($objdivision['id']>0) {                    
                    $objjefedependencia = jefedependencia::select('nom_jefe','id')->where('id','=',$objdivision['id_jefedep'])->orderBy('nom_jefe','asc')->limit($this->RegxPag)->pluck('nom_jefe','id')->toArray();
                    $objjefedependencia = [''=>'Escribir [Enter Busca2]']+$objjefedependencia;                                        
                }
            } else {
                $objjefedependencia = [''=>'Escribir [Enter Busca]'];
            }

            $objubicacion = dependencia::select(DB::raw('concat(nom_dep," ",ubicacion) as nom_dep'),'id')->orderBy('nom_dep','asc')->limit($this->RegxPag)->pluck('nom_dep','id')->toArray();

            if (count($objubicacion)>0) {                                
                $objubicacion = [''=>'Seleccionar']+$objubicacion;                  
            } else {
                $objubicacion = [''=>'Seleccionar'];
            }

            if (isset($objdivision)) {
                Session::put('dateconcurrente', $objdivision['updated_at']);
                Session::save();

                return view('division.editar',array('objdivision'=>$objdivision, 'objjefedependencia'=>$objjefedependencia, 'option_actual'=>$option_actual,'objubicacion'=>$objubicacion));
            } else {
                Session::flash('message-error','Solicitud invalida');               
                return redirect('/division');
            }

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud invalida');               
            return redirect('/division');

        }
        
        return redirect('/division');        
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
            $request = new DivisionUpdateRequest();       //Instancia de la clase que valida        

            $data = $input->all();                            //Datos recuperados desde el formulario con request  

            $objdivision = division::find($id);       //Se busca el registro

            $validation = Validator::make($data, $request->rules(), $request->messages());  //se valida los datos y se recupera el mensaje de la validacion

            //Si ocurre alguna error en los datos validados redirecciona y edita nuevamente sino guarda
            if ($validation->fails()) {

              return redirect()->back()->withInput($data)->withErrors($validation->messages());

            } else {
                $data['descripcion'] = strtoupper($data['descripcion']);
                $data['ubicacion'] = strtoupper($data['ubicacion']);

                $afectados = DB::table('division')->where('id','=',$id)->where('updated_at','=',Session::get('dateconcurrente'))
                ->update(
                [
                      'descripcion'=>$data['descripcion'],
                      'ubicacion'=>$data['ubicacion'],
                      'id_jefedep'=>$data['id_jefedep'],
                      'actual'=>$data['actual'],
                      'updated_at'=>DB::raw('NOW()')
                    ]
                );

                if ($afectados == 1) {
                    Session::flash('message','Actualización exitosa');
                    return redirect('/division');
                } else {
                    $objf = division::find($id);
                    if ($objf['updated_at'] == Session::get('dateconcurrente')) { 
                        Session::flash('message','Actualización exitosa');
                        return redirect('/division');
                    } else {
                        Session::flash('message','Los datos no se actualizaron, porque otro usuario modificó previamente dicho registro, Vuelva a realizar la operación para ver los datos nuevos');
                        return redirect('/division');
                    }
                }

            }

        } catch (Exception $e) {                
            dd($e);
            if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'Intenta escribir un valor que está registrado.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo actualizar el registro');
            }

            return redirect('/division');
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

            $afectados =  division::destroy($id);                   
            if ($afectados > 0) 
            { 
                Session::flash('message-error','Registro eliminado exitosamente'); 
            }        
            return redirect('/division');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/division');        
        }       
    }

    public function BuscarDivision(Request $request) {
        try{

          $busqueda = $request->busqueda;
          $objdivision = division::where('descripcion', 'like', '%'. $busqueda . '%')
            ->orderBy('descripcion','asc')
            ->limit($this->RegxPag)->get();

          return view('division.index', array('objdivision'=>$objdivision, 'busqueda'=>$busqueda));                           

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

    public function BuscarLaDivision(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;
            $id_dep = $request->id_dep;

            if ($b=='*') { $b = ''; }

            try {                  
                //->where('actual','=','1') codicion en modulo de area no mistraba todos los casos
                $objdivision = division::select(DB::raw('concat(division.descripcion," - ",dependencia.nom_dep," ",dependencia.ubicacion,IF(division.actual=0, " (Coordinador)", IF(division.actual=1, " (Jefatura)",""))) as descripcion'),'division.id')
                ->leftjoin('dependencia','division.ubicacion','=','dependencia.id')
                ->where('actual','<','2')
                ->where('division.ubicacion','=',$id_dep)
                ->where(function ($query) use ($b) {
                  $query->orWhere('division.descripcion', 'like', '%'.$b.'%')
                  ->orWhere('dependencia.ubicacion', 'like', '%'.$b.'%');
                  }
                )
                ->orderBy('descripcion','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objdivision);                
            } catch (Exception $e) {    
                return response()->json('Error');              
            }
        } else {
          return response()->json("Error");
        }
    }          

    public function BuscarLaDivisionArea(Request $request){
        if ($request->ajax()) {
            $b  = $request->b;

            if ($b=='*') { $b = ''; }

            try {                  
                //->where('actual','=','1') codicion en modulo de area no mistraba todos los casos
                $objdivision = division::select(DB::raw('concat(division.descripcion," - ",dependencia.nom_dep," ",dependencia.ubicacion,IF(division.actual=0, " (Coordinador)", IF(division.actual=1, " (Jefatura)",""))) as descripcion'),'division.id')
                ->leftjoin('dependencia','division.ubicacion','=','dependencia.id')
                ->where('division.actual','<','2')
                ->where(function ($query) use ($b) {
                  $query->orWhere('division.descripcion', 'like', '%'.$b.'%')
                  ->orWhere('dependencia.ubicacion', 'like', '%'.$b.'%');
                  }
                )
                ->orderBy('division.descripcion','asc')
                ->limit($this->RegxPag)->get()->toArray();

                return response()->json($objdivision);                
            } catch (Exception $e) {    
                return response()->json('Error'.$e->getMessage());              
            }
        } else {
          return response()->json("Error");
        }
    }     
}