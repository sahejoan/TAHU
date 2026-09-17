<?php

namespace App\Http\Middleware;

//use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Session;

//extends Middleware
class Authenticate 
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    //protected function redirectTo($request)
    //{
    //    if (! $request->expectsJson()) {
    //        return route('login');
    //    }
    //}

    public function handle($request, Closure $next)
    {        
        if ($this->auth->guest()) 
        {      
            if ($request->ajax()) 
            {
                return response('Unauthorized.', 401);
            } 
            else 
            {
                return redirect()->guest('/autentificacion');
            }
        } 

        return $next($request);       
    }    
}
