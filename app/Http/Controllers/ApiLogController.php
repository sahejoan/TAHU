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
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Str;

class ApiLogController extends Controller {

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
  public function store(Request $request) {

  }

  public function EntrarApp(Request $request)
  { 

    $email = $request['email'];    
    $password = $request['password'];
    $recordar = false;

        if (Auth::attempt(array('email' => $request['email'], 'password' => $request['password'], 'estatus' => 'ACTIVO'), $recordar)) 
        {
          $user = User::where('email','=',$email)->first();
                      
          if ($user->estatus == "ACTIVO") 
          {
            Auth::user()->tokens()->delete();
            $token = Auth::user()->createToken('appiseniat_auth_token')->plainTextToken;
            return response()->json([
              'access_token' => $token,
              'token_type' => 'Bearer'              
              //'user' => $user,
            ],200);
          } else {
            return response()->json("Denegado", 200);
          }
        }    

    return response()->json("Denegado",200);
  }

  //public function logout(Request $request) 
  public function SalirApp(Request $request) 
  {       
    $headers = $request->header('Authorization');

    $bearer="";
    if (Str::startsWith($headers, 'Bearer')) {             
      $bearer = Str::substr($headers, 7); 

      $token = PersonalAccessToken::findToken($bearer);
      if (isset($token)){
        $token->delete();
        return response()->json('App logout exitoso',200);    
      } else {
        return response()->json('App logout fallido',200);    
      }    
    } 

    return response()->json('App logout fallido',200);    
  }

  public function Usuarios_(Request $request)
  {
    return response()->json($request->User());
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

}
?>