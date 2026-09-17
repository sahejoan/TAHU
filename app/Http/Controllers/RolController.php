<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//agregamos
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Support\Facades\DB;


class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $RegxPag = 1000;

    function __construct()
    {
         //$this->middleware('permission:ver-rol|crear-rol|editar-rol|borrar-rol', ['only' => ['index']]);
         //$this->middleware('permission:crear-rol', ['only' => ['create','store']]);
         //$this->middleware('permission:editar-rol', ['only' => ['edit','update']]);
         //$this->middleware('permission:borrar-rol', ['only' => ['destroy']]);
        $this->middleware('auth');
        $this->middleware('role_or_permission:11_Permisos_Usuarios', ['only' => ['index','create','store','update','edit','destroy']]);

    }

    public function index(Request $request)
    {        
        try{

          $objroles = Role::limit($this->RegxPag)->get();
          return view('roles.index', array('objroles'=>$objroles, 'busqueda'=>''));

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        return view('roles.crear',compact('permission'));
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
            $data = $request->all();

            $this->validate($request, [
                'name' => 'required|unique:roles,name|max:50',
                'permission' => 'required',
            ]);
    
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));
    
            return redirect()->route('roles.index');

        } catch (Exception $e) {                    
             if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro relacionado a otros datos.');
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'El Registro ya existe.');
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
            $role = Role::find($id);
            $permission = Permission::orderBy('name')->get();
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
            return view('roles.editar',compact('role','permission','rolePermissions'));

        } catch (Exception $e) {

            Session::flash('message-error','Solicitud inválida');               
            return redirect('/roles');

        }
        
        return redirect('/roles');        
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
                'permission' => 'required',
            ]);
    
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();
    
            $role->syncPermissions($request->input('permission'));
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();    
            
            return redirect()->route('roles.index');

        } catch (Exception $e) {    
            if (isset($e->errorInfo)) {
                if ($e->errorInfo[1]=='1062') Session::flash('message-error', 'Intenta escribir un valor que está registrado.');
                if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
                Session::flash('message-error','Ocurrio un problema, no se pudo actualizar el registro');
            }

            return redirect('/roles');
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

            DB::table("roles")->where('id',$id)->delete();
            return redirect()->route('roles.index');                        

        } catch (Exception $e) {    
                
            if (isset($e->errorInfo)) {
              if ($e->errorInfo[1]=='1451') Session::flash('message-error', 'Imposible eliminar un registro asociado a otros datos.');
              if ($e->errorInfo[1]!='1451' && $e->errorInfo[1]!='1062') Session::flash('message-error', 'Error['.$e->errorInfo[1].'] Operación inválida.');
            } else {
              Session::flash('message-error','Ocurrio un problema, no se pudo eliminar el registro');
            }

            return redirect('/roles');        
        }        
    }

    public function BuscarRoles(Request $request) {

        try{
          $busqueda = $request->busqueda;
          
          $objroles = Role::where('name', 'like', '%'. $busqueda . '%')
            ->orderBy('name','asc')
            ->limit($this->RegxPag)->get();

          return view('roles.index', array('objroles'=>$objroles, 'busqueda'=>$busqueda));                           

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