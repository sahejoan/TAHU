@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Editar Roles</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            
                              @if ($errors->any())
                                 <div class="alert alert-dark alert-dismissible fade show" role="alert">
                                        <strong>¡Revise los Campos!</strong>
                                       @foreach ($errors->all() as $error)
                                           <span class="badge badge-danger" >{{$error}}</span>                                      
                                       @endforeach
                                      <button type="button" class="close" data-dismiss="alert" aria-label="close">
                                            <span aria-hidden="true">&times;</span>
                                      </button> 
                                  </div>                                
                             @endif
                            {!! Form::model($role, ['method'=>'PATCH', 'route' => ['roles.update', $role->id]])!!}
                               <div class="row">
                                 <div class="col-xs-12 col-ms-12 col-md-12">
                                     <div class="form-group">
                                         <label for="name">Nombre del Rol</label>
                                         {!!Form::text('name', null, array('class'=>'form-control'))!!}
                                     </div>
                                 </div>

                                 <div class="col-xs-12 col-ms-12 col-md-12">
                                    <div class="form-group">
                                      <label for="email">Permisos para este Rol</label>
                                      <br/>                                                          

                                      <div class="table-responsive" align = "left" style="width: 100%"> 
                                      <!-- <table id="tablaordenar" class="table table-hover table-striped table-bordered cell-border"> -->  <!--table-condensed-->
                                      <table id="tablaordenar" class="table table-active table-hover table-striped table-bordered cell-border table-sm" style="width: 100%">
                                        <thead class='table-danger'>
                                          <tr>
                                            <th></th>                  
                                            <th></th>                  
                                            <th>Permiso</th>
                                          </tr>
                                        </thead>
               
                                        <tfoot>
                                          <tr class='table-danger'>
                                            <th></th>                 
                                            <th></th>                  
                                            <th style="font-size=10px;font-weight: normal;">Permiso</th>                 
                                          </tr>                
                                        </tfoot> 

                                        <tbody>
                                          @foreach($permission as $value)
                                          <tr>
                                            <th></th>                  
                                            <td class="col-sm-1 text-center">
                                              {{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
                                            </td>                  
                                            <td class="col-sm-11 text-left">                                                                    
                                              {{ $value->name }}                                              
                                            </td>
                                          </tr>
                                          @endforeach                
                                        </tbody>
                                      </table>          
                                    </div>
                                    <br/>        
                                                   
                                  </div>
                                </div>
                                   
                                <div class="col-xs-12 col-ms-12 col-md-12">
                                  <button type="submit" class="btn btn-primary">Guardar</button>
                                </div> 
                                      
                               </div>
                            {!! Form::close()!!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section("scripts")
  <script>
  $(document).ready(function(){ 
    $('#tablaordenar').DataTable({    
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'bProcessing' : true,  
      'autoWidth'   : false,
      'responsive'  : false,
      'pageLength': 200,
      'lengthMenu': [
            [10, 25, 50, 100, 200, 300],
            [10, 25, 50, 100, 200, 'All'],
      ],      
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 0,1 ] } 
                      ], 
      'pagingType'  : "simple_numbers",
      'language'    : {
                       "decimal": ",",                                   
                       "infoPostFix": "",
                       "thousands": ".",
                       "emptyTable": "No hay datos",
                       "loadingRecords": "Cargando...",        
                       "lengthMenu": "Mostrar _MENU_ Permisos Por Página.",
                       "zeroRecords": "No Se Encontró Registro.",
                       "info": "_START_ a _END_ [_TOTAL_ Permisos En Total]",
                       "infoEmpty": "0 de 0 de 0 registros",
                       "infoFiltered": "(Encontrado de _MAX_registros)",
                       "search": "Busqueda filtrada: ",
                       "processing": "Procesando La Información",
                       "paginate": {
                                   "first"   : " |< ",
                                   "previous": "Anterior",
                                   "next"    : "Siguiente",
                                   "last"    : " >| "
                                 }
                      },

        //dom: 'Bfrtilp',          
        

    });

    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }
  });

  </script>
  @stop
