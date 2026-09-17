<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', array('only' => 'admin'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        Session::put('rid', 0);
        Session::put('dateconcurrencia', '');
        Session::put('id_desig', 0);
        Session::put('id_area', 0);
        Session::save();
        
        return view('home');
    }
}
