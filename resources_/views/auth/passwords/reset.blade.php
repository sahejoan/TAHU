@extends('layouts.auth_app')
@section('title')
    Reiniciar contraseña
@endsection
@section('content')
    <div class="card card-primary">
        <div class="card-header"><h4>Establecer una nueva contraseña</h4></div>

        <div class="card-body">
            {!!Form::open('name' => 'form', array('action' => array("{{ url('/password/reset') }}"), 'method' => 'post', 'files' => true))!!}     
                @if ($errors->any())
                    <div class="alert alert-danger p-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">

                    {!!Form::label('Email')!!}
                    {!!Form::email('email', array('id' => 'email', 'class' => "form-control {{ $errors->has('email') ? ' is-invalid' : '' }}", 'tabindex' => '1', 'value' => "{{ old('email') }}", 'autofocus', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.password.focus();}')!!}

                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>
                <div class="form-group">
                    {!!Form::label('Contraseña')!!}
                    {!!Form::password('password', array('id' => 'password', 'class' => "form-control {{ $errors->has('password') ? ' is-invalid': '' }}", 'tabindex' => '2', 'value' => "{{ old('email') }}", 'autofocus', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.email.focus();}')!!}
                  
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="control-label">Confirm Password</label>
                    <input id="password_confirmation" type="password"
                           class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid': '' }}"
                           name="password_confirmation" tabindex="2">
                    <div class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </div>
                </div>
                <div class="form-group">
                    {!!Form::button(' Confirmar contraseña', ['type' => 'button', 'title'=>'Confirma contraseña', 'class' => 'btn btn-primary btn-lg btn-block', 'tabindex' => '4', 'onClick'=>'document.form.submit();']) !!}
                </div>
            {!!Form::close()!!}
        </div>
    </div>
    <div class="mt-5 text-muted text-center">
        Recordar su información de inicio de sesión? <a href="{{ route('login') }}">Registrarse</a>
    </div>
@endsection
