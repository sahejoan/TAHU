<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//agregamos lo siguiente
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\dependencia;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Session;
use Auth;

class UsuarioController extends Controller
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
        $this->middleware('role_or_permission:11_Permisos_Usuarios', ['only' => ['index','create','store','update','edit','destroy']]);
    }  
    
    public function index(Request $request)
    {     
        try{

        //Sin paginación
        /* $usuarios = User::all();
        return view('usuarios.index',compact('usuarios')); */

        //Con paginación
        //$usuarios = User::paginate(5);

        return view('usuarios.index', array('busqueda'=>''));

        //return view('usuarios.index',compact('usuarios'));

        //al usar esta paginacion, recordar poner en el el index.blade.php este codigo  {!! $usuarios->links() !!}

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

    public function getUsuarios(Request $request)
    {
        if ($request->ajax()) {
        try {
          $search = $request->input('search.value');

          if (Auth::user()->hasAnyPermission('11_Permisos_Usuarios')) {
            $editar1 = "users.id";
          } else {
            $editar1 = "0";
          }                                    
          //para jsindexversion1
          if (Auth::user()->hasAnyPermission('11_Permisos_Usuarios')) {
            $borrar1 = "users.id";
           } else {
            $borrar1 = "0";
           }


          // datatable column index  => database column name
          $columns = array(          
            0 => 'name',
            1 => 'email',
            2 => 'rolName',
            3 => 'editar',
            4 => 'borrar'            
          );

          $obj =User::query();

          //para jsindexversion1          
          $obj->select('users.id','users.name','users.email',DB::raw('roles.name as rolName'),
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'))                
          ->leftjoin('model_has_roles','users.id','=','model_has_roles.model_id')
          ->leftjoin('roles','model_has_roles.role_id','=','roles.id');


          //Cuenta registros
          $totalregistros = User::select('users.id','users.name','users.email',DB::raw('roles.name as rolName'),
          DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'))                
          ->leftjoin('model_has_roles','users.id','=','model_has_roles.model_id')
          ->leftjoin('roles','model_has_roles.role_id','=','roles.id')
          ->orWhere('users.name', 'like', '%'.$search.'%')
          ->orWhere('users.email', 'like', '%'.$search.'%')
          ->count('users.id');
 
          $totalData = $totalregistros;

          //cuenta filrado
          if(empty($request->input('search.value'))) {

            $totalFiltered = $totalData;
          } else {
            $search = $request->input('search.value');

            //filtering
            $totalFiltered = User::select('users.id','users.name','users.email',DB::raw('roles.name as rolName'),
            DB::raw($editar1.' as editar'), DB::raw($borrar1.' as borrar'))                
            ->leftjoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftjoin('roles','model_has_roles.role_id','=','roles.id')
            ->orWhere('users.name', 'like', '%'.$search.'%')
            ->orWhere('users.email', 'like', '%'.$search.'%')
            ->count(DB::raw('users.id'));
          }

          //Carga los datos
          $obj
          ->orWhere('users.name', 'like', '%'.$search.'%')
          ->orWhere('users.email', 'like', '%'.$search.'%');

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
            $order = 'users.name';
          }else{
            $order = $columns[$request->input('order.0.column')];
          }

          if (empty($request->input('order.0.dir'))) {
            $dir = 'asc';
          }else{
            $dir = $request->input('order.0.dir');
          }  

          $obj->offset($start);
          $obj->limit($limit);
          $obj->orderBy($order, $dir);

          $objs = $obj->get()->toArray();

          $data = array();         
          
          //para jsindexversion1
          $data = $objs;
          
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
        try {
           $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion, " - Region: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
            ->leftjoin('region','dependencia.id_reg','=','region.id')
            ->orderBy('dependencia.nom_dep','asc')
            ->limit($this->RegxPag)->pluck('nombredependencia','id')->toArray();

            if (count($objdependencia)>0) {                                
                $objdependencia = [''=>'Escribir [Busca]']+$objdependencia;                  
            } else {
                $objdependencia = [''=>'Escribir [Busca]'];
            }

            //aqui trabajamos con name de las tablas de users
            $roles = Role::pluck('name','name')->all();
            return view('usuarios.crear',compact('roles','objdependencia'));

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
    public function store(Request $request)
    {
        try{
            $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'id_dep' => 'required|min:1|max:4294967295|numeric',            
            'cedula'    => 'required|regex:/^[VvEe]-[0-9]{5,8}$/|max:10',            
            ]);

            $input = $request->all();

            $input['cedula']=trim(strtoupper($input['cedula']));
            
            $user = User::create($input);
            $user->assignRole($request->input('roles'));
    
            Session::flash('message','Registro se creo con éxito');
            return redirect()->route('usuarios.index');

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Relacionado a Otros Datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro Ya Existe.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
             } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo crear el registro');
             }

             return redirect()->back()->withInput($input);                            
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
            $user = User::find($id);
            $roles = Role::pluck('name','name')->all();
            $userRole = $user->roles->pluck('name','name')->all();
    
            if (isset($user['id'])) {
                $objdependencia = dependencia::select(DB::raw('(concat(dependencia.nom_dep, " - ", dependencia.ubicacion, " - Región: ", region.nom_reg)) as nombredependencia'),'dependencia.id')
                ->where('dependencia.id','=',$user['id_dep'])
                ->leftjoin('region','dependencia.id_reg','=','region.id')
                ->orderBy('dependencia.nom_dep','asc')
                ->pluck('nombredependencia','id')->toArray();
            } else {
                $objdependencia = [''=>'Escribir [Enter Busca]'];
            }    
            return view('usuarios.editar',compact('user','roles','userRole','objdependencia'));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud Invalida');               
            return redirect('/usuarios');

        }
        
        return redirect('/usuarios');        
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
        try {

            $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
            'id_dep' => 'required|min:1|max:4294967295|numeric',
            'cedula'    => 'required|regex:/^[VvEe]-[0-9]{5,8}$/|max:10',
            ]);
            
            $input = $request->all();

            $input['password']=TRIM($input['password']);

            $input['cedula']=trim(strtoupper($input['cedula']));

            if(!empty($input['password'])){                 
                //$input['password'] = Hash::make($input['password'], ['rounds' => 10]);

                //$input['password'] = Hash::make($input['password']);
                //$input['password'] = bcrypt($input['password']);                
            }else{
                $input = Arr::except($input,array('password'));    
            }
    
            $user = User::find($id);

            $user->fill($input);
            $user->save();

            //$user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
    
            $user->assignRole($request->input('roles'));
    
            Session::flash('message','Registro se actualizó con éxito');
            return redirect()->route('usuarios.index');            

        } catch (Exception $e) {    
            if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'Intenta escrubir un valor que está registro.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {                
                Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Actualizar el Registro');
            }

            return redirect('/usuarios');
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

            User::find($id)->delete();
            return redirect()->route('usuarios.index');

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible Eliminar Un Registro Asociado a Otros Datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio Un Problema, No Se Pudo Eliminar el Registro');
            }

            return redirect('/usuarios');        
        }        
    }
}