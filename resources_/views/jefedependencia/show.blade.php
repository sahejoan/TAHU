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
{!!Form::open(array('name' => 'form'))!!} 
  <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">    
{!!Form::close()!!}

  <section class="section">
    <div class="section-header">
      <h3 class="page__heading">Monitor de Actividades Jefes</h3>
    </div>        
    <br>
    <div class="row" style="background:#ffffff;">
      <div class="col" align="left">
        <!-- Lista -->
          <div class="table-responsive" align="left" style="width: 100%">
            {{-- <table id="tablaordenar" class="table table-hover table-striped table-bordered cell-border">  --}}<!-- table-condensed -->
            <table class="redTable yajra-datatable nowrap" style="width: 100%">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Creado por</th>
                  <th>Actualizado por</th>                  
                  <th>Eliminado por</th>
                  <th>Fech. creac.</th> 
                  <th>Fech. Actuli.</th>                 
                  <th>Fech. Elim.</th>                  
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
    <div class="row">
      <div class="col" align="left">
        <div class="pagination justify-content-start">
          <a class="btn btn-success" href="{{route('jefedependencia.index')}}">Atras</a>
        </div>
      </div>      
    </div> 
  </section>                                      
@endsection 

@section("page_js")

@endsection 

@section("scripts")
  {!! Html::script('/js/in/moment.js') !!}      

  @include('jefedependencia.jsshow')

  <script>
  var table;        
  $(document).ready(function(){ 
  btnroute = "{{ route('jefelistmonitor') }}";
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
                    { className: "col-xs-2 text-left" },
                    { className: "col-xs-2 text-left" },
                    { className: "col-xs-2 text-left" },
                    { className: "col-xs-2 text-left" },
                    { className: "col-xs-2 text-left" },
                    { className: "col-xs-2 text-left" },
                    { className: "col-xs-2 text-left" }                    
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 6 ] } 
                      ], 
      'pagingType'  : "simple_numbers",
      'language'    : {
                       "decimal": ",",                                   
                       "infoPostFix": "",
                       "thousands": ".",
                       "emptyTable": "No hay datos",
                       "loadingRecords": "Cargando...",        
                       "lengthMenu": "Mostrar _MENU_ Registros Por Página.",
                       "zeroRecords": "No Se Encontró Registro.",
                       "info": "_START_ a _END_ [_TOTAL_ Registros En Total]",
                       "infoEmpty": "0 de 0 de 0 registros",
                       "infoFiltered": "(Encontrado de _MAX_registros)",
                       "search": "Busqueda filtrada: ",
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
           url : '{{ route('jefelistmonitor') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json'
         },
        columns: [
            {data: 'nom_jefe', className: 'col-sm-2 text-left', name: 'nom_jefe'},            
            {
              data: null,
              bSortable: true,
              className: 'col-sm-2 text-left',
              mRender: function(data, type, value) {
                if (value['creator'] != null) {
                  return value['creator']['name'];  
                } else{
                  return '';
                }
              }
            },       
            {
              data: null,
              bSortable: true,
              className: 'col-sm-2 text-left',
              mRender: function(data, type, value) {
                if (value['editor'] != null) {
                  return value['editor']['name'];  
                  return '';
                } else{
                  return '';
                }
              }
            },       

            {
              data: null,
              bSortable: true,
              className: 'col-sm-2 text-left',
              mRender: function(data, type, value) {
                if (value['destroyer'] != null) {
                  return value['destroyer.name'];  
                } else{
                  return '';
                }
              }
            },
            {
              data: null,
              bSortable: true,
              className: 'col-sm-2 text-left',
              mRender: function(data, type, value) {
                if (value['created_at'] != null) {
                  mf = moment(new Date(value['created_at']));
                  fechacreated = mf.format('DD/MM/YYYY hh:mm:ss a'); 
                  return fechacreated;  
                } else{
                  return '';
                }
              }
            },            

            {
              data: null,
              bSortable: true,
              className: 'col-sm-2 text-left',
              mRender: function(data, type, value) {
                if (value['updated_at'] != null) {
                  mf = moment(new Date(value['updated_at']));
                  fechaupdate = mf.format('DD/MM/YYYY hh:mm:ss a'); 
                  return fechaupdate;  
                } else{
                  return '';
                }
              }
            },            

            {
              data: null,
              bSortable: true,
              className: 'col-sm-2 text-left',
              mRender: function(data, type, value) {
                if (value['updated_at'] != null) {
                  mf = moment(new Date(value['updated_at']));
                  fechadelete = mf.format('DD MM YYYY hh:mm:ss a'); 
                  return fechadelete;  
                } else{
                  return '';
                }
              }
            }                        
        ]         
    });
  });
</script>
@stop