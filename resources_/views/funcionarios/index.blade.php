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
      <h5 class="page__heading">
        <div class="btn-group">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Acción
            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">            
              <a class="dropdown-item" href="#" title="Funcionario seleccionado" onclick="clickMostrar();"><i class="far fa-id-card"></i> Mostra</a>              
              <a class="dropdown-item" href="#" title="Funcionario seleccionado" onclick="clickEditar();"><i class="fa fa-edit"></i> Editar</a>              
              <a class="dropdown-item" href="#" title="Funcionario seleccionado" onclick="clickBorrar();"><i class="fa fa-trash"></i> Borrar</a>                            
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" title="Vista actual" onclick="clickImprimir();"><i class="fa fa-print"></i> Imprimir listado</a>              
              <a class="dropdown-item" href="#" title="Vista actual" onclick="ImprimirCargaFamiliar();"><i class="fa fa-print"></i> Imprimir carga familiar</a>
              <a class="dropdown-item" href="#" title="Vista actual" onclick="ImprimirHijos12();"><i class="fa fa-print"></i> Imprimir Hijos hasta 12 años</a>              
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" title="Vista actual" onclick="clickExportarPdf();"><i class="fas fa-file-pdf"></i> Exportar listado a Pdf</a>
              <a class="dropdown-item" href="#" title="Vista actual" onclick="clickExportarExcel();"><i class="fas fa-file-excel"></i> Exportar listado a Excel</a>
              @if (Auth::user()->hasAnyPermission('13_Monitorear'))
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{route('monitor.index')}}" title="Vista actual"><i class="fa fa-desktop"></i> Actividades</a>
              @endif
            </div>
          </div>
        </div>

        <div class="btn-group">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Designación
            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">            
              <a class="dropdown-item" href="#" title="Funionario seleccionado" onclick="clickCrearDesignacion();"><i class="fa fa-plus"></i> Crear designación</a>
              <div class="dropdown-divider"></div>              
              <a class="dropdown-item" href="#" title="Funionario seleccionado" onclick="clickVerDesignacionActual();"><i class="fa fa-paperclip"></i> Ver designación actual</a>
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
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Inactivos</label>
                </div>                                    
              </a>
              <a class="dropdown-item" href="#" onclick="document.getElementById('radio4').checked = true;Refrescar();">
                <div class="icheck-primary d-inline">
                  <input type="radio" id="radio4" name="r1" value ="Jubilado" onclick="Refrescar();">
                  <label style="font-size: 10;font-weight: normal;" for="radioPrimary1">Jubilados</label>
                </div>                                    
              </a>              
            </div>
          </div>
        </div>

        @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios') || Auth::user()->hasAnyPermission('10_Descargar_archivos_funcionarios'))
        <div class="btn-group">
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Adjuntos
            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">            
              <a class="dropdown-item" href="#" title="Funionario seleccionado" onclick="clickExplorarAdjuntos();"><i class="fa fa-folder"></i> Explorar archivos</a>
            </div>
          </div>
        </div>
        @endif

        Funcionarios
      </h5>                        
    </div> 

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
            @if (Auth::user()->hasAnyPermission('10_Crear_funcionarios'))
            <a class="btn btn-success" href="{{route('funcionarios.create')}}">Nuevo Funcionario</a> 
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
                  <th></th>                    
                  <th></th>
                  <th style="font-size:12px;font-weight: bold;">Cédula</th>
                  <th style="font-size:12px;font-weight: bold;">Apellidos</th>
                  <th style="font-size:12px;font-weight: bold;">Nombres</th>
                  <th style="font-size:12px;font-weight: bold;">Fec/Nac</th>
                  <th style="font-size:12px;font-weight: bold;">Teléfono</th>                  
                  <th style="font-size:12px;font-weight: bold;">Ingreso</th> 
                  <th align="center" style="font-size:11px;font-weight: normal;">Mostrar</th>                 
                  <th align="center" style="font-size:11px;font-weight: normal;">Editar</th>
                  <th align="center" style="font-size:11px;font-weight: normal;">Borrar</th>
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
    @if (Auth::user()->hasAnyPermission('10_Crear_funcionarios'))
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12" align="center">
          <a class="btn btn-success" href="{{route('funcionarios.create')}}">Nuevo Funcionario</a> 
      </div>      
    </div> 
    @endif

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9966FF;">
              <h5 class="modal-title" id="staticBackdropLabel">Ficha del Funcionario</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-3 col-sm-3 col-md-3">
                  <!-- Inicio foto -->
                  <div class="form-group" align="left">       
                    {!!Form::label('Foto ')!!}
                    <div id="resultfoto" style="width: 100px; height: 100px;">
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
              
            </div>
            <div class="modal-footer" style="background-color: #CCCCCC;">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrard" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                    <div id="resultdatosd">
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

  {!!Form::open(array('name' => 'formgeneral', 'route' => array('imprimircargafamiliar'), 'method' => 'post', 'target'=>'_blank', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
    <input name='id_dep_' id='id_dep_' type='hidden' value=''>  
    <input type="text" name="ibusqueda" id="ibusqueda" class="form-control" value="">
  {!!Form::close()!!}  
                                       
  {!!Form::open(array('name' => 'formgeneral12', 'route' => array('imprimirhijos12'), 'method' => 'post', 'target'=>'_blank', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
    <input name='id_dep_2' id='id_dep_2' type='hidden' value=''>  
    <input type="text" name="ibusqueda2" id="ibusqueda2" class="form-control" value="">
  {!!Form::close()!!}                                       

  {!!Form::open(array('id' => 'formdocumento', 'name' => 'formdocumento', 'route' => array('imprimirdesignacion'), 'method' => 'post', 'target'=>'_blank' ))!!}
  <input name='idcheck' id='idcheck' type='hidden' value='0'>
  {!!Form::close()!!}

  {!!Form::open(array('id' => 'frmver', 'name' => 'frmver' ))!!}
  <input type="hidden" name="_tokenver" id="tokenver" value="{{ csrf_token() }}">
  {!!Form::close()!!}                                                                                                                              

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios') || Auth::user()->hasAnyPermission('10_Descargar_archivos_funcionarios'))
    @include('funcionarios.adjuntos')
  @endif

@endsection 

@section("page_js")

@endsection 

@section("scripts")

  {!! Html::script('/js/in/confirmaraccionv2.js') !!} 
  {!! Html::script('/js/in/moment.js') !!}      
  {!! Html::script('/js/in/calcularedad.js') !!}      
  <script>
    var permisoa = '<?php echo Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios') ? '1': '0'; ?>';    
    peticiona = 0;
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

  <script> 
    function ImprimirCargaFamiliar() {
      if (document.getElementById( "id_dep" )) {
        $('#id_dep_').val($('#id_dep').val());    
      }
      $('#ibusqueda').val($('#busqueda').val());
      document.formgeneral.submit();
    }
    function ImprimirHijos12() {
      if (document.getElementById( "id_dep" )) {
        $('#id_dep_2').val($('#id_dep').val());    
      }
      $('#ibusqueda2').val($('#busqueda2').val());
      document.formgeneral12.submit();
    }

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
    }     
  </script> 

  @include('funcionarios.jsindex')  

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

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios'))
  <script>
  $(function(){
    $("#formuploadfilea").on("submit", function(e){
      e.preventDefault();
      var f = $(this);
      var formData = new FormData(document.getElementById("formuploadfilea"));
      formData.append("dato", "valor");
       var route =  "{{ route('subirarchivofuncionario') }}"; 
       var token = $("#tokena").val();

        if (peticiona==0) {
          peticiona= 1;        
          $.ajax({              
            url: route,
            headers: {'X-CSRF-TOKEN': token},
            type: "post",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
            processData: false
          }).done(function(res){
            peticiona = 0;
            $("#fileList2").html("<br>Respuesta: " + res);

            if (res == '"Archivo Agregado"'){
              removeAllChilds('cuerpoadjuntos');
              veradjuntos_();      
            }
          }).fail(function( jqXHR, textStatus, errorThrown ) {
            peticiona=0;    
            if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
            }
          });    
        }
    });
  });    
  </script>
  @endif

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios'))
  <script >
  function doClick2() {
    $("#adjuntararchivox").val('');
    $("#adjuntararchivox").empty('');
    $("#mensaje").empty();
    var el = document.getElementById("adjuntararchivox");
    if (el) {
      el.click();        
    }
  }

  function handleFiles2(files) {
    var d = document.getElementById("fileList2");
    if (!files.length) {
      d.innerHTML = "<p>Archivo No Seleccionado!</p>";
    } else {                
      removeAllChilds('fileList2');
      for (var i=0; i < files.length; i++) {          
        var info = document.createElement("span");
        info.innerHTML = "Archivo: "+files[i].name + " Tamaño: " + files[i].size + " bytes";
        d.appendChild(info);
      } 
      $('#adjuntar').css('pointer-events','');
    }
  }  
  </script>
  @endif

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios') || Auth::user()->hasAnyPermission('10_Descargar_archivos_funcionarios'))
  <script>
  function removeAllChilds(a) {
    var a=document.getElementById(a);
    while(a.hasChildNodes())
    a.removeChild(a.firstChild);  
  }  
  </script>
  @endif

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios'))
  <script>
  $(document).ready(function() {   
    $("#adjuntar").click(function(e){
      if ($("#adjuntararchivox").val()==''){

      }else{
        $("#formuploadfilea").submit();
      }      
    });                
  });
  </script>
  @endif

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios') || Auth::user()->hasAnyPermission('10_Descargar_archivos_funcionarios'))
  <script>
  function clickExplorarAdjuntos() {
    var id_func = $('#idcheck').val();
    if (id_func > 0) {      
      $("#dlgModalAdjuntos").modal({backdrop: "static", keyboard: true}); 
      $("#idupload_func").val($('#idcheck').val());
      removeAllChilds('cuerpoadjuntos');
      veradjuntos_();
    }      
  }  
  </script>
  @endif

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios') || Auth::user()->hasAnyPermission('10_Descargar_archivos_funcionarios'))
  <script>  
  function veradjuntos_(){
        var route = "{{ route('verarchivosfuncionario') }}";
        var token = $("#tokena").val();
        var id = $("#idupload_func").val();
        if (peticiona==0) {
          peticiona = 1;
          $.ajax({
            url: route,  
            headers: {'X-CSRF-TOKEN': token},      
            type: "POST",
            dataType: 'json',
            data: {id: id},
            beforeSend: function(){},
            error: function(){},
            afterSend:function(){},
            success: function(response){
              if (response!='Error'){
              if (response.length>0) {                    
                $("#cuerpoadjuntos").append(`<div id='box1' class='table-responsive'></div>`);
                $("#box1").append(`<table id='tabladetallearchivos' class='redTable yajra-datatable nowrap' style='width: 100%'></table>`);
                $("#tabladetallearchivos").append(`<thead><tr><th>Documetos</th><th colspan='2' align='center'><center>Acción</center></th></tr></thead><tbody></tbody>`);
                response.forEach(element => {
                  var tbody = $('#tabladetallearchivos tbody');
                  var fila_nueva = '';                      
                  var rutadescarga = "{{ route('descargachivosfuncionario',['id'=>'__id_adjunto'])  }}";
                  rutadescarga = rutadescarga.replace('__id_adjunto',element.id);
                  fila_nueva = `<tr>
                    <td class='col-sm-9 text-left'>${element.nom_archivo}</td>
                    <td class='col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center'>`;
                    if (permisoa=='1'){
                      fila_nueva =  fila_nueva + `<a class='btn btn-default btn-xs' title='Eliminar archivo' id='btneliminararchivo`+element.id+`' data-id='`+element.id+`' href='#' onclick='confirmaborrararchivo(this)'><i class='fa fa-trash'></i></a>`;
                    }                       
                    fila_nueva =  fila_nueva +`</td>
                    <td class='col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center'>
                      <a href='`+rutadescarga+`' class='btn btn-default btn-xs' title='Descargar archivo'><i class="fa fa-download"></i></a>
                    </td>
                    </tr>`;
                  tbody.append(fila_nueva);
                });
              }
              } else {

              }                                                                                                                                                                             
            }              
          }).done(function(data, textStatus, jqXHR) {
            peticiona=0;
            if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
            }             
          }).fail(function( jqXHR, textStatus, errorThrown ) {
            peticiona=0;    
            if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
            }
          });    
        }
  }    
  </script>
  @endif

  @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios'))
  <script>
  function confirmaborrararchivo(compt) {
    let idb = compt.id;
    var idform = $("#"+idb).data("id");

    if(idb=='') {
    } else {
      var confirmar = confirm("¿Desea Realmente Eliminar Este Archivo?");
      if (confirmar) {
        var route = "{{ route('borrararchivosfuncionario') }}";
        var token = $("#tokena").val();

        if (peticiona==0) {
          peticiona = 1;
          $.ajax({
            url: route,  
            headers: {'X-CSRF-TOKEN': token},      
            type: "POST",
            dataType: 'json',
            data: {id: idform},
            beforeSend: function(){},
            error: function(){},
            afterSend:function(){},
            success: function(response){
            }                
          }).done(function(data, textStatus, jqXHR) {
            peticiona=0;
            if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
            }           
            removeAllChilds('cuerpoadjuntos');
            veradjuntos_();
          }).fail(function( jqXHR, textStatus, errorThrown ) {
            peticiona=0;    
            if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
            }
          });    
        }
      }
    }   
  }  
  </script>
  @endif
@stop
