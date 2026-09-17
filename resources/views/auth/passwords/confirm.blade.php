<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="es" />

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{!!URL::to('img/logoicon.png')!!}">

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <meta http-equiv="Last-Modified" content="0"> 
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">

    <!--
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>
    -->

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{ url('/home') }}"><b>{{ config('app.name') }}</b></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Por favor confirmar su contraseña.</p>

            {!!Form::open('name' => 'form', array('route' => array('password.confirm'), 'method' => 'post', 'files' => true))!!}     

                <div class="input-group mb-3">
                    {!!Form::label('Clave')!!}
                    {!!Form::password('password', array('id' => 'password', 'class' => "form-control {{ $errors->has('password') ? ' is-invalid' : '' }}", 'placeholder' => 'Contraseña', 'required autocomplete' => 'current-password')!!}

                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                    @if ($errors->has('password'))
                        <span class="error invalid-feedback">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        {!!Form::button(' Confirmar contraseña', ['type' => 'button', 'title' => 'Confirma contraseña', 'class' => 'btn btn-primary btn-block', 'onClick' => 'document.form.submit();']) !!}
                    </div>
                    <!-- /.col -->
                </div>
            {!!Form::close()!!}

            <p class="mt-3 mb-1">
                <a href="{{ route('password.request') }}">Recordar contraseña?</a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>

</div>

<script src="{{ mix('js/app.js') }}" defer></script>

</body>
</html>
