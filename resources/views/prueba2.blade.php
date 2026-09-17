<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="es" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    
    <link rel="shortcut icon" type="image/x-icon" href="{!!URL::to('img/logoicon.png')!!}">
    
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

{!!Html::style('/AdminLTE/plugins/fontawesome-free/css/all.min.css')!!}
{!!Html::style('/AdminLTE/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')!!}
{!!Html::style('/AdminLTE/plugins/toastr/toastr.min.css')!!}
{!!Html::style('/AdminLTE/dist/css/adminlte.min.css')!!}

</head>
<body>
Ventana modal
{!!Form::button(' Ver', ['id'=>'btnver', 'type' => 'button', 'title' => 'Mostrar']) !!}                                                                                                                                                                                                       
</body>

{!!Html::script('/AdminLTE/plugins/jquery/jquery.min.js')!!}
{!!Html::script('/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')!!}
{!!Html::script('/AdminLTE/plugins/sweetalert2/sweetalert2.min.js')!!}
{!!Html::script('/AdminLTE/plugins/toastr/toastr.min.js')!!}
{!!Html::script('/AdminLTE/dist/js/adminlte.min.js')!!}

<script>

$("#btnver").click(function(e){

  //Esto es lo que muestra la ventana modal que esta oculta
  $("#dlgModalMiVentana").modal({backdrop: "static", keyboard: true});

});

</script>
</html>