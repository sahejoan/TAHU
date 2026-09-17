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

<div><i class="fas fa-sign-out"></i></div>

{!!Form::button(' Mensaje1', ['id'=>'Boton1', 'type' => 'button', 'title' => 'Mostrar']) !!}
{!!Form::button(' Mensaje2', ['id'=>'Boton2', 'type' => 'button', 'title' => 'Mostrar']) !!}
{!!Form::button(' Mensaje3', ['id'=>'Boton3', 'type' => 'button', 'title' => 'Mostrar']) !!}
 
</body>

{!!Html::script('/AdminLTE/plugins/jquery/jquery.min.js')!!}
{!!Html::script('/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')!!}
{!!Html::script('/AdminLTE/plugins/sweetalert2/sweetalert2.min.js')!!}
{!!Html::script('/AdminLTE/plugins/toastr/toastr.min.js')!!}
{!!Html::script('/AdminLTE/dist/js/adminlte.min.js')!!}

<script>

//Posicion
var men1 = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
    });

var men2 = Swal.mixin({
      toast: true,
      position: 'top-center',
      showConfirmButton: true,
      timer: 3000, 
      buttons : true, 
    });

var men3 = Swal.mixin({
      toast: true,
      position: 'top-center',
      showConfirmButton: true,
      timer: 3000,  
    });

 //Mensajes
 $('#Boton1').click(function() {
      men1.fire({
        icon: 'success',
        title: 'Este es el mensaje 1'
      });
  });


$('#Boton2').click(function() {
  men2.fire({
  title : 'Titulo',
  text : 'Este es el mesaje 3',
  imageUrl: '{{ asset('img/logo-left.png') }}',
  imageWidth : 400,
  imageHeight : 200,
  imageAlt: 'Custon image',  
 });
});


$('#Boton3').click(function() {
men3.fire({
  title: '<strong>HTML <u>Titulo</u></strong>',
  icon: 'info',
  html:
    'Estas usando <b>bold text</b>, ' +
    '<a href="//sweetalert2.github.io">links</a> ' +
    'y otros etiquetas HTML',
  showCloseButton: true,
  showCancelButton: true,
  focusConfirm: false,
  confirmButtonText:
    '<i class="fa fa-thumbs-up"></i> Confirmar!',
  confirmButtonAriaLabel: 'Thumbs up, great!',
  cancelButtonText:
    '<i class="fa fa-thumbs-down">Cancelar</i>',
  cancelButtonAriaLabel: 'Thumbs down'
})
});
</script>
</html>