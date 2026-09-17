<?php

namespace App\Http\Middleware;

//use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Session;
use DB;
use Illuminate\Support\Str;
use \Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use Auth;
//extends Middleware
class Authenticate_ 
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

    public function handle($request, Closure $next)
    {        
        $headers = $request->header('Authorization');

        $bearer="";

        if (Str::startsWith($headers, 'Bearer')) {             
            $bearer = Str::substr($headers, 7); 
            $token = PersonalAccessToken::findToken($bearer);
        } 

        if (isset($token)) {
            $user = User::find($token->tokenable_id);
            if (isset($user['id'])) {
                Auth::login($user);
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'error' => 'Access denied.',
            //'BAREAR' => $bearer
        ]);
    }    
}
