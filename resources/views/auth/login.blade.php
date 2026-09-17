@extends('layouts.auth_app')
@section('title')
    i-Llanos .:GRTI los Llanos:.
@endsection
@section('content')
    <div class="card card-primary -mt-10 ">
        <div><h4 align="center" style="color:#FF0000"> i-Llanos Tahu</h4></div>

        <div class="card-body">
            {!!Form::open(array('name' => 'form', 'action' => 'LogControler@store', 'route' => 'log.store', 'method' => 'post', 'files' => true))!!}
            {{-- {!!Form::open(array('name' => 'form', 'route' => array('login'), 'method' => 'post', 'files' => true))!!}  --}}
                @if ($errors->any())
                    <div class="alert alert-warning p-0 alert-dismissible fade show" role="alert" >
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>                                            
                    </div>
                @endif

                @if(Session::has('message-error'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ Session::get('message-error') }}     
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>                
                    </div>
                @endif


                <div class="form-group">
                    <label for="email" class="control-email">Correo electrónico</label>                    
                    {!!Form::email('email', !Cookie::has('email_') ? '' : Cookie::get('email_'), array('id' => 'email', 'aria-describedby' => 'emailHelpBlock', 'class' => "form-control {{ $errors->has('email_') ? ' is-invalid' : '' }}",  'placeholder' => 'Correo electrónico', 'tabindex' => '1', 'autofocus required', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.password.focus();}')) !!}
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                </div>

                <div class="form-group">

                    <div class="d-block">                        
                        <label for="password" class="control-label">Contraseña</label>
                        <div class="float-right">
                            {!! link_to_route('email.index', $title = ' ¿Olvido Contraseña?', null, $attributes = array('class'=>'text-small', 'title'=>'')) !!}                            
                        </div>
                    </div>
                    {!!Form::input('password', 'password', !Cookie::has('password_') ? '' : Cookie::get('password_'), array('class' => 'form-control', 'placeholder' => 'Contraseña', 'tabindex' => '2', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.email.focus();}'))!!}

                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox icheck">                         
                        {!! Form::checkbox('recordar', '1' , ((Cookie::get('recordarsesion') == 'true') ? true : false), array('id' => 'recordar', 'class'=>'flat-red')) !!}
                        Recordar inicio de sesión
                    </div>
                </div>

                <div class="form-group">
                    {!!Form::button(' Entrar', ['type' => 'button', 'title'=>'Confirma contraseña', 'class' => 'btn btn-danger btn-lg btn-block', 'tabindex' => '4', 'onClick'=>'document.form.submit();']) !!}
                </div>
             {!!Form::close()!!}
        </div>
    </div>
    <div ><h6 align="center" style="color:#FF0000"> © GRTI los LLanos</h6></div>
@endsection
