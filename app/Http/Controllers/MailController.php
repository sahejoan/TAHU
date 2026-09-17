<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\area;
use App\Models\division;
use App\Http\Controllers\Controller;

use App\Http\Requests\AreaCreateRequest;
use App\Http\Requests\AreaUpdateRequest;
//use Illuminate\Support\Facades\Hash;

use Redirect;
use DateTime;
use Validator;
use Session;
use Exception;
use ValidateException;
use Auth;
use Illuminate\Routing\Route;
use Mail;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $RegxPag = 1000;

    public function __construct()
    {
        
    }  

    public function index()
    {   
      return view('email.index');                
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $r = env('MAIL_USERNAME');
        $correo = $request->all();

        try {

               $objmail = User::select('id', 'name', 'email')->where('email','=',$correo['email'])->first()->toArray();

               if (isset($objmail)){

               if ($objmail['email']==$correo['email']) {

                   $objm = User::find($objmail['id']);

                   $g = $this->generarCodigo(8);                                  

                   $d = $objmail['email'];
                   $m = 'Datos de Recuperación de Cuenta: ';
                   $m1 = 'Usuario: '.$objmail['name'];
                   $m2 = 'Contraseña: '.$g;

                   $data = ['remitente'=>$r, 'destinatario'=>$d, 'm'=>$m, 'm1'=>$m1, 'm2'=>$m2];

                   $p = ['password'=>$g];
                   $objm->fill($p);
                   $objm->save();

                   Mail::send('email.contactwebmaster',$data, function($msj) use ($r, $d){

                       $msj->subject($r);
                       $msj->to($d);

                   });                   

                   //Session::flash('message','Los Datos Fueron Enviados Correctamente a su Correo Electrónico');
                   //return Redirect('/email');
                   return redirect::route('email.index')->withInput()->withErrors('Los Datos Fueron Enviados Correctamente a su Correo Electrónico');
                } else {

                   //Session::flash('message','Operación de Recuperación de Contraseña No Puede Ser Procesada');
                   //return Redirect('/email');
                   return redirect::route('email.index')->withInput()->withErrors('Operación de Recuperación de Contraseña No Puede Ser Procesada');

                }
                } else {

                   //Session::flash('message','Operación de Recuperación de Contraseña No Puede Ser Procesada');
                   //return Redirect('/email');
                   return redirect::route('email.index')->withInput()->withErrors('Operación de Recuperación de Contraseña No Puede Ser Procesada');
                }

        
        } catch (Exception $e) {    

              //Session::flash('message','Operación de Recuperación de Contraseña No Puede Ser Procesada');
              //return Redirect('/email');
              return redirect::route('email.index')->withInput()->withErrors('Operación de Recuperación de Contraseña No Puede Ser Procesada');         
        }
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
    public function update(Request $input, $id)
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

    public function generarCodigo($longitud) {
        //return Hash::make('seniat2022');

        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($pattern)-1;

        for($i=0;$i < $longitud;$i++) { $key .= $pattern[mt_rand(0,$max)]; }
            return $key;        
    }
}