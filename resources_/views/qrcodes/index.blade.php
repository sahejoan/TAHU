@extends('layouts.app') 


@section('css')
{!!Html::style('/css/in/html5-qrcode-css.css')!!}  
@stop

@section('content') 
  <div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6">
      <section class="section">
        <div class="section-header" align="Left">
          <h3 class="page__heading">
          <div class="btn-group">
            <div class="dropdown">
              <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Acción
              </button>

              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @if (Auth::user()->hasAnyPermission('12_Listar_Asistencia'))
                  <a class="dropdown-item" href="{{route('scan2.index')}}"><i class="fa fa-th-list"></i> Lista de asistencias</a>
                  <div class="dropdown-divider">                    
                  </div>                                    
                @endif

                @if (Auth::user()->hasAnyPermission('20_Generarqr'))
                  {!!Form::open(array('name' => 'form', 'route' => array('listadoqr'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
                    <input type="hidden" name="buscarv" id="buscarv" value="">
                    <a class="dropdown-item" href="#" onclick="document.form.submit();"><i class="fa fa-qrcode"></i> Gernerar QR</a>
                  {!!Form::close()!!}                  
                @endif                  
              </div>
            </div>
          </div>  

          @if (Auth::user()->hasAnyPermission('12_Capturar_Asistencia'))
            Captura
          @endif          
          </h3>          
        </div>

        @if (Auth::user()->hasAnyPermission('12_Capturar_Asistencia'))
        <div class="section-body">
          <div class="card">
            <div class="card-body m-20 ml-30">
              <div class="container -m-50 w3-display-container">
                {{-- ______________ --}}
                <div id="reader" style="width:100%;">                  
                </div>                            
                <div id="result">                  
                </div>

                <script>
                      function docReady(fn) {
                        // see if DOM is already available
                        if (document.readyState === "complete" ||
                          document.readyState === "interactive") {
                          // call on next available tick
                          setTimeout(fn, 1);                                          
                        } else {
                          document.addEventListener("DOMContentLoaded", fn);
                        }
                      }
                           
                      docReady(function() {
                        var resultContainer = document.getElementById('result');
                        var lastResult, countResults = 0;
                           
                        function onScanError(errorMessage) {
                          //handle scan error
                        }
                        let config = { fps: 10, qrbox: {width: 250, height: 250}, aspectRatio: 1 };
                           
                        var html5QrcodeScanner = new Html5QrcodeScanner (
                        "reader", config);

                        html5QrcodeScanner.render(onScanSuccess, onScanError);
                      });                                   
                </script>
                                                      
                <script type="text/javascript">
                      function onScanSuccess(qrCodeMessage) {
                        document.getElementById('busqueda').value = qrCodeMessage;
                        document.getElementById('boton').click();                                                                       
                      }

                      /* esto esta de mas */
                      /*
                      function boton1() {
                        Swal.fire({
                          title: 'Sweet!',
                          text: 'Hola',
                          imageUrl: 'https://unsplash.it/400/200',
                          imageWidth: 400,
                          imageHeight: 200,
                          imageAlt: 'Custom image',
                        });
                      }
                    
                      function onScanError(errorMessage) {
                        //handle scan error
                      }
                         
                      var html5QrcodeScanner = new Html5QrcodeScanner (
                        "reader", {
                          fps: 10,
                          qrbox: 250                        
                      });
                      
                      html5QrcodeScanner.render(onScanSuccess, onScanError);
                      */
                </script>                          
              </div>                            

              {{-- ---------------------- --}}                                     
              <div class="d-md-flex justify-content-md-end">
                <form action="#" method="POST" ENCTYPE="multipart/form-data" file=true>
                  <input type="hidden" name="_token1" id="_token1" value="{{ csrf_token() }}">
                  @if ($lineadetiempo==4)
                    <input value="Buscar" type="button" name="" id="boton" class="btn btn-danger" hidden onClick="Procesar();">
                  @endif
                  @if ($lineadetiempo==8)
                    <input value="Buscar" type="button" name="" id="boton" class="btn btn-danger" hidden onClick="Procesar8();">                  
                  @endif
                  <input type="text" name="scan" id="busqueda" class="form-control" readonly hidden>
                </form>
              </div>                                                                                                                                                                               
            </div>
          </div>          
        </div>
        @endif              
      </section>
    </div>


    <div class="col-xs-6 col-sm-6 col-md-6">
      <section class="section">

        @if (Auth::user()->hasAnyPermission('12_Capturar_Asistencia'))
        <div class="section-header" align="center">
          <h3 class="page__heading">Datos de asistencia</h3>
        </div>

        <div class="section-body">
          <div class="card">
            <div class="card-body m-20 ml-30">
              <div class="container -m-50 w3-display-container">              

                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3">
                    <!-- Inicio foto -->
                    <div class="form-group" align="left">                       
                      <div id="resultfoto" style="width: 200px; height: 120px;">
                        <div id="fotofuncionario" style="width: 100px; height: 100px;">

                        </div>
                      </div>
                    </div>                             
                    <!-- fin foto -->
                  </div>

                  <div class="col-xs-9 col-sm-9 col-md-9">
                    <div class="form-group" align="left">       
                      <div id="resultdatos">
                        <div id="datosfuncionario">

                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group" align="left">       
                      <div id="resultasistencia">
                        <div id="asistenciafuncionario">

                        </div>
                      </div>
                    </div>
                  </div>
                </div>

      <div id="mensaje-observacion" class="alert alert-success alert-dismissible" role="alert" style="display:none" align="center">
        <strong>Observación guardada con éxito</strong>    
      </div>                                                   

      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botonobservacion" style="display:none;" align="center">
          <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalobservacion" style="width: 100%;"><i class="fas fa-comment-dots"></i> Observación</a>
      </div>                                                   
      </div>
      &nbsp;
      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botoncompra" style="display:none;" align="center">
          <a class="btn btn-default" href="#" style="width: 100%;" onclick="GuardarOtraObservacion('Salida: Comisión de compra');"><i class="fas fa-comment-dots"></i> Compra</a>
      </div>
      </div>      
      &nbsp;
      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botonpermiso" style="display:none;" align="center">
          <a class="btn btn-success" href="#" style="width: 100%;" onclick="GuardarOtraObservacion('Salida: Por permiso');"><i class="fas fa-comment-dots"></i> Permiso</a>
      </div>      
      </div>

      &nbsp;
      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botonFiscalizacion" style="display:none">
          <a class="btn btn-warning" href="#" style="width: 100%;" onclick="GuardarOtraObservacion('Salida: Fiscalización');"><i class="fas fa-comment-dots"></i> Fiscalización</a>
      </div>
      </div>
      </div>                                                   
      &nbsp;
      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botoncontrolingresos" style="display:none">
          <a class="btn btn-warning" href="#" style="width: 100%;" onclick="GuardarOtraObservacion('Salida: Control de ingreso');"><i class="fas fa-comment-dots"></i> Control de Ingreso</a>
      </div>
      </div>                                                   
      &nbsp;
      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botonnotificacion" style="display:none">
          <a class="btn btn-warning" href="#" style="width: 100%;" onclick="GuardarOtraObservacion('Salida: Notificación');"><i class="fas fa-comment-dots"></i> Notificación</a>
      </div>
      </div>                                                         
      &nbsp;
      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botondivulgacion" style="display:none">
          <a class="btn btn-warning" href="#" style="width: 100%;" onclick="GuardarOtraObservacion('Salida: Divulgación');"><i class="fas fa-comment-dots"></i> Divulgación</a>
      </div>      
      </div>
      &nbsp;
      <div class="row" align="center">
      <div class="col-xs-12 col-sm-12 col-md-12" id="botonvdf" style="display:none">
          <a class="btn btn-warning" href="#" style="width: 100%;" onclick="GuardarOtraObservacion('Salida: VDF');"><i class="fas fa-comment-dots"></i> VDF</a>
      </div>      
      </div>
              </div>
            </div>
          </div>
        </div>        
        @endif

      </section> 

    </diV>
  </div>



  <div class="section">                                                                                                                                   
    <!-- Modal -->
    <div class="modal fade" id="modalobservacion" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #9966FF;">
            <h5 class="modal-title" id="staticBackdropLabel">Observación de asistencia</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>            
          </div>

          <div class="modal-body">
            <div id="mensaje-success" class="alert alert-success alert-dismissible" role="alert" style="display:none">
              <strong>Observación guardada con éxito</strong>    
            </div>                                                   

            <div id="mensaje-danger" class="alert alert-danger alert-dismissible" role="alert" style="display:none">
              <strong>No se pudo procesar la información</strong>    
            </div>                                                   

            <div class="row"> 
              <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group" align="left">       
                  <div id="resultobservacion">
                    <div id="datosobservacion">
                      Observación:
                      {!!Form::open(array('name' => 'formobservacion', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
                        <input type="hidden" name="_token2" id="_token2" value="{{ csrf_token() }}">
                        <input type="hidden" name="horasalida" id="horasalida" value="">
                        <input name='id_asistencia' id='id_asistencia' type='hidden' value=''>
                        {!!Form::textarea('observacion',null, array('id' => 'observacion', 'cols'=>'50', 'rows'=>'2', 'class' => 'form-control input-sm col-xs-5'))!!}
                      {!!Form::close()!!}                                                                                         
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary"  onclick="GuardarObservacion(0);">Guardar</button>
          </div>

        </div>
      </div>
    </div>
  </div>                  
</section>

@endsection

@section("scripts")
  <!-- Libreria para la camara lectora del QR -->
  {!!Html::script('/AdminLTE/scan-qrcode/minified/html5-qrcode.min_.js')!!}
  
  {!! Html::script('/js/in/moment.js') !!}      

  @include('alert.general')  

@if (!(Auth::user()->hasAnyPermission('11_Permisos_Usuarios')))              
  <script> 
  $(document).ready(function() {
    document.getElementById('reader__dashboard_section_swaplink').style.display = 'none';       

    $("#reader__dashboard_section_csr").click(function(){
      document.getElementById('reader__dashboard_section_swaplink').style.display = 'none';       
    });
  });
  </script>
 @endif

  <script>
    //$(document).ready(function() {
    //  $('#reader__dashboard_section_swaplink').css('cursor', 'default').css('pointerEvents', 'none');
    //  $('#reader__dashboard_section_swaplink').css('background', '#b2b9c1');
    //});

   //var pe = moment('2022-10-31 17:33:00', 'DD-MM-YYYY').format('DD-MM-YYYY');
   //var ps = moment('2022-10-31 17:33:00', 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');

   //var pe2 = moment('2022-10-31 17:33:00', 'DD-MM-YYYY').format('DD-MM-YYYY');
   //var ps2 = moment('2022-10-31 17:33:00', 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
    var esperar = 0;
    var tiempodeespera = 0;
    var ultimacedula = "";

    let timerId = setTimeout(() => iniciar(), 300000);

    function iniciar() {
      ultimacedula = 0;
    }
  </script>

  <script>
    var enmodal = "{{ env('APP_EN_MODAL_ASISTENCIA') }}";
    var asistenciaautomatica = "{{ env('APP_ASISTENCIA_AUTOMATICA') }}";
  </script>

  @if ($lineadetiempo==4)
  <script>
    function Procesar() {
      if (esperar==0) {
      document.getElementById('botonobservacion').style.display = 'none'; 
      document.getElementById('botoncontrolingresos').style.display = 'none'; 
      document.getElementById('botonnotificacion').style.display = 'none'; 
      document.getElementById('botonFiscalizacion').style.display = 'none'; 
      document.getElementById('botoncompra').style.display = 'none'; 
      document.getElementById('botonpermiso').style.display = 'none'; 
      document.getElementById('botondivulgacion').style.display = 'none'; 
      document.getElementById('botonvdf').style.display = 'none'; 

      document.getElementById('mensaje-observacion').style.display = 'none'; 

      if (asistenciaautomatica) {
        BuscarDatosFuncionarios();
      } else {
        BuscarParaPreguntar();
      }
      }
    }
  </script>
  @endif

  @if ($lineadetiempo==8)
  <script>
    function Procesar8() {
      if (esperar==0) {
      document.getElementById('botonobservacion').style.display = 'none';       
      document.getElementById('botoncontrolingresos').style.display = 'none'; 
      document.getElementById('botonnotificacion').style.display = 'none'; 
      document.getElementById('botonFiscalizacion').style.display = 'none'; 
      document.getElementById('botoncompra').style.display = 'none'; 
      document.getElementById('botonpermiso').style.display = 'none'; 
      document.getElementById('botondivulgacion').style.display = 'none';       
      document.getElementById('botonvdf').style.display = 'none'; 

      document.getElementById('mensaje-observacion').style.display = 'none'; 
      if (asistenciaautomatica) {
        BuscarDatosFuncionarios8();
      } else {
        BuscarParaPreguntar8();
      }
      }
    }
  </script>
  @endif

  @if ($lineadetiempo==4)
  <script>
    function MostrarDatos(response) {
      var fe = moment(response.hora_e, 'DD-MM-YYYY').format('DD-MM-YYYY');
      var he = moment(response.hora_e, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');

      if (response.hora_s != "") {
        var fs = moment(response.hora_s, 'DD-MM-YYYY').format('DD-MM-YYYY');
        var hs = moment(response.hora_s, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
      } else {
        var fs = "";
        var hs = "";
      }

      if (response.hora_e2 != "") {
        var fe2 = moment(response.hora_e2, 'DD-MM-YYYY').format('DD-MM-YYYY');
        var he2 = moment(response.hora_e2, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
      } else {
        var fe2 = "";
        var he2 = "";
      }

      if (response.hora_s2 != "") {
        var fs2 = moment(response.hora_s, 'DD-MM-YYYY').format('DD-MM-YYYY');
        var hs2 = moment(response.hora_s, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
      } else {
        var fs2 = "";
        var hs2 = "";
      }
                  
      var htmlresult = `<center><div id="fotofuncionario_" style="border:solid; width: 150px; height: 150px;"><img src="`+response.foto+`" style="width: 150px; height: 150px; border:solid 1px;"/></div></center>`;
      htmlresult = htmlresult + `<div id="datosfuncionario_" style="width: 100%;">`+
      `<table style="width:100%;">`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.cedula+`</td></tr>`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.nombre+`&nbsp`+response.apellido+`</td></tr>`+
      `</table>`+
      `</div>`+
      `<div id="asistenciafuncionario_" align="left">`+
      `<table style="width:100%;">`+
      `<tr><td style='color: #000000; font-weight: bold; font-size:18px;'></td><td style='color: #000000; font-weight: bold; font-size:18px;'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;'>Hora</td></tr>`+
      `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>Entrada: </td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+fe+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+he+`</td></tr>`+
      `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>Salida: </td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+fs+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+hs+`</td></tr>`+                    
      `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>Entrada: </td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+fe2+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+he2+`</td></tr>`+
      `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>Salida: </td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+fs2+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+hs2+`</td></tr>`+                    
      `</table>`+
      `</div>`; 

        var vistamen = Swal.mixin({
          position: 'center',
          showConfirmButton: true,
          timer: 4000,
        });

        vistamen.fire({
          title: "<center><h5 style='font-weight: bold; color:black'>Asistencia procesada</h5></center>",    
          html: htmlresult,
          confirmButtonText: '<center><i class="fa fa-thumbs-up"></i> Continuar!</center>',
        }).then((result) => {  });
    }   
  </script>
  @endif

  @if ($lineadetiempo==8)
  <script>
    function MostrarDatos8(response) {
      var fe = moment(response.hora_r, 'DD-MM-YYYY').format('DD-MM-YYYY');
      var he = moment(response.hora_r, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  
      var htmlresult = `<center><div id="fotofuncionario_" style="border:solid; width: 150px; height: 150px;"><img src="`+response.foto+`" style="width: 150px; height: 150px; border:solid 1px;"/></div></center>`;
      htmlresult = htmlresult + `<div id="datosfuncionario_" style="width: 100%;">`+
      `<table style="width:100%;">`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.cedula+`</td></tr>`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.nombre+`&nbsp`+response.apellido+`</td></tr>`+
      `</table>`+
      `</div>`+
      `<div id="asistenciafuncionario_" align="left">`+
      `<table style="width:100%;">`+
      `<tr><td style='color: #000000; font-weight: bold; font-size:18px;'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;'>Hora</td></tr>`+
      `<tr><td style='background-color: #999999; color: #000000; font-weight: bold; font-size:18px;'>`+fe+`</td><td style='background-color: #999999; color: #000000; font-weight: bold; font-size:18px;'>`+he+`</td></tr>`+
      `</table>`+
      `</div>`; 

        var vistamen = Swal.mixin({
          position: 'center',
          showConfirmButton: true,
          timer: 4000,
        });

        vistamen.fire({
          title: "<center><h5 style='font-weight: bold; color:black'>Asistencia procesada</h5></center>",    
          html: htmlresult,
          confirmButtonText: '<center><i class="fa fa-thumbs-up"></i> Continuar!</center>',
        }).then((result) => {  });
    }   
  </script>
  @endif

  @if ($lineadetiempo==4)
  <script>        
    function BuscarDatosFuncionarios() { 
      var comparar = $("#busqueda").val();
      if (ultimacedula != comparar) {
        var route = "{{ route('verdatosfuncionario') }}";
        var token = $("#_token1").val();
        var cedula = $("#busqueda").val();        
        $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {cedula: cedula},
          beforeSend: function() {
            esperar = 1;
          },
          error: function(response) {},
          success: function(response) {
              esperar = 0;              
              if ((response!="Error") && (response!="registrado") && (response!="No tiene designación activa")) {                
                  if (typeof response.id_asistencia) {
                    $('#botonobservacion').fadeIn();                    
                    $('#botoncontrolingresos').fadeIn();
                    $('#botonnotificacion').fadeIn();
                    $('#botonFiscalizacion').fadeIn();
                    $('#botoncompra').fadeIn();
                    $('#botonpermiso').fadeIn();
                    $('#botondivulgacion').fadeIn();
                    $('#botonvdf').fadeIn();

                    $('#id_asistencia').val(response.id_asistencia);
                  }

                  ultimacedula = $("#busqueda").val();                  

                  if (enmodal && asistenciaautomatica) {
                    MostrarDatos(response);
                  }                    

                  var fe = moment(response.hora_e, 'DD-MM-YYYY').format('DD-MM-YYYY');
                  var he = moment(response.hora_e, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');

                  if (response.hora_s != "") {
                    var fs = moment(response.hora_s, 'DD-MM-YYYY').format('DD-MM-YYYY');
                    var hs = moment(response.hora_s, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  } else {
                    var fs = "";
                    var hs = "";
                  }

                  if (response.hora_e2 != "") {
                    var fe2 = moment(response.hora_e2, 'DD-MM-YYYY').format('DD-MM-YYYY');
                    var he2 = moment(response.hora_e2, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  } else {
                    var fe2 = "";
                    var he2 = "";
                  }

                  if (response.hora_s2 != "") {
                    var fs2 = moment(response.hora_s2, 'DD-MM-YYYY').format('DD-MM-YYYY');
                    var hs2 = moment(response.hora_s2, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  } else {
                    var fs2 = "";
                    var hs2 = "";
                  }
                  
                  $('#fotofuncionario').remove();            
                  $('#datosfuncionario').remove();            
                  $('#asistenciafuncionario').remove();            

                  $("#resultfoto").append(`<div id="fotofuncionario" style="width: 100px; height: 100px;"><img src="`+response.foto+`" style="width: 100px; height: 100px; border:solid 1px;"/></div>`);
                  $("#resultdatos").append(`<div id="datosfuncionario" align="left">`+
                    `<table>`+
                    `<tr><td>Cédula: </td><td>`+response.cedula+`</td></tr>`+
                    `<tr><td>Nombre: </td><td>`+response.nombre+`</td></tr>`+
                    `<tr><td>Apellido: </td><td>`+response.apellido+`</td></tr>`+                    
                    `</table>`+
                    `</div>`);
                  $("#resultasistencia").append(`<div id="asistenciafuncionario" align="left">`+
                    `<table style="width:100%;">`+
                    `<tr><td style='color: #000000; font-weight: bold; font-size:18px;'></td><td style='color: #000000; font-weight: bold; font-size:18px;'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;'>Hora</td></tr>`+
                    `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>Entrada: </td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+fe+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+he+`</td></tr>`+
                    `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>Salida: </td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+fs+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+hs+`</td></tr>`+                    
                    `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>Entrada: </td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+fe2+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+he2+`</td></tr>`+
                    `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>Salida: </td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+fs2+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+hs2+`</td></tr>`+                    
                    `</table>`+
                    `</div>`);                    
              }
              if (response=="registrado") {
                var ultimacedula = "";
                MostrarMensajeGeneral("Ya está registrado");
              }
              if (response=="No tiene designación activa") {
                var ultimacedula = "";
                MostrarErrorGeneral("No tiene designación activa");
              }                                                        
          }  
        }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }          
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          }                  
        });            
      } else {                
        MostrarMensajeGeneral("Ya está registrado");
      }
    }  
  </script> 
  @endif

  @if ($lineadetiempo==8)
  <script>        
    function BuscarDatosFuncionarios8() { 
      var comparar = $("#busqueda").val();
      if (ultimacedula != comparar) {
        var route = "{{ route('verdatosfuncionario8') }}";
        var token = $("#_token1").val();
        var cedula = $("#busqueda").val();        
        $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {cedula: cedula},
          beforeSend: function() {
            esperar = 1;
          },
          error: function(response) {},
          success: function(response) {              
              esperar = 0;
              if ( (response!="Error") && (response!="registrado") && (response!="No tiene designacion activa")) {                
                  if (typeof response.id_asistencia) {
                    $('#botonobservacion').fadeIn();
                    $('#botoncontrolingresos').fadeIn();
                    $('#botonnotificacion').fadeIn();
                    $('#botonFiscalizacion').fadeIn();
                    $('#botoncompra').fadeIn();
                    $('#botonpermiso').fadeIn();
                    $('#botondivulgacion').fadeIn();
                    $('#botonvdf').fadeIn();

                    $('#id_asistencia').val(response.id_asistencia);
                  }

                  ultimacedula = $("#busqueda").val();                  

                  if (enmodal && asistenciaautomatica) {
                    MostrarDatos8(response);
                  }                    

                  var fe = moment(response.hora_r, 'DD-MM-YYYY').format('DD-MM-YYYY');
                  var he = moment(response.hora_r, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  
                  $('#fotofuncionario').remove();            
                  $('#datosfuncionario').remove();            
                  $('#asistenciafuncionario').remove();            

                  $("#resultfoto").append(`<div id="fotofuncionario" style="width: 100px; height: 100px;"><img src="`+response.foto+`" style="width: 100px; height: 100px; border:solid 1px;"/></div>`);
                  $("#resultdatos").append(`<div id="datosfuncionario" align="left">`+
                    `<table>`+
                    `<tr><td>Cédula: </td><td>`+response.cedula+`</td></tr>`+
                    `<tr><td>Nombre: </td><td>`+response.nombre+`</td></tr>`+
                    `<tr><td>Apellido: </td><td>`+response.apellido+`</td></tr>`+                    
                    `</table>`+
                    `</div>`);
                  $("#resultasistencia").append(`<div id="asistenciafuncionario" align="left">`+
                    `<table style="width:100%;">`+
                    `<tr><td style='color: #000000; font-weight: bold; font-size:18px;'></td><td style='color: #000000; font-weight: bold; font-size:18px;'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;'>Hora</td></tr>`+
                    `<tr><td style='background-color: #999999; color: #000000; font-weight: bold; font-size:18px;'>Registro: </td><td style='background-color: #f2d7d5; color: #f1f1f1; font-weight: bold; font-size:18px;'>`+fe+`</td><td style='background-color: #f2d7d5; color: #f1f1f1; font-weight: bold; font-size:18px;'>`+he+`</td></tr>`+
                    `</table>`+
                    `</div>`);                    
              }
              if (response=="registrado") {
                var ultimacedula = "";
                MostrarMensajeGeneral("Ya está registrado");
              }
              if (response=="No tiene designación activa") {
                var ultimacedula = "";
                MostrarErrorGeneral("No tiene designación activa");
              }                                          
          }  
        }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }          
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          }                  
        });            
      } else {                
        MostrarMensajeGeneral("Ya está registrado");
      }
    }  
  </script> 
  @endif

  @if ($lineadetiempo==4)
  <script>
    function BuscarParaPreguntar() {
        var comparar = $("#busqueda").val();
        var route = "{{ route('verdatosfuncionario2') }}";
        var token = $("#_token1").val();
        var cedula = $("#busqueda").val();
        $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {cedula: cedula},
          beforeSend: function() {
            esperar = 1;
          },
          error: function(response) {},
          success: function(response) {                          
              if ( (response!="Error") && (response!="registrado")) {                
                 VentanaPreguntar(response, cedula);   
              }
              if (response=="registrado") {
                var ultimacedula = "";
                MostrarMensajeGeneral("Ya está registrado");
              }
          }  
        }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }          
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
              esperar = 0;
          }                  
        });
    }
  </script>
  @endif


  @if ($lineadetiempo==8)
  <script>
    function BuscarParaPreguntar8() {
        var comparar = $("#busqueda").val();
        var route = "{{ route('verdatosfuncionario82') }}";
        var token = $("#_token1").val();
        var cedula = $("#busqueda").val();
        $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {cedula: cedula},
          beforeSend: function() {
            esperar = 1;
          },
          error: function(response) {},
          success: function(response) {              
              if ( (response!="Error") && (response!="registrado")) {                
                 VentanaPreguntar8(response, cedula);   
              }
              if (response=="registrado") {
                var ultimacedula = "";
                MostrarMensajeGeneral("Ya está registrado");
              }
          }  
        }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }          
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          }
          esperar = 0;                  
        });
    }
  </script>
  @endif


  @if ($lineadetiempo==4)
  <script>
    function VentanaPreguntar(response, cedula_) {
      var cedula = cedula_;
      var fe = moment(response.hora_e, 'DD-MM-YYYY').format('DD-MM-YYYY');
      var he = moment(response.hora_e, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');

      if (response.hora_s != "") {
        var fs = moment(response.hora_s, 'DD-MM-YYYY').format('DD-MM-YYYY');
        var hs = moment(response.hora_s, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
      } else {
        var fs = "";
        var hs = "";
      }

      if (response.hora_e2 != "") {
        var fe2 = moment(response.hora_e2, 'DD-MM-YYYY').format('DD-MM-YYYY');
        var he2 = moment(response.hora_e2, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
      } else {
        var fe2 = "";
        var he2 = "";
      }

      if (response.hora_s2 != "") {
        var fs2 = moment(response.hora_s, 'DD-MM-YYYY').format('DD-MM-YYYY');
        var hs2 = moment(response.hora_s, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
      } else {
        var fs2 = "";
        var hs2 = "";
      }
                  
      var htmlresult = `<center><div id="fotofuncionario_" style="border:solid; width: 100px; height: 100px;"><img src="`+response.foto+`" style="width: 100px; height: 100px; border:solid 1px;"/></div></center>`;
      htmlresult = htmlresult + `<div id="datosfuncionario_" style="width: 100%;">`+
      `<table style="width:100%;">`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.cedula+`</td></tr>`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.nombre+`&nbsp`+response.apellido+`</td></tr>`+
      `</table>`+
      `</div>`+
      `<div id="asistenciafuncionario_" align="left">`+
      `<table style="width:100%;">`+
      `<tr><td style='color: #000000; font-weight: bold; font-size:18px;' align='center'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;' align='center'>Hora</td></tr>`;

      if(hs.length==0) {
        htmlresult = htmlresult +     
        `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+fe+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+he+`</td></tr>`;
      } else {
        if(he2.length==0) {
          htmlresult = htmlresult +    
          `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+fs+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+hs+`</td></tr>`;                    
        } else {
          if(hs2.length==0) {
            htmlresult = htmlresult +
            `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+fe2+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+he2+`</td></tr>`;
          } else {
            htmlresult = htmlresult +
            `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+fs2+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+hs2+`</td></tr>`;            
          }
        }
      }

      htmlresult = htmlresult +
      `</table>`;

      htmlresult = htmlresult+`</div>`; 

      Swal.fire({
        title: '¿ Registrar Asistencia ?',        
        html: htmlresult,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: '<i class="fa fa-thumbs-down">Cancelar</i>',
        confirmButtonText: 'Si, Registrar!'
      }).then((result) => {      
        if (result.isConfirmed) {           
          Registrar(cedula);
        } else {
          esperar = 0;
        }
      });      
    }
  </script> 
  @endif


  @if ($lineadetiempo==8)
  <script>
    function VentanaPreguntar8(response, cedula_) {
      var cedula = cedula_;
      var fe = moment(response.hora_r, 'DD-MM-YYYY').format('DD-MM-YYYY');
      var he = moment(response.hora_r, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  
      var htmlresult = `<center><div id="fotofuncionario_" style="border:solid; width: 100px; height: 100px;"><img src="`+response.foto+`" style="width: 100px; height: 100px; border:solid 1px;"/></div></center>`;
      htmlresult = htmlresult + `<div id="datosfuncionario_" style="width: 100%;">`+
      `<table style="width:100%;">`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.cedula+`</td></tr>`+
      `<tr><td style="font-weight: bold; color:#3f48cc;" align="center">`+response.nombre+`&nbsp`+response.apellido+`</td></tr>`+
      `</table>`+
      `</div>`+
      `<div id="asistenciafuncionario_" align="left">`+
      `<table style="width:100%;">`+
      `<tr><td style='color: #000000; font-weight: bold; font-size:18px;' align='center'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;' align='center'>Hora</td></tr>`;

      htmlresult = htmlresult +     
      `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+fe+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;' align='center'>`+he+`</td></tr>`;

      htmlresult = htmlresult +
      `</table>`;

      htmlresult = htmlresult+`</div>`; 

      Swal.fire({
        title: '¿ Registrar Asistencia ?',        
        html: htmlresult,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: '<i class="fa fa-thumbs-down">Cancelar</i>',
        confirmButtonText: 'Si, Registrar!'
      }).then((result) => {      
        if (result.isConfirmed) {           
          Registrar8(cedula);
        } else {
          esperar = 0;
        }
      });      
    }
  </script> 
  @endif 


  @if ($lineadetiempo==4)
  <script>
    function Registrar(cedula_) {
      var comparar = cedula_;
      if (ultimacedula != comparar) {
        var route = "{{ route('registrardatosfuncionario') }}";
        var token = $("#_token1").val();
        var cedula = cedula_;        
        $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {cedula: cedula},
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {              
              if ( (response!="Error") && (response!="registrado") && (response!="No tiene designación activa")) {                
                  if (typeof response.id_asistencia) {
                    $('#botonobservacion').fadeIn();
                    $('#botoncontrolingresos').fadeIn();
                    $('#botonnotificacion').fadeIn();
                    $('#botonFiscalizacion').fadeIn();
                    $('#botoncompra').fadeIn();
                    $('#botonpermiso').fadeIn();
                    $('#botondivulgacion').fadeIn();
                    $('#botonvdf').fadeIn();

                    $('#id_asistencia').val(response.id_asistencia);
                  }

                  ultimacedula = cedula_;                  

                  var fe = moment(response.hora_e, 'DD-MM-YYYY').format('DD-MM-YYYY');
                  var he = moment(response.hora_e, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');

                  if (response.hora_s != "") {
                    var fs = moment(response.hora_s, 'DD-MM-YYYY').format('DD-MM-YYYY');
                    var hs = moment(response.hora_s, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  } else {
                    var fs = "";
                    var hs = "";
                  }

                  if (response.hora_e2 != "") {
                    var fe2 = moment(response.hora_e2, 'DD-MM-YYYY').format('DD-MM-YYYY');
                    var he2 = moment(response.hora_e2, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  } else {
                    var fe2 = "";
                    var he2 = "";
                  }

                  if (response.hora_s2 != "") {
                    var fs2 = moment(response.hora_s2, 'DD-MM-YYYY').format('DD-MM-YYYY');
                    var hs2 = moment(response.hora_s2, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  } else {
                    var fs2 = "";
                    var hs2 = "";
                  }
                  
                  $('#fotofuncionario').remove();            
                  $('#datosfuncionario').remove();            
                  $('#asistenciafuncionario').remove();            

                  $("#resultfoto").append(`<div id="fotofuncionario" style="width: 100px; height: 100px;"><img src="`+response.foto+`" style="width: 100px; height: 100px; border:solid 1px;"/></div>`);
                  $("#resultdatos").append(`<div id="datosfuncionario" align="left">`+
                    `<table>`+
                    `<tr><td>Cédula: </td><td>`+response.cedula+`</td></tr>`+
                    `<tr><td>Nombre: </td><td>`+response.nombre+`</td></tr>`+
                    `<tr><td>Apellido: </td><td>`+response.apellido+`</td></tr>`+                    
                    `</table>`+
                    `</div>`);
                  $("#resultasistencia").append(`<div id="asistenciafuncionario" align="left">`+
                    `<table style="width:100%;">`+
                    `<tr><td style='color: #000000; font-weight: bold; font-size:18px;'></td><td style='color: #000000; font-weight: bold; font-size:18px;'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;'>Hora</td></tr>`+
                    `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'><i class='fas fa-calendar-check'></i></td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+fe+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+he+`</td></tr>`+
                    `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'><i class='fas fa-calendar-check'></i></td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+fs+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+hs+`</td></tr>`+                    
                    `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'><i class='fas fa-calendar-check'></i></td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+fe2+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+he2+`</td></tr>`+
                    `<tr><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'><i class='fas fa-calendar-check'></i></td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+fs2+`</td><td style='background-color: #f2d7d5; color: #000000; font-weight: bold; font-size:18px;'>`+hs2+`</td></tr>`+                    
                    `</table>`+
                    `</div>`);                    
              }
              if (response=="registrado") {
                var ultimacedula = "";
                MostrarMensajeGeneral("Ya está registrado");
              }
              if (response=="No tiene designación activa") {
                var ultimacedula = "";
                MostrarErrorGeneral("No tiene designación activa");
              }
              esperar = 0;                            
          }  
        }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }          
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          } 
          esperar = 0;
        });            
      } else {                
        MostrarMensajeGeneral("Ya está registrado");
        esperar = 0;        
      }
    } 
  </script> 
  @endif

  @if ($lineadetiempo==8)
  <script>
    function Registrar8(cedula_) {
      var comparar = cedula_;
      if (ultimacedula != comparar) {
        var route = "{{ route('registrardatosfuncionario8') }}";
        var token = $("#_token1").val();
        var cedula = cedula_;        
        $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {cedula: cedula},
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {
              if ((response!="Error") && (response!="registrado") && (response!="No tiene designación activa")) {                
                  if (typeof response.id_asistencia) {
                    $('#botonobservacion').fadeIn();
                    $('#botoncontrolingresos').fadeIn();
                    $('#botonnotificacion').fadeIn();
                    $('#botonFiscalizacion').fadeIn();
                    $('#botoncompra').fadeIn();
                    $('#botonpermiso').fadeIn();
                    $('#botondivulgacion').fadeIn();
                    $('#botonvdf').fadeIn();

                    $('#id_asistencia').val(response.id_asistencia);
                  }

                  ultimacedula = cedula_;                  

                  var fe = moment(response.hora_r, 'DD-MM-YYYY').format('DD-MM-YYYY');
                  var he = moment(response.hora_r, 'DD-MM-YYYY hh:mm:ss a').format('hh:mm:ss a');
                  
                  $('#fotofuncionario').remove();            
                  $('#datosfuncionario').remove();            
                  $('#asistenciafuncionario').remove();            

                  $("#resultfoto").append(`<div id="fotofuncionario" style="width: 100px; height: 100px;"><img src="`+response.foto+`" style="width: 100px; height: 100px; border:solid 1px;"/></div>`);
                  $("#resultdatos").append(`<div id="datosfuncionario" align="left">`+
                    `<table>`+
                    `<tr><td>Cédula: </td><td>`+response.cedula+`</td></tr>`+
                    `<tr><td>Nombre: </td><td>`+response.nombre+`</td></tr>`+
                    `<tr><td>Apellido: </td><td>`+response.apellido+`</td></tr>`+                    
                    `</table>`+
                    `</div>`);
                  $("#resultasistencia").append(`<div id="asistenciafuncionario" align="left">`+
                    `<table style="width:100%;">`+
                    `<tr><td style='color: #000000; font-weight: bold; font-size:18px;'></td><td style='color: #000000; font-weight: bold; font-size:18px;'>Fecha</td><td style='color: #000000; font-weight: bold; font-size:18px;'>Hora</td></tr>`+
                    `<tr><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'><i class='fas fa-calendar-check'></i></td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+fe+`</td><td style='background-color: #d7bde2; color: #000000; font-weight: bold; font-size:18px;'>`+he+`</td></tr>`+
                    `</table>`+
                    `</div>`);                    
              }
              if (response=="registrado") {
                var ultimacedula = "";
                MostrarMensajeGeneral("Ya está registrado");
              }
              if (response=="No tiene designación activa") {
                var ultimacedula = "";
                MostrarErrorGeneral("No tiene designación activa");
              } 
              esperar = 0;             
          }  
        }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }          
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          } 
          esperar = 0;
        });            
      } else {                
        MostrarMensajeGeneral("Ya está registrado");
        esperar = 0;
      }
    } 
  </script> 
  @endif  

  <script>
  function GuardarObservacion(sw) {
      var id_asistencia = $('#id_asistencia').val();
      var observacion = $('#observacion').val();
      var route = "{{ route('goasistencia') }}";
      var token = $("#_token2").val();
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {
            id: id_asistencia,            
            observacion: observacion
          },
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {                        
              if (response=="Ok") {                
                if (sw==0) {
                  document.getElementById('mensaje-danger').style.display = 'none'; 
                  $('#mensaje-success').fadeIn();
                } else {
                  document.getElementById('botonobservacion').style.display = 'none';       
                  document.getElementById('botoncontrolingresos').style.display = 'none'; 
                  document.getElementById('botonnotificacion').style.display = 'none'; 
                  document.getElementById('botonFiscalizacion').style.display = 'none'; 
                  document.getElementById('botoncompra').style.display = 'none'; 
                  document.getElementById('botonpermiso').style.display = 'none'; 
                  document.getElementById('botondivulgacion').style.display = 'none';       
                  document.getElementById('botonvdf').style.display = 'none';       
                  
                  $('#mensaje-observacion').fadeIn();
                }
              } else {
                if (sw==0) {
                  document.getElementById('mensaje-success').style.display = 'none'; 
                  $('#mensaje-danger').fadeIn();
                } else {
                  document.getElementById('mensaje-observacion').style.display = 'none'; 
                }
              }
          }  
      }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }             
      }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          }                  
      });            
  }  
  </script>

  <script>
    function GuardarOtraObservacion(observacion) {
      $('#observacion').val(observacion);
      GuardarObservacion(1);      
    }          
  </script>
@stop  
 