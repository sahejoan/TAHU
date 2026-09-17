<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Auth\Events\Logout;

use App\Models\User;
use DB;
use Auth;
use Session;
use Cookie;

class DeleteSessionData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //        
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {                
        $objusuario = DB::table('users')->where('id','=',Auth::user()->id)->update(['idsesion'=>'']);

        //Session::forget('rid');
        Session::forget('dateconcurrencia');
        Session::forget('id_desig');
        Session::forget('id_area');
        Session::forget('userconection');        
        Session::save();

        if (Cookie::get('recordarsesion')=='false') {
            Cookie::queue(Cookie::forever('password_', ''));        
            Cookie::queue(Cookie::forever('email_', ''));        
        }
        //Desde aqui se borran las variables de seion con:
        //session()->forget('variable_de_sesion_que_quieres_asegurarte_de_borrar');
    }
}
