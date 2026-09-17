@extends('layouts.auth_app')
@section('title')
    i-Llanos .:GRTI los Llanos:.
@endsection
@section('content')
    <div class="card card-primary -mt-10 ">
        <div ><h2 align="center" style="color:#FF0000"> i-Llanos</h2></div>

        <div class="card-body">

            <div id="mensajed-danger1" class="alert alert-danger alert-dismissible" role="alert">
                <p class="login-box-msg">Sesión</p>      
                <strong style="color:#ffffff;"><center>Otro equipo inicio sesión con el mismo usuario se cerrará ésta sesión</center></strong>
            </div>

            <center>
                <a href="{!!URL::to('/')!!}" style="font-size: 25px;"> Volver a entrar<a>
            </center>

        </div>
    </div>
    <div ><h6 align="center" style="color:#FF0000"> © GRTI los LLanos</h6></div>
@endsection