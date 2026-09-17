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
    @include('alert.succes')
    <section class="section">
        <div class="section-header" align="center">
            <h5 class="page__heading">Listado General QR de Funcionarios</h5>
        </div>
        <center>(Tamaño Carnet 56mm x 90mm)</center>
        <div class="section-body">
        <div class="row">                
          <div class="col-xs-1 col-sm-1 col-md-1">
            <center>
            <a class="btn btn-success btn-xs" href="{!!URL::to('qrcodes')!!}">Atras</a>
            </center>
          </div>
          <div class="col-xs-1 col-sm-1 col-md-1">
            &nbsp;
          </div>          
          <div class="col-xs-2 col-sm-2 col-md-2" align="center">          
            {!!Form::open(array('name' => 'form2', 'route' => array('listapdf'), 'method' => 'post', 'target'=>"_blank", 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
            <input type="hidden" name="buscarl" id="buscarl" value="">
            <input type="hidden" name="id_depl" id="id_depl" value="">
            <a class="btn btn-info btn-xs" href="#" onclick="Imprimir();"><i class="fa fa-print"></i> QR agrupados</a>
            {!!Form::close()!!}
          </div>
          <div class="col-xs-2 col-sm-2 col-md-2" align="center">          
            {!!Form::open(array('name' => 'form3', 'route' => array('listapdfs'), 'method' => 'post', 'target'=>"_blank", 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
            <input type="hidden" name="buscarls" id="buscarls" value="">
            <input type="hidden" name="id_depls" id="id_depls" value="">
            <a class="btn btn-info btn-xs" href="#" onclick="ImprimirTarjetas();"><i class="fa fa-print"></i> Tarjetas QR</a>
            {!!Form::close()!!}
          </div>
          <div class="col-xs-3 col-sm-3 col-md-3" align="center">          
            {!!Form::open(array('name' => 'form4', 'route' => array('listapdfsagrupados'), 'method' => 'post', 'target'=>"_blank", 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
            <input type="hidden" name="buscarlsg" id="buscarlsg" value="">
            <input type="hidden" name="id_deplsg" id="id_deplsg" value="">
            <a class="btn btn-info btn-xs" href="#" onclick="ImprimirTarjetasAgrupados();"><i class="fa fa-print"></i> Tarjetas QR agrupados</a>
            {!!Form::close()!!}
          </div>                          
          <div class="col-sm-3">          
          </div>                  
          </div>  
        </div>

    <br>                       
    <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12" align="left">
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

          <div class="input-group mb-10">
            <input type="text" name="buscarv" id="buscarv" class="form-control" title="Busqueda por Cédula o Nombre del funcionario" value="{{ $buscar }}">
            <div class="input-group-prepend">
              <button type="button" class="btn btn-info btn-flat" onclick="Refrescar();"><i class="fa fa-search"></i> Buscar</button>
            </div>
            <!-- /btn-group -->
          </div>        
    {!!Form::close()!!}
    </div>
    </div>
    <br>
              <div class="table-responsive" align = "left" style="width: 100%"> 
                <!-- <table id="tablaordenar" class="table table-hover table-striped table-bordered cell-border"> -->  <!--table-condensed-->
                <table class="redTable yajra-datatable nowrap" style="width: 100%">
                <thead>
                  <tr>
                  <th>Cédula</th>
                  <th>Apellidos</th>
                  <th>Nombres</th>
                  <th align="center"><center><i class="fas fa-solid fa-print"></i></center></th>
                  </tr>
                </thead>
              
                <tbody>
                
                </tbody>
                </table>          
            </div>      
            <!-- Fin de la lista -->
        </div>                                           
    </section>
@endsection

@section("scripts")
  <script>
    var peticion1=0;

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

  <script>
    $(document).ready(function(){ 
        $("#id_dep").change(event => {           
            Refrescar();
        });
    });
  </script>  
  
  <script>
  var table;        
  var auxid_dep = '0';     
  $(document).ready(function(){ 
  btnroute = "{{ route('qrlist') }}";
  var tokent = $("#token").val();    
  table = $('.yajra-datatable').DataTable({        
      'processing'  : true,
      'serverSide'  : true,
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'bProcessing' : true,  
      'autoWidth'   : false,
      'responsive'  : false,
      'pageLength'  : 10,
      'lengthMenu'  : [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'Todos'],
      ],
      "columns": [
                    { className: "col-sm-2 text-left" },
                    { className: "col-sm-5 text-left" },
                    { className: "col-sm-5 text-left" },
                    { className: "col-xs-0 text-center" },
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 3 ] } 
                      ], 
      'pagingType'  : "simple_numbers",
      'language'    : {
                       "lengthMenu": "Mostrar _MENU_ Registros Por Página.",
                       "zeroRecords": "No Se Encontró Registro.",
                       "info": "_START_ a _END_ [_TOTAL_ Registros En Total]",
                       "infoEmpty": "0 de 0 de 0 registros",
                       "infoFiltered": "(Encontrado de _MAX_registros)",
                       "search": "Buscar: ",
                       "processing": "<div id='loader'><img style='width:50px; height:50px;' src='"+urlImagenLoading+"'/></div>",
                       "paginate": {
                                   "first"   : " |< ",
                                   "previous": "Anterior",
                                   "next"    : "Siguiente",
                                   "last"    : " >| "
                                 }
                      },
        dom: 'frtilp',                      
        deferRender: true,
        ajax:{
           url : '{{ route('qrlist') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json',
           data: function(d) { 
            d.id_dep = auxid_dep;
           },           
         },
        columns: [
            {data: 'cedula', className: 'col-sm-2 text-left', name: 'cedula'},
            {data: 'nombre', className: 'col-sm-5 text-left', name: 'nombre'},
            {data: 'apellido', className: 'col-sm-5 text-left', name: 'apellido'},
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {
                  var xroute = "{{ route('tarjetapdf','__idfuncionario') }}";
                  xroute = xroute.replace('__idfuncionario', value["ver"]);
                  return '<a class="btn btn-warning btn-sm btn-xs" title="Imprimir tarjeta QR" href="'+xroute+'" target="_blank"><i class="fa fa-qrcode"></i></a>';
                } else { return ''; }
              }
            }            
        ],                              
    });

    $('input[name="buscarv"]').change(function(){
      Refrescar();
    });

    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }
  });

  function Refrescar() {
      auxid_dep = $('#id_dep').val();       
      var dataTable = $(".yajra-datatable").dataTable();    
      dataTable.fnFilter($("#buscarv").val());

      table.ajax.reload();
  }

  function Imprimir() {
    $('#buscarl').val($('#buscarv').val());
    $('#id_depl').val($('#id_dep').val());    
    document.form2.submit();
  }  

  function ImprimirTarjetas() {
    $('#buscarls').val($('#buscarv').val());
    $('#id_depls').val($('#id_dep').val());        
    document.form3.submit();
  }    

  function ImprimirTarjetasAgrupados() {
    $('#buscarlsg').val($('#buscarv').val());
    $('#id_deplsg').val($('#id_dep').val());        
    document.form4.submit();
  }      
  </script>

@stop
