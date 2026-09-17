@extends('layouts.auth_app')
@section('title')
    Forgot Password
@endsection
@section('content')
    <div class="card card-primary">
        <div class="card-header"><h4>Reset Password</h4></div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            {!!Form::open('name' => 'form', array('route' => array('password.email'), 'method' => 'post', 'files' => true))!!}     
                <div class="form-group">
                    {!!Form::label('Email')!!}
                    {!!Form::email('email', array('id' => 'email', 'class' => "form-control {{ $errors->has('email') ? ' is-invalid' : '' }}", 'tabindex' => '1', 'value' => "{{ old('email') }}")!!}

                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>
                <div class="form-group">
                    {!!Form::button(' Enviar enlace de reinicio', ['type' => 'button', 'title' => 'Confirma contraseña', 'class' => 'btn btn-primary btn-lg btn-block', 'onClick'=>'document.form.submit();']) !!}
                </div>
            {!!Form::close()!!}
        </div>
    </div>
    <div class="mt-5 text-muted text-center">
        Recordar su información de inicio de sesión? <a href="{{ route('login') }}">Registrarse</a>
    </div>
@endsection
