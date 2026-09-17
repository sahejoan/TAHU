@extends('layouts.auth_app')
@section('title')
    i-Llanos .:GRTI los Llanos:.
@endsection
@section('content')
    <div class="card card-primary -mt-10 ">
        <div ><h2 align="center" style="color:#FF0000"> i-Llanos</h2></div>

        <div class="card-body">

            <div id="mensajed-danger1" class="alert alert-dismissible" role="alert" style="background: #f8c471;">
                <p class="login-box-msg" style="color:#000000;">La sesión expiro</p>      
                <p style="color:#000000;"><center>Fallo la conexión</center></p>
                <strong style="color:#000000;"><center>La sesión se cerro por seguridad</center></strong>
                <p style="color:#000000;"><center>Si el problema persiste cierre el navegador</center></p>
            </div>

            <center>
                <a href="{!!URL::to('/')!!}" style="font-size: 25px;"> Volver a entrar<a>
            </center>

        </div>
    </div>
    <div ><h6 align="center" style="color:#FF0000"> © GRTI los LLanos</h6></div>
@endsection