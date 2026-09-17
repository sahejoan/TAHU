  <script>
  var table;        
  $(document).ready(function(){ 
  btnroute = "{{ route('usuarioslist') }}";
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
                    { className: "col-lg-4 col-xs-4 col-sm-4 col-md-4 text-left" },
                    { className: "col-lg-4 col-xs-4 col-sm-4 col-md-4 text-left" },
                    { className: "col-lg-4 col-xs-4 col-sm-4 col-md-4 text-left" },                    
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },                    
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 3,4 ] } 
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
           url : '{{ route('usuarioslist') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json',
           beforeSend: function() { }           
         },
        columns: [
            {data: 'name', className: 'col-lg-4 col-xs-4 col-sm-4 col-md-4 text-left', name: 'name'},
            {data: 'email', className: 'col-lg-4 col-xs-4 col-sm-4 col-md-4 text-left', name: 'email'},
            {data: 'rolName', className: 'col-lg-4 col-xs-4 col-sm-4 col-md-4 text-left', name: 'rolName'},            
          
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {                
                  if (value["editar"]>0) {
                    var xroute = "{{ route('usuarios.edit',['usuario'=>'__idusuarios']) }}";
                    xroute = xroute.replace('__idusuarios', value["editar"]);
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit" title="Editar registro" href="'+xroute+'"></a></center>';
                  } else {
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit disabled" title="Editar registro" href="'+xroute+'"></a></center>';
                  }
                } else { 
                  if (value["editar"]>0) {
                    var xroute = "{{ route('usuarios.edit',['usuario'=>'__idusuarios']) }}";
                    xroute = xroute.replace('__idusuarios', value["editar"]);
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit" title="Editar registro" href="'+xroute+'"></a></center>';
                  } else {
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit disabled" title="Editar registro" href="'+xroute+'"></a></center>';
                  }                  
                }
              }
            },
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {                                
                  if (value["borrar"]>0) {
                    var xroute = "{{ route('usuarios.destroy',['usuario'=>'__idusuarios']) }}";
                    xroute = xroute.replace('__idusuarios', value["borrar"]);
                    return '<form method="POST" action="'+xroute+'" accept-charset="UTF-8" id="frmeliminar'+value["borrar"]+'" name="frmeliminar'+value["borrar"]+'">'+
                        '<input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a id="btneliminar'+value["borrar"]+'" data-id="'+value["borrar"]+'" class="btn btn-danger btn-xs " title="Borra registrar" href="#" onclick="confirma(this)"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  } else {
                    return '<form><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a class="btn btn-danger btn-xs disabled" title="Borra registrar" href="#" style="pointer-events: none"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  }
                } else { 
                  if (value["borrar"]>0) {
                    var xroute = "{{ route('usuarios.destroy',['usuario'=>'__idusuarios']) }}";
                    xroute = xroute.replace('__idusuarios', value["borrar"]);
                    return '<form method="POST" action="'+xroute+'" accept-charset="UTF-8" id="frmeliminar'+value["borrar"]+'" name="frmeliminar'+value["borrar"]+'">'+
                        '<input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a id="btneliminar'+value["borrar"]+'" data-id="'+value["borrar"]+'" class="btn btn-danger btn-xs " title="Borra registrar" href="#" onclick="confirma(this)"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  } else {
                    return '<form><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a class="btn btn-danger btn-xs disabled" title="Borra registrar" href="#" style="pointer-events: none"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  }                  
                }
              }
            },
        ]
    });

    $('input[name="busqueda"]').change(function(){
      Refrescar();
    });

    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }
  });

  function Refrescar() {     
      var dataTable = $(".yajra-datatable").dataTable();    
      dataTable.fnFilter($("#busqueda").val());

      table.ajax.reload();
  }  

  </script>

