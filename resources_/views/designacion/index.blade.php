@extends('layouts.app')

@section('css')
    {!! Html::style('/css/in/estilotablaseniat.min.css') !!}
    <style>
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
    </style>
@stop

@section('content')
  <section class="section">
    <div class="section-header">
      <h7 class="page__heading">
        <div class="btn-group">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Registro
            </button>            
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              @if (Auth::user()->hasAnyPermission('9_Crear_designacion'))
              <a class="dropdown-item" href="#" onclick="CrearCargo();"><i class="far fa-id-badge"></i> Crear cargo</a>              
              @endif
              @if (Auth::user()->hasAnyPermission('9_Crear_designacion'))
              <a class="dropdown-item" href="#" onclick="EditarCargo();"><i class="fa fa-highlighter"></i> Editar cargo</a>              
              @endif
              @if (Auth::user()->hasAnyPermission('9_Ver_designacion'))
              <a class="dropdown-item" href="#" onclick="VerCargo();" data-toggle="modal" data-target="#modalmostrar2"><i class="fa fa-street-view"></i> Ver cargo</a>              
              @endif
              <div class="dropdown-divider"></div>
              @if (Auth::user()->hasAnyPermission('9_Ver_designacion'))
              <a class="dropdown-item" href="#" onclick="VerDesignacionSeleccionada();" data-toggle="modal" data-target="#modalmostrar"><i class="fa fa-eye"></i> Ver designación</a>              
              @endif
              <div class="dropdown-divider"></div>
              @if (Auth::user()->hasAnyPermission('9_Imprimir_designacion_doc'))              
              <a class="dropdown-item" href="#" onclick="document.getElementById('formdocumento').submit();"><i class="fa fa-scroll"></i> Imprimir documento</a>
              @endif
            </div>
          </div>          
          &nbsp;
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Acción
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="#" onclick="clickImprimir();"><i class="fa fa-print"></i> Imprimir listado</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="clickExportarPdf();"><i class="fas fa-file-pdf"></i> Exportar Listado Pdf</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="clickExportarExcel();"><i class="fas fa-file-excel"></i> Exportar listado a Excel</a>

              @if (Auth::user()->hasAnyPermission('13_Monitorear'))
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{route('monitordesignacion.index')}}"><i class="fa fa-desktop"></i> Actividades</a>
              @endif        
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
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Todos</label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio2').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio2" name="r1" value ="Activo" onclick="Refrescar();" checked="">
                  <label style="font-size: 10;font-weight: normal;"  for="radioPrimary1">Activos</label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio3').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio3" name="r1" value ="Culminado" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Culminados</label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio4').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio4" name="r1" value ="Culminado" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Jubilados</label>
                </div>                                    
              </a>              
            </div>
          </div>
        </div>               
        Designaciones
      </h7>                        
    </div>        
        
    @include('alert.succes')
    @include('alert.errors')
    <br>
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
          @endif

        </div>
        <!-- /.card-body -->
      </div>

      <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6" align="left">
          <div class="input-group mb-10">
            <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ $busqueda }}">
            <div class="input-group-prepend">
              <button type="button" class="btn btn-info btn-flat" onclick="Refrescar();"><i class="fa fa-search"></i> Buscar</button>
            </div>            
            <!-- /btn-group -->
          </div>        
      {!!Form::close()!!}
      </div>
      <div class="col-xs-1 col-sm-1 col-md-1" align="left">
        &nbsp;
      </div>
      <div class="col-xs-5 col-sm-5 col-md-5" align="center">
          @if (Auth::user()->hasAnyPermission('9_Crear_designacion'))
          <a class="btn btn-success" href="{{route('designacion.create')}}">Nueva designacion</a>
          @endif
      </div>      
    </div>  
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
                  <th>Número</th>
                  <th>Fecha</th>
                  <th>Estaus</th>
                  <th>Funcionario</th>
                  <th>Dependencia</th>
                  <th>Región</th>
                  <th>Ubicación</th>
                  <th>Area</th>
                  <th align="center">Mostrar</th>                 
                  <th align="center">Editar</th>
                  <th align="center">Borrar</th>
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

    @if (Auth::user()->hasAnyPermission('9_Crear_designacion'))
    <br>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12" align="center">
          <a class="btn btn-success" href="{{route('designacion.create')}}">Nueva designación</a> 
      </div>      
    </div> 
    @endif

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Datos de la Designacion</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group" align="left">       
                    <div id="resultdatos">
                      <div id="datosdesignacion">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Continuar</button>
            </div>
          </div>
        </div>
      </div>
    </div>            

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrar2" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Datos del cargo</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group" align="left">       
                    <div id="resultdatos2">
                      <div id="datoscargo2">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Continuar</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="Confirma_();">Borrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>    
</section>

{!!Form::open(array('id' => 'formdocumento', 'name' => 'formdocumento', 'route' => array('imprimirdesignacion'), 'method' => 'post', 'target'=>'_blank' ))!!}
<input name='idcheck' id='idcheck' type='hidden' value='0'>
{!!Form::close()!!}

{!!Form::open(array('id' => 'frmver', 'name' => 'frmver' ))!!}
  <input type="hidden" name="_tokenver" id="tokenver" value="{{ csrf_token() }}">
{!!Form::close()!!}                                                                                                                              
@endsection 

@section("page_js")

@endsection 

@section("scripts")

  {!! Html::script('/js/in/confirmaraccionv2.js') !!} 
  {!! Html::script('/js/in/moment.js') !!}      

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
  
  @include('designacion.jsindex')  

  <script>
  //if (localStorage.getItem('keyseleccionado')>0) {
  //  var seleccionactual = localStorage.getItem('keyseleccionado');
  //} else {
  //  var seleccionactual = 0;
  //}

  function Seleccionar(id) {
    var seleccionactual = $('#idcheck').val();
    if (seleccionactual==0) {
      seleccionactual = $('#seleccionado'+id).val();      
    } else {
      if (seleccionactual == $('#seleccionado'+id).val()) {
        seleccionactual = 0;                
      } else {
        document.getElementById('seleccionado'+seleccionactual).checked = false;
        seleccionactual = $('#seleccionado'+id).val();
      }
    }     
    $('#idcheck').val(seleccionactual);


    /*
    if (seleccionactual==0) {
      seleccionactual = $('#seleccionado'+id).val();
    } else {
      if (seleccionactual == $('#seleccionado'+id).val()) {
        seleccionactual = 0;                
      } else {
        document.getElementById('seleccionado'+seleccionactual).checked = false;
        seleccionactual = $('#seleccionado'+id).val();
      }
    }     
    localStorage.setItem('keyseleccionado', seleccionactual);
    */
  }

  function CrearCargo() {
    var seleccionactual = $('#idcheck').val();
    document.location.href="{!!URL::to('crear/"+seleccionactual+"/cargofuncionario')!!}";
  }
  function EditarCargo() {
    var seleccionactual = $('#idcheck').val();
    document.location.href="{!!URL::to('editar/"+seleccionactual+"/cargofuncionario')!!}";
  }  
  </script>

  <script>  
    function VerDesignacionSeleccionada() {
      var id_desig = $('#idcheck').val();
      if (id_desig > 0) {
        buscardesignacion(id_desig);
      }      
    }

    function VerCargo() {
      var id_desig = $('#idcheck').val();
      var id = $('#idcheck').val();
      var route = "{{ route('vercargofuncionario', ['id'=>'__id_desig']) }}";
      route = route.replace('__id_desig', id_desig);
      var token = $("#token"+id).val();
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "GET",
          dataType: 'json',
          data: {id: id},
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {
            console.log(response);
              $('#datoscargo2').remove();            
              if (response!="Error") {                  
                  var fecha_ex = moment(response.fecha_ex, 'YYYY-MM-DD').format('DD-MM-YYYY');
                  var fecha_inic = moment(response.fecha_inic, 'YYYY-MM-DD').format('DD-MM-YYYY');

                  if (response.fecha_culm==null) {                     
                    var fecha_culm = "Activo";
                  } else {
                    var fecha_culm = moment(response.fecha_culm, 'YYYY-MM-DD').format('DD-MM-YYYY');
                  }                  

                  var conten = `<div id="datoscargo2" align="left">`+
                  `<section class="content">
                  <div class="container-fluid">
                  <div class="row">
                  <div class="col-md-12">
                  <div class="timeline">
                  <div class="time-label">
                  <span class="bg-green">Fecha de expedición: `+fecha_ex+`</span>
                  </div>

                  <div>
                  <i class="fas fa-calendar bg-aqua"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Fecha de inicio</a>&nbsp;`+fecha_inic+`</h3>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-calendar bg-aqua"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Fecha de culminación</a>&nbsp;`+fecha_culm+`</h3>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-check bg-purple"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Cargo</a>&nbsp;`+response.sta_cargo+`</h3>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-check bg-purple"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Contrato</a>&nbsp;`+response.sta_contrato+`</h3>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-align-justify bg-yellow"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Observaciones</a></h3>
                  <div class="timeline-body">`+response.observaciones+
                  `</div><div class="timeline-footer">
                  </div>                  
                  </div>
                  </div>`+

                  `<div>
                  <i class="fas fa-list bg-blue"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Funciones asignadas</a></h3>
                  <div class="timeline-body">`;
                  response.listadefunciones.forEach(element => {  
                    conten = conten + `<i class="fas fa-angle-right"></i>` +element.descripcion+`<br>`;
                  });                                         
                  conten = conten + `</div><div class="timeline-footer">
                  </div>                  
                  </div>
                  </div>`

                  +`<div class="time-label">
                  <span>Fecha de culiminación: `+fecha_culm+`</span>
                  </div>`+                                
                  `</div>
                  </div>
                  </div>
                  </div>
                  </section>`+                  

                  `</div>`;

                  $("#resultdatos2").append(conten); 
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
  function BorrarCargo() {    
    var id_designacion = $('#idcheck').val();
    var id = $('#idcheck').val();

    if (id_designacion>0) {
      var route = "{{ route('borrarcargofuncionario', ['id_desig'=>'__id_designacion']) }}";
      route = route.replace('__id_designacion', id_designacion);
      var token = $("#token"+id).val();

      $.ajax({
        url: route,  
        headers: {'X-CSRF-TOKEN': token},      
        type: "GET",
        dataType: 'json',
        data: {id_desig: id_designacion},
        beforeSend: function(){},
        error: function(){},
        afterSend:function(){},
        success: function(response) {
          
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
  }

  function Confirma_() {  
      Swal.fire({
        title: '¿ Desea realmente eliminar este registro ?',
        text: "No podrá revertir esto!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: '<i class="fa fa-thumbs-down">Cancelar</i>',
        confirmButtonText: 'Si, borrarlo!'
      }).then((result) => {      
        if (result.isConfirmed) {           
          BorrarCargo();
        }
      });    
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
            Refrescar();
        });
    });
  </script>    
@stop