@extends('layouts.app')

@section('css')
    {!! Html::style('/css/in/estilotablaseniat.min.css') !!}
    <style>
      .logopdf {
        width: 120px;
        height: 30px;
        content:url("{{ asset('img/logo-left.png') }}");        
      }

    .dataTables_wrapper .dataTables_processing {
      position: absolute;
      top: 30%;
      left: 50%;
      width: 30%;
      height: 40px;
      margin-left: -20%;
      margin-top: -25px;
      padding-top: 20px;
      text-align: center;
      font-size: 1.2em;
      background:none;
    } 

            #mapa-container {
                height: 800px;
                max-width: 100%;
                margin: 0 auto;
            }
            .loading {
                margin-top: 10em;
                text-align: center;
                color: gray;
            }

            .chart-outer {
                max-width: 800px;
                margin: 2em auto;
            }

            #container {
                height: 300px;
                margin-top: 2em;
                min-width: 380px;
            }

            .highcharts-data-table table, th, td {
                border: solid 1px;
                border-collapse: collapse;
                border-spacing: 0;
                background: #F2D7D5;
                min-width: 100%;
                margin-top: 10px;
                font-family: sans-serif;
                font-size: 0.9em;
            }

            .colorScroll {              
              scrollbar-color: #C0392B #99A3A4;
            }

    </style>
@stop

@section('content')
  <section class="section">
    <div class="section-header">
      <h7 class="page__heading">
        <div class="btn-group">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Acción
            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">            
              <a class="dropdown-item" href="#" title="Vista actual" onclick="clickExportarPdf();"><i class="fas fa-file-pdf"></i> Exportar listado a Pdf</a>
              <a class="dropdown-item" href="#" title="Vista actual" onclick="clickExportarExcel();"><i class="fas fa-file-excel"></i> Exportar listado a Excel</a>
            </div>
          </div>
        </div>

        <div class="btn-group">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Clasificación
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">            
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio1').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio1" name="r1" value ="Todos" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Todos <label style="font-size: 9px;">(Solo por dependencia)</label></label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio2').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio2" name="r1" value ="Activo" onclick="Refrescar();" checked="">
                  <label style="font-size: 10;font-weight: normal;"  for="radioPrimary1">Activos <label style="font-size: 9px;">(Solo con designación)</label></label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio3').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio3" name="r1" value ="Culminado" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Culminados <label style="font-size: 9px;">(Solo con designación)</label></label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio4').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio4" name="r1" value ="Jubilado" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Jubilados <label style="font-size: 9px;">(Solo con designación)</label></label>
                </div>                                                    
              </a>              
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio5').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio5" name="r1" value ="No asignado" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Sin designación <label style="font-size: 9px;">(Solo por dependencia)</label></label>
                </div>                                    
              </a>                            
            </div>
          </div>
        </div>

        <div class="btn-group">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Sufragio
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">            
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio6').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio6" name="r2" value ="Participo" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Participo</label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio7').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio7" name="r2" value ="No participo" onclick="Refrescar();" checked="">
                  <label style="font-size: 10;font-weight: normal;"  for="radioPrimary1">No Participo</label>
                </div>                                    
              </a>
            </div>
          </div>
        </div>

        Funcionarios Votantes&nbsp;&nbsp;&nbsp;

        <div class="btn-group">
          <a class="fas fa-chart-pie" style="height: 30px;" href="#" data-toggle="modal" data-target="#modalmostrargrafico" title="Gráfico" onclick="Graficar();"></a>        
        </div>

      </h5>                        
    </div> 
   <br>
    @include('alert.succes')
    @include('alert.errors')
    {!!Form::open(array('name' => 'form'))!!}    
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">        

      <div id="a" class="card card-default collapsed-card">
        <a href="#" data-card-widget="collapse">
        <div class="card-header">
          <h5 class="card-title">Más filtros</h5>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-plus-square"></i>
            </button>
          </div>
        </div>
        </a>
        <!-- /.card-header -->
        <div class="card-body" style="background:#F9EBEA; display: none;">

          @if (Auth::user()->hasAnyPermission('Solo_dependencia'))
            <input type="hidden" name="id_dep" id="id_dep" value="{{ Auth::user()->id_dep }}">

          <div class="row"> 
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div id="edepen"> 
                <div class="form-group">
                  <label for="label">División</label>
                  {!!Form::select('id_divi', $objdivision, $id_tempd, array('id'=>'id_divi' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                </div>
              </div>          
            </div>            
          </div>  

          <div class="row"> 
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div id="edepen"> 
                <div class="form-group">
                  <label for="label">Area</label>
                  {!!Form::select('id_area', $objarea, $id_tempa, array('id'=>'id_area' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                </div>
              </div>          
            </div>            
          </div> 
          @else
          <div class="row"> 
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div id="edepen"> 
                <div class="form-group">
                  <label for="label">Dependencia</label>
                  {!!Form::select('id_dep', $objdependencia, $id_temp, array('id'=>'id_dep' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                </div>
              </div>          
            </div>                        
          </div>

          <div class="row"> 
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div id="edepen"> 
                <div class="form-group">
                  <label for="label">División</label>
                  {!!Form::select('id_divi', $objdivision, $id_tempd, array('id'=>'id_divi' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                </div>
              </div>          
            </div>            
          </div>  

          <div class="row"> 
            <div class="col-xs-12 col-sm-12 col-md-12">
              <div id="edepen"> 
                <div class="form-group">
                  <label for="label">Area</label>
                  {!!Form::select('id_area', $objarea, $id_tempa, array('id'=>'id_area' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                </div>
              </div>          
            </div>            
          </div>           
          @endif

        </div>
        <!-- /.card-body -->
      </div>

      <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6" align="left">
          <div class="input-group mb-10">
            <input type="text" name="busqueda" id="busqueda" class="form-control" title="Busqueda por Cédula o Nombre del funcionario" value="{{ $busqueda }}">
            <div class="input-group-prepend">
              <button id="btnbuscar" type="button" class="btn btn-info btn-flat" onclick="Refrescar();"><i class="fa fa-search"></i> Buscar</button>
            </div>
            <!-- /btn-group -->
          </div>        
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1" align="left">
          &nbsp;
        </div>
        <div class="col-xs-5 col-sm-5 col-md-5" align="center">          
        </div>      
      </div>  
    {!!Form::close()!!}    
    <br>

    <div class="row" style="background:#ffffff;">
      <div class="col" align="left">
            <!-- Lista -->
              <div class="table-responsive" align = "left" style="width: 100%"> 
                <!-- <table id="tablaordenar" class="table table-hover table-striped table-bordered cell-border"> -->  <!--table-condensed-->
                <table class="redTable yajra-datatable nowrap" style="width: 100%">
                <thead>
                  <tr>
                  <th></th>                    
                  <th style="font-size:12px;font-weight: bold;">Cédula</th>
                  <th style="font-size:12px;font-weight: bold;">Nombres</th>
                  <th style="font-size:12px;font-weight: bold;">Apellidos</th>                  
                  <th style="font-size:12px;font-weight: bold;">Fec/Nac</th>
                  <th style="font-size:12px;font-weight: bold;">Teléfono</th>                  
                  <th align="center" style="font-size:11px;font-weight: normal;background: #a40808;">Editar</th>
                  <th align="center" style="font-size:11px;font-weight: normal;background: #a40808;">Voto</th>
                  </tr>
                </thead>
               
                <tbody>
                
                </tbody>
                </table>          
            </div>      
            <!-- Fin de la lista -->

      </div>      
    </div>  
    <br>
</section>

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Cetro de votación</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <!-- Inicio foto -->
                  <div class="form-group" align="left">       
                    <label for="">Ubicación</label>
                      <form name="formu" id="formu">
                        <input type="hidden" name="_token1" id="_token1" value="{{ csrf_token() }}">
                        <input type="hidden" name="id_" id="id_" value="">
                        {!!Form::textarea('centrovotacion', null, array('id' => 'centrovotacion', 'maxlength'=>'250', 'rows'=>'5', 'class' => 'input-sm col-md-12 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Ubicación'))!!}                
                      </form>
                  </div>                             
                  <!-- fin foto -->
                </div>
              </div>
              
            </div>
            <div class="modal-footer" style="background-color: #CCCCCC;">
              <button type="button" class="btn btn-success" data-dismiss="modal" onclick="ActualizarCentroVotacion();">Actualizar</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

  {!!Form::open(array('id' => 'frmver', 'name' => 'frmver' ))!!}
  <input type="hidden" name="_tokenver" id="tokenver" value="{{ csrf_token() }}">
  {!!Form::close()!!}                                                                                                                              

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrargrafico" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9999ff; height: 50px;">
              <h7 class="modal-title" id="staticBackdropLabel">Totalización</h7>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <figure class="highcharts-figure">
              <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">                      
                <div id="resultados">                  
                  <div id="datosrecaudacion">

                  </div>                
                </div>                      
                <div id="grafico-container">                  

                </div>                             
              </div>              
              </figure>
            </div>
            <div class="modal-footer">

            </div>
          </div>
        </div>
      </div>
    </div>
@endsection 

@section("page_js")

@endsection 

@section("scripts")
  {!! Html::script('/js/in/confirmaraccionv2.js') !!} 
  {!! Html::script('/js/in/moment.js') !!}      
  {!! Html::script('/js/in/calcularedad.js') !!}      

  @include('votos.jsselect2')

  @if (Auth::user()->hasAnyPermission('Solo_dependencia'))
  @else
    @include('votos.jsselect3')
  @endif

  {!! Html::script('/js/Maps/maps.js/highmaps.js') !!} 
  {!! Html::script('/js/Maps/maps.js/exporting.js') !!} 

  {!! Html::script('/js/Graficos/Librerias-Higthcharts/pluggins/Highcharts_7.0.3/code/modules/data.js') !!} 
  {!! Html::script('/js/Graficos/Librerias-Higthcharts/pluggins/Highcharts_7.0.3/code/modules/export-data.js') !!} 
  {!! Html::script('/js/Graficos/Librerias-Higthcharts/pluggins/Highcharts_7.0.3/code/modules/accessibility.js') !!} 

  @include('votos.grafico1')  


  <script>        
    var peticion1=0;
    var peticion2=0;
    var peticion3=0;
    var peticion4=0;
    var peticion5=0;

    var consulta1="";
    var consulta3="";
    var consulta4="";

    $(function () {
        $('#id_area').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });

        $('#id_divi').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });
    });  
  </script>

  @if (Auth::user()->hasAnyPermission('Solo_dependencia'))
  @else
  <script>    
    $(function () {
        $('#id_dep').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                            
    });    
  </script>
  @endif

  @include('votos.jsindex')  

  <script> 
    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }

    if( typeof MostrarMensajeError !== 'undefined' && jQuery.isFunction( MostrarMensajeError ) ) {
      //Es seguro ejectura la función
      MostrarMensajeError(messageerror);
    }
  </script>  

  <script>
    $(document).ready(function(){ 
        $("#id_dep").change(event => {                     
          BuscarDivision();            
        });
        $("#id_divi").change(event => {           
          BuscarArea();
        });                
        $("#id_area").change(event => {           
          Refrescar();
        });    
    });        
  </script>

  <script>
    function buscarcentrovotacion(id) {           
      var route = "{{ route('vercentrovotacion') }}";
      var token = $("#_token1").val();            
      var ubicacion = $("#centrovotacion").val();            
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {id: id},
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {
              if (response!="Error") {
                $("#id_").val(id);
                $("#centrovotacion").val(response.centrovotacion);
              }else{
                $("#id_").val('');            
                $("#centrovotacion").val('');
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
    function ActualizarCentroVotacion() {           
      var route = "{{ route('actualizarcentrovotacion') }}";
      var token = $("#_token1").val();
      var id = $("#id_").val();            
      var ubicacion = $("#centrovotacion").val();            
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {
            id: id,
            centrovotacion : ubicacion
          },
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {
              if (response!="Error") {
                MostrarMensajeSuccess_('Actualización Exitosa');
              }else{
                MostrarMensajeError_('Error no se pudo actualizar');
              }
            $("#id_").val('');            
            $("#centrovotacion").val('');
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
    function ContarVotos() {           
      var route = "{{ route('contarvotos') }}";
      var token = $("#_token1").val();
      var resp = 'Error';
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          beforeSend: function() {

          },
          error: function(response) {},
          success: function(response) {
              if (response!="Error") {
                console.log(response);

                var ancho_= "800";
                var alto_="700";
                var vr = 1;
                var titulo = "GRTI LOS LLANOS<br>28 de Julio, 2024";

                var tv = response[1].votos+response[2].votos+response[3].votos+response[4].votos+response[5].votos;
                var tp = response[1].total+response[2].total+response[3].total+response[4].total+response[5].votos;

                var votaron = tv;
                var faltanporvotar = tp-tv;

                datac = [
                  { name: 'Votaron', y: 0 },
                  { name: 'Faltan por Votar', y: 0 },
                ];

                datac[0].y = votaron;
                datac[1].y = faltanporvotar;


                datat = [
                  { p: 0.00, np: 0.00 },
                  { p: 0.00, np: 0.00 },
                  { p: 0.00, np: 0.00 },
                  { p: 0.00, np: 0.00 },
                  { p: 0.00, np: 0.00 },
                  { p: 0.00, np: 0.00 }                        
                ];

                datat[1].p = response[1].votos;
                datat[1].np = response[1].total-response[1].votos;

                datat[2].p = response[2].votos;
                datat[2].np = response[2].total-response[2].votos;

                datat[3].p = response[3].votos;
                datat[3].np = response[3].total-response[3].votos;

                datat[4].p = response[4].votos;
                datat[4].np = response[4].total-response[4].votos;

                datat[5].p = response[5].votos;
                datat[5].np = response[5].total-response[5].votos;


                Grafico(datac,datat,ancho_,alto_,titulo,vr);                                 
              }else{
                MostrarMensajeError_('La consulta no se realizó intentelo nuevamente');
                resp = 'Error';
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
      return resp;
    }  
  </script> 
  <script>
    function Graficar() {
      ContarVotos();    
    }
  </script>


  <script>        
    function MostrarMensajeSuccess_(message) {     
        var vistamen = Swal.mixin({
          position: 'center',
          showConfirmButton: true,
          timer: 4000,
          background : '#9999cc',  
        });

        vistamen.fire({
          title: "<center><h5 style='color:black'>Transacción</h5></center>",    
          icon: 'success',
          html: "<h6 style='color:black'>"+message+"</h6>",
          showCloseButton: true,
          confirmButtonText: '<center><i class="fa fa-thumbs-up"></i> Continuar!</center>',
          confirmButtonColor: '#1f13df',
        }).then((result) => {  });
    }
    function MostrarMensajeError_(messageerror) {       
            var vistamen = Swal.mixin({
                position: 'center',
                showConfirmButton: true,
                timer: 4000,
                background : '#9999cc',  
            });

            vistamen.fire({
                title: "<center><h5 style='color:black'>Transacción</h5></center>",    
                icon: 'warning',
                html: "<h6 style='color:black'>"+messageerror+"</h6>",
                showCloseButton: true,
                confirmButtonText: '<center><i class="fa fa-thumbs-up"></i> Continuar!</center>',
                confirmButtonColor: '#1f13df',
            }).then((result) => {  });
        }
  </script>  
@stop
