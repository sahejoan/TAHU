<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="es" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">

    <title>@yield('title') | {{ config('app.name') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{!!URL::to('img/logoicon.png')!!}">

    <!-- General CSS Files -->
    {!!Html::style('/assets/css/bootstrap.min.css')!!}    

    {!!Html::style('/AdminLTE/plugins/Ionicons/css/ionicons.min.css')!!}
    {!!Html::style('/AdminLTE/plugins/fontawesome-free/css/all.min.css')!!}

    <!-- Template CSS -->
    {!!Html::style('/web/css/style.css')!!}    
    {!!Html::style('/web/css/components.css')!!}    
    {!!Html::style('/assets/css/iziToast.min.css')!!}    
    {!!Html::style('/assets/css/sweetalert.css')!!}    
    {!!Html::style('/assets/css/select2.min.css')!!}    
</head>

<body>
<div id="app">
    <section class="section">
        <div class="container mt-4">
            <div class="row" style="background-image: url('img/illanos.png');">
                <div class="col-md-5 offset-md-3">
                    <div class="login-logo" >
                        <img src="{{ asset('img/logo1.png') }}" srcset="{!!URL::to('img/logo1.png 400w, img/logo1.png 400w,')!!}" sizes="(max-width: 400px) 100vw, 314px" alt="logo" width="100%" class="shadow-light">                                                        
                    </div>                    
                    @yield('content')
                    <div class="simple-footer">
                    {{--Copyright &copy; {{ getSettingValue('application_name') }}  {{ date('Y') }}--}}
                    </div>
                </div>
            </div>
        </div>       
    </section>
</div>

<!-- General JS Scripts -->
{!!Html::script('/assets/js/jquery.min.js') !!}
{!!Html::script('/assets/js/popper.min.js') !!}
{!!Html::script('/assets/js/bootstrap.min.js') !!}
{!!Html::script('/assets/js/jquery.nicescroll.js') !!}

<!-- JS Libraies -->

<!-- Template JS File -->
{!!Html::script('/web/js/stisla.js') !!}
{!!Html::script('/web/js/scripts.js') !!}
<!-- Page Specific JS File -->
</body>
</html>
