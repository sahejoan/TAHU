<?php namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;
use Session;
use Redirect;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\User;
use DB;
use Cookie;
class LogController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */

   public function __construct()
   {

   }

  public function index()
  {

  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(LoginRequest $request)
  { 

    $email = $request['email'];    
    $password = $request['password'];
    $recordar = false;
    $recordar2 = 'false';
    if($request['recordar']==1)
    { 
      $recordar = true;
      $recordar2 = 'true';       
    } 
    else 
    { 
      $recordar = false;       
      $recordar2 = 'false';
    }   

    if (!Cookie::has('recordarsesion'))
    {
      Cookie::queue(Cookie::forever('password_', ''));        
      Cookie::queue(Cookie::forever('email_', ''));                    
      
      Cookie::queue(Cookie::forever('recordarsesion', $recordar2));        
    } else {
      if (Cookie::get('recordarsesion')=='false') {
        Cookie::queue(Cookie::forever('password_', ''));        
        Cookie::queue(Cookie::forever('email_', ''));                    
      }
    }

    if (($recordar) && (Cookie::get('recordarsesion')=='true'))
    {      
      if (Auth::viaRemember()) 
      {      
        Cookie::queue(Cookie::forever('recordarsesion', $recordar2));        
        $user = User::where('email','=',$email)->first();
                      
        Session::put('userconection',$user->email.$this->generateRandomString());
        Session::save();

        $objusuario = DB::table('users')->where('id','=', Auth::user()->id)->update(['idsesion'=>Session::getId()]);

        if (Auth::user()->estatus == "ACTIVO") 
        {            
            Cookie::queue(Cookie::forever('password_', $password));        
            Cookie::queue(Cookie::forever('email_', $email));        

            Session::put('rid', 0);
            Session::put('dateconcurrencia', '');
            Session::put('id_desig', 0);
            Session::put('id_area', 0);
            Session::save();

            return Redirect::to('admin'); 
        } else {
            Session::flash('message-error', 'Usuario Bloqueado');
            Auth::logout();
            return Redirect::to('autentificacion');
        }
      } else {
        if (Auth::attempt(array('email' => $request['email'], 'password' => $request['password'], 'estatus' => 'ACTIVO'), $recordar)) 
        {
          Cookie::queue(Cookie::forever('recordarsesion', $recordar2));        
          $user = User::where('email','=',$email)->first();
                      
          Session::put('userconection',$user->email.$this->generateRandomString());        
          Session::save();

          $objusuario = DB::table('users')->where('id','=', Auth::user()->id)->update(['idsesion'=>Session::getId()]);

          if (Auth::user()->estatus == "ACTIVO") 
          {
            Cookie::queue(Cookie::forever('password_', $password));        
            Cookie::queue(Cookie::forever('email_', $email));        

            Session::put('rid', 0);
            Session::put('dateconcurrencia', '');
            Session::put('id_desig', 0);
            Session::put('id_area', 0);
            Session::save();          

            return Redirect::to('admin'); 
          } else {
            Session::flash('message-error', 'Usuario Bloqueado');
            Auth::logout();
            return Redirect::to('autentificacion');
          }
        }        
      }
    }
    else
    {
      if (Auth::attempt(array('email' => $request['email'], 'password' => $request['password'], 'estatus' => 'ACTIVO'), $recordar)) 
      {
        Cookie::queue(Cookie::forever('recordarsesion', $recordar2));        
        $user = User::where('email','=',$email)->first();
                      
        Session::put('userconection',$user->email.$this->generateRandomString());        
        Session::save();

        $objusuario = DB::table('users')->where('id','=', Auth::user()->id)->update(['idsesion'=>Session::getId()]);

        if (Auth::user()->estatus == "ACTIVO") 
        {
            Cookie::queue(Cookie::forever('password_', $password));        
            Cookie::queue(Cookie::forever('email_', $email));        

            Session::put('rid', 0);
            Session::put('dateconcurrencia', '');
            Session::put('id_desig', 0);
            Session::put('id_area', 0);
            Session::save();          

            return Redirect::to('admin'); 
        } else {
            Session::flash('message-error', 'Usuario Bloqueado');
            Auth::logout();
            return Redirect::to('autentificacion');
        }
      }
    }

    Session::flash('message-error', 'Datos No Son Validos...Acceso Negado');
    return Redirect::to('autentificacion');        
  }

  //public function logout(Request $request) 
  public function logout() 
  {        
    if (isset(Auth::user()->tipoimagen)) 
    {
        if (Cookie::get('recordarsesion')=='false') {
            Cookie::queue(Cookie::forever('password_', ''));        
            Cookie::queue(Cookie::forever('email_', ''));        
        }
     
        Session::forget('userconection');
        Session::save();

        Session::flush();
        Auth::logout();
        
        return Redirect::to('/admin');
    }else{
        return Redirect::to('/admin');
    }
  }

  public function CerrarSesion()
  {    
      if (isset(Auth::user()->tipoimagen)) 
      {        
        Session::forget('userconection');
        Session::save();
        Session::flush();
        Auth::logout();
        return view('/layouts/advertencia');
      }else{
        return Redirect::to('/admin');
      }
  }

  public function CierreConexion()
  {      
      if (isset(Auth::user()->tipoimagen)) 
      {              
          Session::forget('userconection');
          Session::save();
          Session::flush();
          Auth::logout();
          return view('/layout/advertencia3');
      } else {
        return Redirect::to('/admin');
      }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id) 
  {
    
  }

  public function generateRandomString($length = 10) 
  {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) 
      {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
  } 

  public function errorSesion()
  {
    return view('/layouts/advertencia3');
  }

}
?>