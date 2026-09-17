<?php namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Session;                         
use Closure;
use App\Models\User;

class Admin {

    protected $auth;           

    public function __construct(Guard $auth) 
    {
         $this->auth = $auth;
    }

	public function handle($request, Closure $next)
	{	
		
    if ($this->auth->user()->rol == 'root')
    {		
			   //Para eliminar sesion si abrio en otro equipo	        
        	$objunicasesion = User::select('idsesion')->where('id','=',$this->auth->user()->id)->first()->toArray();

        	if (count($objunicasesion)>0){
           		if ($objunicasesion['idsesion']==Session::getId())
           		{
           		}
           		else
           		{
 			  		    return Redirect()->to('cerrarsesion');
           		}
        	}                
		}
		else
		{
			Session::flash('message-error', 'Sin privilegios para accesar');
			return Redirect()->to('admin');
		}


		return $next($request);
		
	}

}