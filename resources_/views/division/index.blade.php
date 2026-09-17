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
              <a class="dropdown-item" href="{{route('monitorregion.index')}}"><i class="fa fa-desktop"></i> Actividades</a>
              @endif
            </div>
          </div>
        </div>        
        Divisiones o Coordinaciones
      </h7>                        
    </div>        
    <br> 
    @include('alert.succes')
    @include('alert.errors')
    <div class="row">
    <div class="col-xs-6 col-sm-6 col-md-6" align="left">
    {!!Form::open(array('name' => 'form'))!!} 
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">        
          <div class="input-group mb-10">
            <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ $busqueda }}">
            <div class="input-group-prepend">
              <button id="btnbuscar" type="button" class="btn btn-info btn-flat" onclick="Refrescar();"><i class="fa fa-search"></i> Buscar</button>
            </div>            
            <!-- /btn-group -->
          </div>        
    {!!Form::close()!!}
    </div>
    <div class="col-xs-1 col-sm-1 col-md-1" align="left">        
      &nbsp;
    </div>    
    <div class="col-xs-5 col-sm-5 col-md-5" align="center">
          @if (Auth::user()->hasAnyPermission('2_Crear_division'))
            <a class="btn btn-success" href="{{route('division.create')}}">Nueva división</a>                              
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
                  <th>Descripción</th>
                  <th>Ubicación</th>
                  <th>Nombre del funcionario</th>
                  <th align="center" class="text-center">Función</th>
                  <th align="center">Editar</th>
                  <th align="center">Borrar</th>
                  </tr>
                </thead>
               
                <tbody>
               
                </tbody>
                </table>          
            </div>      
      </div>      
    </div>  

    @if (Auth::user()->hasAnyPermission('2_Crear_division'))
    <br>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12" align="center">
          <a class="btn btn-success" href="{{route('division.create')}}">Nueva división</a> 
      </div>      
    </div> 
    @endif       
</section>                                        
@endsection 

@section("page_js")

@endsection 

@section("scripts")

  {!! Html::script('/js/in/confirmaraccionv2.js') !!} 

  @include('division.jsindex') 

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
@stop
