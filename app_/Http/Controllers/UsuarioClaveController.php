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
class UsuarioClaveController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

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
    public function update($id)
    {
           
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

    public function ModificarClave($p)
    {     
        $user = User::find(Auth::user()->id);
        //return view('usuario.resert-password' , array('user'=>$user));
        return view('auth.reset-password');
    }    

    public function ActualizarClave(Request $request)
    {     

        if ((strlen($request->password) < 8) || (strlen($request->password_confirmation) < 8)) {
           $user = User::find(Auth::user()->id);
           Session::flash('message-error','Solicitud inválida. Mínimo de 8 caracteres');
           return view('auth.reset-password');
        } else {

           if ($request->password==$request->password_confirmation) {
      
              try {
                 $user = User::find(Auth::user()->id);        
                 $clave = $request->password;
                 $data = ['password'=>$clave];
                 $user->fill($data);
                 $user->save();

                 Session::flash('message','Cambio de clave exitosa');
                 
                 return redirect("admin");

              } catch (Exception $e) {                    
                 Session::flash('message-error','Solicitud Inválida');               
                 return view('auth.reset-password');
              }

            } else {          
                 Session::flash('message-error','Solicitud Inválida.La clave de confirmación no coincide.');
                 return view('auth.reset-password');
            }
        }
    }     
}