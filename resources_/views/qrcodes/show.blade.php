@extends('layouts.app')

@section('css')
    {!! Html::style('/css/in/estilotablaseniat.min.css') !!}
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
              <a class="dropdown-item" href="#" onclick="ImprimirAsistenciaGeneral();"><i class="fa fa-print"></i> Imprimir asistencia general</a>              
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="clickImprimir();"><i class="fa fa-print"></i> Imprimir asistencia</a>
              <a class="dropdown-item" href="#" onclick="clickExportarPdf();"><i class="fas fa-file-pdf"></i> Exportar asistencia Pdf</a>
              <a class="dropdown-item" href="#" onclick="clickExportarExcel();"><i class="fas fa-file-excel"></i> Exportar asistencia a Excel</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalfecha"><i class="fa fa-calendar-check"></i> Consultar período</a>
              @if (Auth::user()->hasAnyPermission('12_Registrar_Permiso_Asistencia'))              
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalpermiso"><i class="fa fa-bookmark"></i> Registrar permiso</a>              
              @endif

              @if (Auth::user()->hasAnyPermission('13_Monitorear'))
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#"><i class="fa fa-desktop"></i> Actividades</a>              
              @endif                      
            </div>
          </div>
        </div>
        Asistencias

        @if (($desde== "") && ($hasta == ""))
        <div class="btn-group" style="font-size: 12pt;" align="center">          
          &nbsp;&nbsp;Hoy
        </div>         
        @else        
        <div class="btn-group" style="font-size: 12pt;" align="center">          
          &nbsp;&nbsp;Del {{date("d/m/Y",strtotime($desde))}} al {{date("d/m/Y",strtotime($hasta))}}
        </div>         
        @endif

        <div class="btn-group" style="float:right;">                    
          <div class="small-box bg-warning">
            <div class="inner">
              <h5>{{ count($objasistencia) }}&nbsp;<i class="fas fa-user"></i></h5>                            
            </div>
          </div>                      
        </div> 
      </h7>                        
    </div>        
        
    @include('alert.succes')
    @include('alert.errors')
    <br><br>
    {!!Form::open(array('name' => 'form', 'route' => array('buscarasistencia'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input name='sw' id='sw' type='hidden' value='0'>  

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
          @else
          <div class="row"> 
            <div class="col-xs-6 col-sm-6 col-md-6">
              <div id="edepen"> 
                <div class="form-group">
                  <label for="label">Dependencia</label>
                  {!!Form::select('id_dep', $objdependencia, $id_temp, array('id'=>'id_dep' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                </div>
              </div>          
            </div>            

            <div class="col-xs-6 col-sm-6 col-md-6">
              <div id="edivi"> 
                <div class="form-group">
                  <label for="label">División</label>
                  {!!Form::select('id_div', $objdivision, $id_tempdiv, array('id'=>'id_div' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                </div>
              </div>          
            </div>                  
          </div>
          @endif
        </div>
        <!-- /.card-body -->
      </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12" align="left">
        <div class="input-group mb-10">

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalfecha" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Consultar el período</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group" align="left">       
                    <div id="resultfecha">
                      <div id="datosfecha">

                        <center>
                        
                        <label for="" style="color:#">Del</label>&nbsp;&nbsp;&nbsp;
                        {!!Form::date('desde', $desde, array('id' => 'desde', 'class' => 'input-sm col-md-4 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_cese.focus();}'))!!}                                                                                                                                                                                                   
                        
                        &nbsp;&nbsp;&nbsp;
                        
                        <label for="">al</label>&nbsp;&nbsp;&nbsp;
                        {!!Form::date('hasta', $hasta, array('id' => 'hasta', 'class' => 'input-sm col-md-4 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_cese.focus();}'))!!}                                                                                                                                                                                                   
                        
                        </center>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success" data-dismiss="modal" onclick="document.form.submit();">Consultar</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
          </div>
        </div>
      </div>
    </div>                       

            <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ $busqueda }}">
            <div class="input-group-prepend">
              <button type="button" class="btn btn-info btn-flat" onclick="document.form.submit();"><i class="fa fa-search"></i> Buscar</button>
            </div>            
            <!-- /btn-group -->
          </div>        
      </div>
    </div>
    {!!Form::close()!!}

    <br>
    <div class="row" style="background:#ffffff;">
      <div class="col" align="left">
            <!-- Lista -->
            @if (count($objasistencia)>0)
              <div class="table-responsive" align = "left" style="width: 100%"> 
                <!-- <table id="tablaordenar" class="table table-hover table-striped table-bordered cell-border"> -->  <!--table-condensed-->
                <table id="tablaordenar" class="redTable table table-active table-hover table-striped table-bordered cell-border table-sm nowrap" style="width: 100%">
                <thead>
                  <tr>
                  <th>Más</th>                  
                  <th>Fecha</th>                  
                  <th>Hora/Entrada</th>
                  <th>Hora/Salida</th>
                  <th>Hora/Entrada</th>
                  <th>Hora/Salida</th>
                  <th>Funcionario</th>
                  <th>Cédula</th>
                  <th>Observación</th>
                  </tr>
                </thead>
               
                <tbody>

                  @foreach($objasistencia as $asistencia)

                  <tr>

                  <td>
                    @if (Arr::get($asistencia,'hora_e3') != null)
                    <a class="fa fa-calendar-plus" title="Mostrar más registros de asistencia" data-toggle="modal" data-target="#modalmostrar" onclick="MasAsistencia('{{$objfechacarbon->versolofecha_a_str(Arr::get($asistencia,'hora_e'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e3'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s3'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e4'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s4'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e5'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s5'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e6'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s6'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e7'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s7'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e8'))}}','{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s8'))}}');"></a>
                    @endif
                  </td>                  
                  <td class="col-sm-1 text-left">{{date("d/m/Y",strtotime(Arr::get($asistencia,'hora_e')))}}</td>                  
                  <td class="col-sm-1 text-left" style="{{ $objfechacarbon->EntroAlas8(Arr::get($asistencia,'hora_e'),10)}}">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e'))}}</td>

                  <td class="col-sm-1 text-left" style="{{ $objfechacarbon->EoS(Arr::get($asistencia,'hora_e'),Arr::get($asistencia,'hora_s'),Arr::get($asistencia,'hora_e2'),Arr::get($asistencia,'hora_s2'),Arr::get($asistencia,'hora_e3'),Arr::get($asistencia,'hora_s3'),Arr::get($asistencia,'hora_e4'),Arr::get($asistencia,'hora_s4'),Arr::get($asistencia,'hora_e5'),Arr::get($asistencia,'hora_s5'),Arr::get($asistencia,'hora_e6'),Arr::get($asistencia,'hora_s6'),Arr::get($asistencia,'hora_e7'),Arr::get($asistencia,'hora_s7'),Arr::get($asistencia,'hora_e8'),Arr::get($asistencia,'hora_s8'),1,$horaactual,10) }}">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s'))}}</td>
                  <td class="col-sm-1 text-left" style="{{ $objfechacarbon->EoS(Arr::get($asistencia,'hora_e'),Arr::get($asistencia,'hora_s'),Arr::get($asistencia,'hora_e2'),Arr::get($asistencia,'hora_s2'),Arr::get($asistencia,'hora_e3'),Arr::get($asistencia,'hora_s3'),Arr::get($asistencia,'hora_e4'),Arr::get($asistencia,'hora_s4'),Arr::get($asistencia,'hora_e5'),Arr::get($asistencia,'hora_s5'),Arr::get($asistencia,'hora_e6'),Arr::get($asistencia,'hora_s6'),Arr::get($asistencia,'hora_e7'),Arr::get($asistencia,'hora_s7'),Arr::get($asistencia,'hora_e8'),Arr::get($asistencia,'hora_s8'),2,$horaactual,10) }}">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e2'))}}</td>
                  <td class="col-sm-1 text-left" style="{{ $objfechacarbon->EoS(Arr::get($asistencia,'hora_e'),Arr::get($asistencia,'hora_s'),Arr::get($asistencia,'hora_e2'),Arr::get($asistencia,'hora_s2'),Arr::get($asistencia,'hora_e3'),Arr::get($asistencia,'hora_s3'),Arr::get($asistencia,'hora_e4'),Arr::get($asistencia,'hora_s4'),Arr::get($asistencia,'hora_e5'),Arr::get($asistencia,'hora_s5'),Arr::get($asistencia,'hora_e6'),Arr::get($asistencia,'hora_s6'),Arr::get($asistencia,'hora_e7'),Arr::get($asistencia,'hora_s7'),Arr::get($asistencia,'hora_e8'),Arr::get($asistencia,'hora_s8'),3,$horaactual,10) }}">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s2'))}}</td>                  

                  <td class="col-sm-2 text-left">{{Arr::get($asistencia,'nombre')}}&nbsp;{{Arr::get($asistencia,'apellido')}}</td>
                  <td class="col-sm-1 text-left">{{Arr::get($asistencia,'cedula')}}</td>
                  <td class="col-sm-5 text-left"><a class="dropdown-item" href="#" title="Actualizar observación" onclick="IdSeleccionado({{Arr::get($asistencia,'id')}});" data-toggle="modal" data-target="#modalobservacion"><i class="fas fa-comment-dots"></i>&nbsp;{{Arr::get($asistencia,'observacion')}}</a></td>
                  </tr>

                  @endforeach
                
                </tbody>
                </table>          
            </div>      

            @else
              <center><H3>Registro no encontrado</H3></center>
            @endif
            <!-- Fin de la lista -->

      </div>      
    </div>

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Más registro de asistencia</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group" align="left">       
                    <div id="resultasistencia">
                      <div id="datosasistencia">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
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
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
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
              <button type="button" class="btn btn-primary"  onclick="GuardarObservacion();">Guardar</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalpermiso" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Registrar permiso</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="mensaje-success2" class="alert alert-success alert-dismissible" role="alert" style="display:none">
              <strong>Permiso registrado con éxito</strong>    
              </div>                                                   
              <div id="mensaje-danger2" class="alert alert-danger alert-dismissible" role="alert" style="display:none">
              <strong>No se pudo procesar la información</strong>    
              </div>                                                   

              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group" align="left">       
                    <div id="resultpermiso">
                      <div id="datospermiso">
                          Funcionario:
                          {!!Form::open(array('name' => 'formpermiso', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
                            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            <div id="efunc"> 
                            <div class="form-group">                                
                                {!!Form::select('id_func', $objfuncionarios, 'Escribir [Enter Busca]', array('id'=>'id_func' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;'))!!}
                            </div>
                            </div>          
                          {!!Form::close()!!}                                                                                         
                          <br>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary"  onclick="GuardarPermiso();">Registrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>                        
</section> 


  {!!Form::open(array('name' => 'formgeneral', 'route' => array('imprimirasistencia'), 'method' => 'post', 'target'=>'_blank', 'files' => true, 'enctype' => 'multipart/form-data'))!!}   
    <input name='idesde' id='idesde' type='hidden' value=''>  
    <input name='ihasta' id='ihasta' type='hidden' value=''>  
    <input name='id_dep_' id='id_dep_' type='hidden' value=''>  
    <input name='id_div_' id='id_div_' type='hidden' value=''>  
    <input type="text" name="ibusqueda" id="ibusqueda" class="form-control" value="">
  {!!Form::close()!!}                                       
@endsection 

@section("page_js")

@endsection 

@section("scripts")
  @include('qrcodes.jsindex')  

  <script>
    var peticion1=0;
    var peticion2=0;
    var peticion3=0;

    $(function () {
        $('#id_dep').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                                                      

        $('#id_div').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });

        $('#id_func').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                             
    });
  </script>    
  @include('qrcodes.jsindex2')  

<script>
function IdSeleccionado(id_asistencia) {
  document.getElementById('mensaje-success').style.display = 'none'; 
  document.getElementById('mensaje-danger').style.display = 'none'; 
  $('#id_asistencia').val(id_asistencia);
}

function GuardarObservacion() {
      var id_asistencia = $('#id_asistencia').val();
      var observacion = $('#observacion').val();
      var route = "{{ route('goasistencia') }}";
      var token = $("#token").val();
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
                document.getElementById('mensaje-danger').style.display = 'none'; 
                $('#mensaje-success').fadeIn();
              } else {
                document.getElementById('mensaje-success').style.display = 'none'; 
                $('#mensaje-danger').fadeIn();
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

function GuardarPermiso() {
      var id_func = $('#id_func').val();
      var route = "{{ route('registrarpermiso') }}";
      var token = $("#token").val();
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {
            id_func: id_func
          },
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {                        
            console.log(response);
              if (response=="Ok") {                
                document.getElementById('mensaje-danger2').style.display = 'none'; 
                $('#mensaje-success2').fadeIn();
              } else {
                document.getElementById('mensaje-success2').style.display = 'none'; 
                $('#mensaje-danger2').fadeIn();
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

function ImprimirAsistenciaGeneral() {
 $('#idesde').val($('#desde').val());
 $('#ihasta').val($('#hasta').val());
 if (document.getElementById( "id_dep" )) {
  $('#id_dep_').val($('#id_dep').val());    
 }
 if (document.getElementById( "id_div" )) {
  $('#id_div_').val($('#id_div').val());    
 }
 $('#ibusqueda').val($('#busqueda').val());
 document.formgeneral.submit();
}

function MasAsistencia(fecha,e3,s3,e4,s4,e5,s5,e6,s6,e7,s7,e8,s8) { 
  $('#datosasistencia').remove();            
  var vista = `<div id="datosasistencia" align="left">`+
    `<section class="content">`+    
      `<div class="container-fluid">
        <div class="row">
          <div class="col-md-12">`+

            `<div class="timeline">
              <div class="time-label">
                <span class="bg-dark">`+fecha+`</span>
              </div>`;
    if(e3!="") {
    vista += `<div>
                <i class="fas fa-clock bg-secondary"></i>
                <div class="timeline-item">
                  <span class="time" style="background: #f2d7d5; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Entrada</span>
                  <h3 class="timeline-header" style="background: #f2d7d5;"><a href="#">&nbsp;`+e3+`</a></h3>
                  <span class="time" style="background: #d7bde2; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Salida&nbsp;&nbsp;&nbsp;</span>
                  <h3 class="timeline-header" style="background: #d7bde2;"><a href="#">&nbsp;`+s3+`</a></h3>                  
                </div>
              </div>`;
    }

    if(e4!="") {
    vista += `<div>
                <i class="fas fa-clock bg-secondary"></i>
                <div class="timeline-item">
                  <span class="time" style="background: #f2d7d5; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Entrada</span>
                  <h3 class="timeline-header" style="background: #f2d7d5;"><a href="#">&nbsp;`+e4+`</a></h3>
                  <span class="time" style="background: #d7bde2; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Salida&nbsp;&nbsp;&nbsp;</span>
                  <h3 class="timeline-header" style="background: #d7bde2;"><a href="#">&nbsp;`+s4+`</a></h3>                  
                </div>
              </div>`;
    }

    if(e5!="") {
    vista += `<div>
                <i class="fas fa-clock bg-secondary"></i>
                <div class="timeline-item">
                  <span class="time" style="background: #f2d7d5; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Entrada</span>
                  <h3 class="timeline-header" style="background: #f2d7d5;"><a href="#">&nbsp;`+e5+`</a></h3>
                  <span class="time" style="background: #d7bde2; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Salida&nbsp;&nbsp;&nbsp;</span>
                  <h3 class="timeline-header" style="background: #d7bde2;"><a href="#">&nbsp;`+s5+`</a></h3>                  
                </div>
              </div>`;
    }
    if(e6!="") {
    vista += `<div>
                <i class="fas fa-clock bg-secondary"></i>
                <div class="timeline-item">
                  <span class="time" style="background: #f2d7d5; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Entrada</span>
                  <h3 class="timeline-header" style="background: #f2d7d5;"><a href="#">&nbsp;`+e6+`</a></h3>
                  <span class="time" style="background: #d7bde2; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Salida&nbsp;&nbsp;&nbsp;</span>
                  <h3 class="timeline-header" style="background: #d7bde2;"><a href="#">&nbsp;`+s6+`</a></h3>                  
                </div>
              </div>`;
    }
    if(e7!="") {
    vista += `<div>
                <i class="fas fa-clock bg-secondary"></i>
                <div class="timeline-item">
                  <span class="time" style="background: #f2d7d5; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Entrada</span>
                  <h3 class="timeline-header" style="background: #f2d7d5;"><a href="#">&nbsp;`+e7+`</a></h3>
                  <span class="time" style="background: #d7bde2; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Salida&nbsp;&nbsp;&nbsp;</span>
                  <h3 class="timeline-header" style="background: #d7bde2;"><a href="#">&nbsp;`+s7+`</a></h3>                  
                </div>
              </div>`;
    }
    if(e8!="") {
    vista += `<div>
                <i class="fas fa-clock bg-secondary"></i>
                <div class="timeline-item">
                  <span class="time" style="background: #f2d7d5; color: #5d6d7e;"><i class="fas fa-clock"></i>&nbsp;Entrada</span>
                  <h3 class="timeline-header" style="background: #f2d7d5;"><a href="#">&nbsp;`+e8+`</a></h3>
                  <span class="time"style="background: #d7bde2; color: #5d6d7e;" ><i class="fas fa-clock"></i>&nbsp;Salida&nbsp;&nbsp;&nbsp;</span>
                  <h3 class="timeline-header" style="background: #d7bde2;"><a href="#">&nbsp;`+s8+`</a></h3>                  
                </div>
              </div>                                          
            </div>`;
    }
    
    vista+=`</div>
        </div>
      </div>`+
    `</section></div>`;

    $("#resultasistencia").append(vista);
  }
</script>

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
            document.form.submit();
        });
        $("#id_div").change(event => {           
            document.form.submit();
        });        
    });
  </script>    
@stop
