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
            Acción
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">            
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="clickImprimir();"><i class="fa fa-print"></i> Imprimir listado</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="clickExportarPdf();"><i class="fas fa-file-pdf"></i> Exportar Listado Pdf</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="clickExportarExcel();"><i class="fas fa-file-excel"></i> Exportar listado a Excel</a>
              @if (Auth::user()->hasAnyPermission('13_Monitorear'))
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{route('monitorarea.index')}}"><i class="fa fa-desktop"></i> Actividades</a>
              @endif       
            </div>
          </div>
        </div>
        Area
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

          @endif

        </div>
        <!-- /.card-body -->
      </div>

    <div class="row">
      <div class="col-xs-6 col-sm-6 col-md-6" align="left">
          <div class="input-group mb-10">
            <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ $busqueda }}">
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
          @if (Auth::user()->hasAnyPermission('3_Crear_area'))
          <a class="btn btn-success" href="{{route('area.create')}}">Nueva área</a>
          @endif        
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
                  <th>Descripción</th>
                  <th>Ubicación</th>
                  <th>Coordinador/Responsable</th>
                  <th>Teléfono</th>
                  <th>División</th>
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

    @if (Auth::user()->hasAnyPermission('3_Crear_area'))
    <br>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12" align="center">
        <a class="btn btn-success" href="{{route('area.create')}}">Nueva área</a>                                
      </div>      
    </div> 
    @endif

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Datos del Area</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group" align="left">       
                    <div id="resultdatos">
                      <div id="datosarea">

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
</section> 

{!!Form::open(array('id' => 'frmver', 'name' => 'frmver' ))!!}
  <input type="hidden" name="_tokenver" id="tokenver" value="{{ csrf_token() }}">
{!!Form::close()!!}                                                                                                                              
@endsection 

@section("page_js")

@endsection 

@section("scripts")

  {!! Html::script('/js/in/confirmaraccionv2.js') !!} 

  <script>    
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

  @include('area.jsindex')  

  <script>
    $(document).ready(function(){ 
        $("#id_dep").change(event => {           
          BuscarDivision();            
        });
        $("#id_divi").change(event => {           
          Refrescar();
        });                
    });        
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
    function BuscarDivision() {        
        var route = "{{ route('buscarladivision') }}";
        var token = $("#token").val();
        var iddep = $('#id_dep').val();
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {
                            b: consulta4,
                            id_dep : iddep,
                        },
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Error'){
                                $("#id_divi").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_divi").append(`<option value='${element.id}'>${element.descripcion}</option>`);
                                    });                        
                                    $('#id_divi').select2({         
                                        width: '100%',
                                        placeholder: "Escribir [Enter Busca]",
                                    });
                                } else {

                                }
                                Refrescar();                                
                            } else {
                                if (response=='Error') {
                                    
                                }
                            }                                                                                                                                                                                                                                                                
                        }                
                    }).done(function(data, textStatus, jqXHR) {
                        peticion4=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion4=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });        
    }    
  </script>  

<script>
    consulta4 = "";
    $(document).on('keyup', function (e) {
        evt = e ? e : event;
        tcl = (window.Event) ? evt.which : evt.keyCode;          
        if (tcl == 13) {            
            $("#select2-id_divi-container").css('color','green');
            if ($("#select2-id_divi-results").length==0) {         
                consulta4 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta4 = "*";
                } else {
                    consulta4 = $('.select2-search__field').val();
                }
            }

            if (consulta4.length>0) {
                if (peticion4==0) {
                    peticion4 = 1;
                    BuscarDivision();    
                }         
            }                                    
        }        
    });
</script>  
@stop
