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
          </h3>          
        </div>

        @if (Auth::user()->hasAnyPermission('20_Generaqr'))
        <div class="form-group">
          <label for="label">Cédula</label>
          {!!Form::text('lacedula', null, array('id' => 'lacedula', 'maxlength'=>'12', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'V-0000000000'))!!}                                                                                    
        </div>

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
                        let config = { fps: 10, qrbox: {width: 250, height: 250}, aspectRatio: 1, rememberLastUsedCamera: false };
                           
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
                </script>                          
              </div>                            

              {{-- ---------------------- --}}                                     
              <div class="d-md-flex justify-content-md-end">
                <form action="#" method="POST" ENCTYPE="multipart/form-data" file=true>
                  <input type="hidden" name="_token1" id="_token1" value="{{ csrf_token() }}">
                    <input value="Buscar" type="button" name="" id="boton" class="btn btn-danger" hidden onClick="Procesar();">
                    <input type="text" name="scan" id="busqueda" class="form-control" readonly hidden>
                </form>
              </div>                                                                                                                                                                               
            </div>
          </div>          
        </div>

        <div id="mensaje-success1" class="text-center" role="alert" style="color:#2980B9;display:none">
          <strong>Se agrego nuevo QR</strong>    
        </div>
        <div id="mensaje-danger1" class="text-center" role="alert" style="color:red;display:none">
          <strong>Procedimiento cancelado</strong>    
        </div>                                                   

        @endif              
      </section>
    </div>


    <div class="col-xs-6 col-sm-6 col-md-6">


    </diV>
  </div>

@endsection

@section("scripts")
  <!-- Libreria para la camara lectora del QR -->
  {!!Html::script('/AdminLTE/scan-qrcode/minified/html5-qrcode.min_.js')!!}
  
  {!! Html::script('/js/in/moment.js') !!}      

  @include('alert.general')  

@if (!(Auth::user()->hasAnyPermission('20_Generaqr')))              
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
    var ultimacedula = "";
    var peticion1 = 0;
    let timerId = setTimeout(() => iniciar(), 300000);

  </script>


  <script>
    function Procesar() {
      RegistrarNuevoCarnet();
    }
  </script>

<script>        
    function RegistrarNuevoCarnet() { 
        document.getElementById('mensaje-danger1').style.display = 'none'; 
        document.getElementById('mensaje-success1').style.display = 'none'; 

        var cedula = $("#lacedula").val();
        var nuevocodigo = $("#busqueda").val();

        var route = "{{ route('registrarnuevocarnet') }}";
        var token = $("#_token1").val();
      if (peticion1 == 0)
        peticion1 = 1;
        $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {
            cedula: cedula,
            nuevoqr: nuevocodigo
          },
          beforeSend: function() {            
          },
          error: function(response) {},
          success: function(response) {              
              if (response!="Error") {                
                document.getElementById('mensaje-danger1').style.display = 'none'; 
                document.getElementById('mensaje-success1').style.display = '';                    
              }else{
                document.getElementById('mensaje-danger1').style.display = ''; 
                document.getElementById('mensaje-success1').style.display = 'none';                                    
              }

              peticion1=0;
          }  
        }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }
          peticion1=0;          
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          }
          peticion1=0;                  
        });            
    }  
  </script>

@stop  
 