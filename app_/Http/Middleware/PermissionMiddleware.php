<?php namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Session;                         
use Closure;
use App\Models\User;
use Auth;
class PermissionMiddleware {

    protected $auth;           

    public function __construct(Guard $auth) 
    {
         $this->auth = $auth;
    }

	public function handle($request, Closure $next, $permission)
	{	
		if (Auth::guest()) {
        	return Redirect()->to('cerrarsesion');
    	}

    	if (! $request->user()->can($permission)) {
       		Session::flash('message-error', 'Sin privilegios para accesar');
			return Redirect()->to('admin');
    	}

		return $next($request);		
	}

}