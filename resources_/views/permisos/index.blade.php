@extends('layouts.app')

@section('css')

@stop

@section('content')
  <section class="section">
    <div class="section-header">
      <h3 class="page__heading">Permisos</h3>                        
    </div>        
        
    @include('alert.succes')
    @include('alert.errors')
    <div class="row">
    <div class="col-sm-6" align="left">
    {!!Form::open(array('name' => 'form', 'route' => array('buscarpermisos'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
          <div class="input-group mb-10">
            <input type="text" name="busqueda" id="busqueda" class="form-control" value="{{ $busqueda }}">
            <div class="input-group-prepend">
              <button type="button" class="btn btn-info btn-flat" onclick="document.form.submit();"><i class="fa fa-search"></i> Buscar</button>
            </div>            
            <!-- /btn-group -->
          </div>        
    {!!Form::close()!!}
    </div>
      <div class="col-sm-6" align="left">
        <div class="pagination justify-content-start">
          @can('crear-rol')
            <a class="btn btn-success" href="{{route('permisos.create')}}">Nuevo permiso</a>                              
          @endcan
        </div>                                                                                      
      </div>      
    </div>  
    <br>

    <div class="row" style="background:#ffffff;">
      <div class="col" align="left">
            <!-- Lista -->
            @if (count($objpermisos)>0)
              <div class="table-responsive" align = "left" style="width: 100%"> 
                <!-- <table id="tablaordenar" class="table table-hover table-striped table-bordered cell-border"> -->  <!--table-condensed-->
                <table id="tablaordenar" class="table table-active table-hover table-striped table-bordered cell-border table-sm" style="width: 100%">
                <thead class='table-danger'>
                  <tr>
                  <th></th>                  
                  <th>Permisos</th>
                  <th align="center">Editar</th>
                  <th align="center">Borrar</th>
                  </tr>
                </thead>
               
                <tfoot>
                <tr class='table-danger'>
                  <th></th>                  
                  <th style="font-size=10px;font-weight: normal;">Permisos</th>
                  <th style="font-size=10px;font-weight: normal;">Editar</th>
                  <th style="font-size=10px;font-weight: normal;">Borrar</th>                  
                </tr>                
                </tfoot> 

                <tbody>

                  @foreach($objpermisos as $permisos)

                  <tr>
                    <td></td>                  
                    <td class="col-sm-10 text-left">{{$permisos->name}}</td>

                    <td class="col-sm-0 text-center">
                      @can('editar-rol')
                        <a class="btn btn-success btn-sm  fa fa-edit" title="Editar registro" href="{{route('permisos.edit', $permisos->id)}}"></a>
                      @endcan                                                     
                    </td>

                    <td class="col-sm-0 text-center">                    
                      @can('borrar-rol')                                
                        {!!Form::open(array('id' => 'frmeliminar'.$permisos->id, 'name' => 'frmeliminar'.$permisos->id, 'route' => array('permisos.destroy',  $permisos->id), 'method' => 'delete'))!!}
                          <a id="btneliminar{{ $permisos->id }}" data-id="{{ $permisos->id }}" class="btn btn-danger btn-sm" title="Borra registrar" href="#" onclick="confirma(this)"><i class="fa fa-trash"></i></a>
                        {!!Form::close()!!}                                                                                        
                      @endcan
                    </td>
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

    @if (count($objpermisos)>0)
    <div class="row">
      <div class="col" align="left">
        <div class="pagination justify-content-start">
          @can('crear-rol')
          <a class="btn btn-success" href="{{route('permisos.create')}}">Nuevo permiso</a> 
          @endcan
        </div>                                                                                              
      </div>      
    </div> 
    @endif       
</section>                                        
@endsection 

@section("page_js")

@endsection 

@section("scripts")

  {!! Html::script('/js/in/confirmaraccionv2.js') !!} 

  @include('permisos.jsindex')

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
