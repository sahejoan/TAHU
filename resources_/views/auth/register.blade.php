@extends('layouts.auth_app')
@section('title')
    Registrarse
@endsection
@section('content')
    <div class="card card-primary">
        <div class="card-header"><h4>Registrase</h4></div>

        <div class="card-body pt-1">

            {!!Form::open('name' => 'form', array('route' => array('register'), 'method' => 'post', 'enctype' => 'multipart/form-data', 'files' => true))!!}     
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">Full Name:</label><span
                                    class="text-danger">*</span>
                                    {!!Form::text('name', array('id' => 'firstName', 'class' => "form-control {{ $errors->has('name') ? ' is-invalid' : '' }}", 'tabindex' => '1', 'tabindex' => '1', 'placeholder' => 'Escribir nombre comprelo', 'value' => "{{ old('name') }}", 'autofocus required')!!}

                            <div class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">

                            {!!Form::label('Email')!!}
                            <span class="text-danger">*</span>
                            {!!Form::email('email', array('id' => 'email', 'class' => "form-control {{ $errors->has('email') ? ' is-invalid' : '' }}", 'placeholder' => 'Escribir email', 'tabindex' => '1', 'value' => "{{ old('email') }}", 'required autofocus')!!}

                            <div class="invalid-feedback">
                                {{ $errors->first('email') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!!Form::label('Password:')!!}                            
                            <span class="text-danger">*</span>
                            {!!Form::password('password', array('id' => 'password', 'class' => "form-control {{ $errors->has('password') ? ' is-invalid' : '' }}", 'placeholder' => 'Escribir contraseña', 'tabindex' => '1', 'required' => 'current-password', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.password_confirmation.focus();}')!!}
                            <div class="invalid-feedback">
                                {{ $errors->first('password') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!!Form::label('Confirmar contraseña:')!!}                            
                            <span class="text-danger">*</span>

                            {!!Form::password('password_confirmation', array('id' => 'password_confirmation', 'class' => "form-control {{ $errors->has('password_confirmation') ? ' is-invalid': '' }}", 'placeholder' => 'Confirmar contraseña', 'tabindex' => '2', 'required' => 'current-password', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.password.focus();}')!!}
                            <div class="invalid-feedback">
                                {{ $errors->first('password_confirmation') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div class="form-group">
                            {!!Form::button(' Registrar', ['type' => 'button', 'title' => 'Registrar', 'class' => 'btn btn-primary btn-lg btn-block', 'onClick'=>'document.form.submit();']) !!}
                        </div>
                    </div>
                </div>
            {!!Form::close()!!}
        </div>
    </div>
    <div class="mt-5 text-muted text-center">
        Already have an account ? <a
                href="{{ route('login') }}">SignIn</a>
    </div>
@endsection
