<?php namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Redirect;
use Session;
use Cookie;
class FromController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */	

	public function __construct()
	{        
		$this->middleware('auth', array('only' => 'admin'));
	}    

   	public function index()
	{		  	    
        return view('index');     
	}

    public function admin()
	{		
        if (!Cookie::has('recordarsesion'))
        {
          Cookie::queue(Cookie::forever('password_', ''));        
          Cookie::queue(Cookie::forever('email_', ''));        
          Cookie::queue(Cookie::forever('recordarsesion', 'false'));
        } else {
          if (Cookie::get('recordarsesion')=='false') {
            Cookie::queue(Cookie::forever('password_', ''));        
            Cookie::queue(Cookie::forever('email_', ''));                    
          }
        }

		if (Auth::check())
		{
			return view('/layouts/app'); 
    } else {        	
			return view('/auth/login');		   
		}			
	}

   	public function autentificacion()
	{       	
        if (!Cookie::has('recordarsesion'))
        {
          Cookie::queue(Cookie::forever('recordarsesion', 'false'));
          Cookie::queue(Cookie::forever('password_', ''));        
          Cookie::queue(Cookie::forever('email_', ''));        
        } else {
          if (Cookie::get('recordarsesion')=='false') {
            Cookie::queue(Cookie::forever('password_', ''));        
            Cookie::queue(Cookie::forever('email_', ''));                    
          }
        }

        if (Auth::check()) 
        {
            if (Auth::check()) { } 
            else {        	
               Auth::logout();
               Session::flash('message-error', 'Su sesión a caducado');
               return view('/auth/login');        	
            }
            return Redirect::to('admin');            	               
        } else {           
		   return view('/auth/login');		   
		}	    
	}		
}
?>
