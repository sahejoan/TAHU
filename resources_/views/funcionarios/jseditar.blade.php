<script> 

  $("#pestana1-tab").trigger("click");

  function btnactivar() {
    document.getElementById('fileList').style.display = 'none';
    document.getElementById('my_photo_booth').style.display = '';
    document.getElementById('my_photo_booth4').style.display = 'none';
    document.getElementById('my_photo_booth2').style.display = 'none';
    document.getElementById('my_photo_booth5').style.display = 'none';
    document.getElementById('my_photo_booth3').style.display = '';
    activarcamara();
  }

  function activarcamara() {
    Webcam.set({
      // live preview size
      width: 320,
      height: 240,
        
      // device capture size
      dest_width: 320,
      dest_height: 240,

      // final cropped size
      crop_width: 320,
      crop_height: 240,
     
      // format and quality
      image_format: 'jpeg',
      jpeg_quality: 90,
        
      // flip horizontal (mirror mode)
      flip_horiz: true,

      enable_flash: false
    });
    Webcam.attach( '#my_camera' );

    Webcam.on( 'error', function(err) {
      if ((err.name == "PermissionDeniedError") || (err.name == "NotAllowedError")) {
        document.getElementById('fileList').style.display = '';
        document.getElementById('my_photo_booth').style.display = 'none';
        document.getElementById('my_photo_booth4').style.display = 'none';
        document.getElementById('my_photo_booth2').style.display = '';
        document.getElementById('my_photo_booth5').style.display = '';
        document.getElementById('my_photo_booth3').style.display = 'none';
      } else {
        document.getElementById('fileList').style.display = '';
        document.getElementById('my_photo_booth').style.display = 'none';
        document.getElementById('my_photo_booth4').style.display = 'none';
        document.getElementById('my_photo_booth2').style.display = '';
        document.getElementById('my_photo_booth5').style.display = '';
        document.getElementById('my_photo_booth3').style.display = 'none';
      }
    });

    Webcam.on( 'load', function() {
        
    });
  }

  function preview_snapshot() {
    Webcam.freeze();
    document.getElementById('my_photo_booth2').style.display = 'none';
    document.getElementById('pre_take_buttons').style.display = 'none';
    document.getElementById('post_take_buttons').style.display = '';
  }
    
  function cancel_preview() {
    Webcam.unfreeze();
      
    document.getElementById('pre_take_buttons').style.display = '';
    document.getElementById('post_take_buttons').style.display = 'none';
  }
    
  function save_photo() {
    Webcam.snap( function(data_uri) {
      document.getElementById('results').innerHTML = 
      '<img src="'+data_uri+'"/><br/></br>';
      $("#capture").val(data_uri);
      Webcam.reset();
        
      document.getElementById('my_photo_booth').style.display = 'none';
      document.getElementById('my_photo_booth4').style.display = '';
      document.getElementById('my_photo_booth3').style.display = 'none';
      document.getElementById('my_photo_booth5').style.display = '';
      document.getElementById('results').style.display = '';        
    });
  } 

    function crear_qr() { 
        if ($('#cedula').val() != '' && $('#apellidos').val() != '' && $('#nombres').val() != '') {        
            var cedula = $('#cedula').val();
            var apellido = $('#apellido').val();
            var nombre = $('#nombre').val();
            var route = "{{ route('crearqr') }}";
            var token = $("#token").val();
            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': token},
                type: "POST",
                dataType: 'json',
                data: {cedula: cedula, nombre: nombre, apellido: apellido},
                beforeSend: function() {},
                error: function(response) {},
                success: function(response) {
                    if (response!="Error") {
                        $('#imagenqr').remove();            
                        $("#resultqr").append(`<div id="imagenqr" width="70%" height="70%" align="center"><img src="`+response+`" width="70%" height="70%"/></div>`);
                        document.getElementById('codigo_qr_imagen').style.display = '';
                        document.getElementById('resultqr').style.display = '';                    
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
        else {               
            $('#imagenqr').remove();
            $("#resultqr").append(`<div id="imagenqr" width="70%" height="70%"><img src="{!! asset('/images/fotografia.jpg') !!}" width="70%" height="70%"/></div>`);
            document.getElementById('codigo_qr_imagen').style.display = '';
            document.getElementById('resultqr').style.display = '';                                
        }    
    }  
</script>

<script>
  function guardarfamiliar() { 
    var cedula = $("#cedulaf").val();
    var apellido = $("#apellidof").val();
    var nombre = $("#nombref").val();
    var parentesco = $("#parentescof").val();
    var fecha_nac = $("#fecha_nacf").val();
    var id_func = $("#id_func").val();

    var route = "{{ route('guardarfamiliares') }}";
    var token = $("#token").val();
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: "POST",
      dataType: 'json',
      data: {cedula: cedula, nombre: nombre,  apellido: apellido, fecha_nac: fecha_nac, parentesco: parentesco, id_func: id_func},
        beforeSend: function() {},
        error: function(response) {},
        success: function(response) {         
          if (response=="OK") {            
            verfamiliares();
            MostrarMensajeSuccess("Registro guardado con éxito");
          } else {
            MostrarMensajeError(response);
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
  function actualizarfamiliar() { 
    var cedula = $("#cedulae").val();
    var apellido = $("#apellidoe").val();
    var nombre = $("#nombree").val();
    var parentesco = $("#parentescoe").val();
    var fecha_nac = $("#fecha_nace").val();
    var id = $("#idfam").val();

    var route = "{{ route('actualizarfamiliares') }}";
    var token = $("#token").val();
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: "POST",
      dataType: 'json',
      data: {id: id, cedula: cedula, nombre: nombre,  apellido: apellido, fecha_nac: fecha_nac, parentesco: parentesco},
        beforeSend: function() {},
        error: function(response) {},
        success: function(response) {         
          if (response=="OK") {
            verfamiliares();
            MostrarMensajeSuccess("Actualización exitosa");            
          } else {            
            MostrarMensajeError(response);
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
  function eliminarfamiliar(id) { 
    var id = id;

    var route = "{{ route('eliminarfamiliares') }}";
    var token = $("#token").val();
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: "POST",
      dataType: 'json',
      data: {id: id},
        beforeSend: function() {},
        error: function(response) {},
        success: function(response) {         
          if (response=="OK") {
            verfamiliares();
            MostrarMensajeSuccess("El registro se eliminó con éxito");            
          } else {            
            MostrarMensajeError(response);
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
  var permisoeditar = '<?php echo $permisoeditar; ?>'
  var permisoborrar = '<?php echo $permisoborrar; ?>'

  function verfamiliares() {   
    var auxid_func = $("#idfuncionario").val();
    var route = "{{ route('verfamiliares') }}";
    var tokent = $("#token").val();
    var table;

    table = $('.yajra-datatable').DataTable({        
      'processing'  : true,
      'serverSide'  : true,
      'paging'      : false,
      'lengthChange': true,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'bProcessing' : true,  
      'autoWidth'   : false,
      'responsive'  : false,
      'pageLength'  : 25,
      'lengthMenu'  : [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'Todos'],
      ],
      "columns": [
                    { className: "col-sm-1 text-left" },
                    { className: "col-sm-1 text-left" },
                    { className: "col-sm-1 text-left" },
                    { className: "col-sm-1 text-left" },                    
                    { className: "col-sm-1 text-left" },
                    { className: "col-sm-1 text-left" },                    
                    { className: "col-xs-0 text-center" },
                    { className: "col-xs-0 text-center" },                    
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 6,7 ] } 
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
           url : '{{ route('verfamiliares') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json',
           data: function(d) { 
            d.id_func = auxid_func;
           }                      
         },
        columns: [
            {data: 'cedula', className: 'col-sm-1 text-left', name: 'cedula'},
            {data: 'apellido', className: 'col-sm-2 text-left', name: 'apellido'},
            {data: 'nombre', className: 'col-sm-2 text-left', name: 'nombre'},
            {data: 'fecha_nacf', className: 'col-sm-1 text-left', name: 'fecha_nacf'},            

            {
              data: null,
              bSortable: true,
              className: 'col-sm-1 text-left',
              mRender: function(data, type, value) {
                var edad = calculaEdad( moment(), value['fecha_nac']);
                var fecha_nacimiento = moment(value['fecha_nac'], 'YYYY-MM-DD').format('DD-MM-YYYY');
                return edad;
              }
            }, 

            {data: 'parentesco', className: 'col-sm-1 text-left', name: 'parentesco'},            
          
            {
              data: null,
              bSortable: false,
              className: 'col-sm-0 text-center',
              mRender: function(data, type, value) {
                  if (value["editar"]>0) {
                    return `<form name="frmeditar`+value['editar']+`" id="frmeditar`+value['editar']+`">`+                
                    `<input type="hidden" name="_token`+value['editar']+`" id="token`+value['editar']+`" value="`+token+`">`+
                    `<a class="btn btn-success btn-xs fa fa-edit" title="Editar registro" href="#" data-toggle="modal" data-target="#modaleditarfamiliar" onclick="buscarfamiliar(`+value['editar']+`);"></a>`+
                    `</form>`;
                  } else {
                    return `<a class="btn btn-secondary btn-xs fa fa-edit disabled" title="Editar registro" href="#" data-toggle="modal" data-target="#modaleditarfamiliar" style="pointer-events: none"></a>`;
                  }
              }
            },
            {
              data: null,
              bSortable: false,
              className: 'col-sm-0 text-center',              
              mRender: function(data, type, value) {
                  if (value["borrar"]>0) {
                    return `<form name="frmeliminar`+value['borrar']+`" id="frmeliminar`+value['borrar']+`">`+                
                    `<input type="hidden" name="_token`+value['borrar']+`" id="token`+value['borrar']+`" value="`+token+`">`+                
                    `<a class="btn btn-danger btn-xs" title="Eliminar registro" href="#" onclick="eliminarfamiliar(`+value['borrar']+`);"><i class="fa fa-trash"></i></a>`+                    
                    `</form>`;
                  } else {
                    return `<a class="btn btn-secondary btn-xs disabled" title="Eliminar registro" href="#" style="pointer-events: none"><i class="fa fa-trash"></i></a>`; 
                  }
              }
            },
        ]
    });
  }
</script>

<script>
  function buscarfamiliar(id) {
    var id = id;
    var route = "{{ route('buscarfamiliares') }}";
    var token = $("#token").val();
    $.ajax({
      url: route,
      headers: {'X-CSRF-TOKEN': token},
      type: "POST",
      dataType: 'json',
      data: {id: id},
        beforeSend: function() {},
        error: function(response) {},
        success: function(response) {         
          if (response!="Error") {
            $("#idfam").val(response.id);
            $("#cedulae").val(response.cedula);
            $("#nombree").val(response.nombre);
            $("#apellidoe").val(response.apellido);
            $("#fecha_nace").val(response.fecha_nac);
            $("#parentescoe").val(response.parentesco);
          } else {

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