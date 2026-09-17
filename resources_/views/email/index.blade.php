@extends('layouts.auth_app')
@section('title')
    i-Llanos .:GRTI los Llanos:.
@endsection
@section('content')
    <div class="card card-primary -mt-10 ">
        <div ><h2 align="center" style="color:#FF0000"> i-Llanos</h2></div>

        <div class="card-body" style="color:#000000;background-color:#CCCCCC;">
        <center><p class="login-box-msg">Recuperación de clave</p></center>
        <center>La clave se enviará al correo electrónico resgistrado.</center>
        {!!Form::open(array('name' => 'formmail',  'route' => 'email.store', 'method' => 'post', 'files' => true))!!}
            @include('alert.errors')
            @include('alert.request')

            <div class="form-group has-feedback">
                {!!Form::label('&nbsp;Correo Electrónico&nbsp;')!!}
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      
                <div class="form-group has-feedback">
                    {!!Form::text('email', null, array('id' => 'name', 'class' => 'form-control input-md', 'placeholder' => 'email@dominio.com') )!!}         
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4">
                    
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4">                    
                    
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4">
                    {!!Form::button(' Enviar', ['type' => 'button', 'title'=>'Enviar', 'class' => 'btn btn-primary btn-block btn-flat fas fa-solid fa-paper-plane', 'onClick'=>'document.formmail.submit();']) !!}                                        
                </div>
            </div>
        {!!Form::close()!!}

        </div>
    </div>
    <div ><h6 align="center" style="color:#FF0000"> © GRTI los LLanos</h6></div>
@endsection