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
  function verfamiliares() { 
    var id = $("#idfuncionario").val();
    var route = "{{ route('verfamiliares') }}";
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
            if (response.length>0) { 
              $("#datosfamiliares").empty();
              
              $("#datosfamiliares").append(`<div class="table-responsive"><table id="tablaf1" class="table table-active table-hover table-striped table-bordered cell-border table-sm" style="width: 100%"></table></div>`);               

              var box = `<thead class="table-danger"><tr>
              <th>Cédula</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Fecha/Nac.</th>
              <th>Edad</th>
              <th>Parentesco</th>
              <th>Editar</th>
              <th>Borrar</th>                  
              </tr>
              </thead>
              <tbody>`;

              $("#tablaf1").append(``);

              response.forEach(element => {
                var edad = calculaEdad( moment(), element.fecha_nac);
                var fecha_nacimiento = moment(element.fecha_nac, 'YYYY-MM-DD').format('DD-MM-YYYY');

                box = box +
                `<tr>`+
                `<td class="col-sm-1 text-left tablecolortd">${element.cedula}</td>`+                
                `<td class="col-sm-1 text-left tablecolortd">${element.nombre}</td>`+
                `<td class="col-sm-1 text-left tablecolortd">${element.apellido}</td>`+
                `<td class="col-sm-1 text-left tablecolortd">`+fecha_nacimiento+`</td>`+
                `<td class="col-sm-1 text-left tablecolortd">`+edad+`</td>`+
                `<td class="col-sm-1 text-left tablecolortd">`+element.parentesco+`</td>`+
                `<td class="col-sm-1 text-left tablecolortd">`+
                  `<form name="frmeditar`+element.id+`" id="frmeditar`+element.id+`">`+                
                  `<input type="hidden" name="_token`+element.id+`" id="token`+element.id+`" value="`+token+`">`+                
                  `<a class="btn btn-success btn-sm fa fa-edit" title="Editar registro" href="#" data-toggle="modal" data-target="#modaleditarfamiliar" onclick="buscarfamiliar(`+element.id+`);"></a>`+                                
                  `</form>`+
                `</td>`+
                `<td class="col-sm-1 text-left tablecolortd">`+
                  `<form name="frmeliminar`+element.id+`" id="frmeliminar`+element.id+`">`+                
                  `<input type="hidden" name="_token`+element.id+`" id="token`+element.id+`" value="`+token+`">`+                
                  `<a class="btn btn-danger btn-sm" title="Eliminar registro" href="#" onclick="eliminarfamiliar(`+element.id+`);"><i class="fa fa-trash"></i></a>`+                                
                  `</form>`+                
                `</td>`+
                `</tr>`;
              });


              box = box + `</tbody>`;
              $("#tablaf1").append(box);

              $('#tablaf1').DataTable({    
              'paging'      : false,
              'lengthChange': false,
              'searching'   : true,
              'ordering'    : true,
              'info'        : true,
              'bProcessing' : true,  
              'autoWidth'   : false,              
              'responsive'  : true,
              'pagingType'  : "simple_numbers",
              'aoColumnDefs': [ 
                               { 'bSortable': false, 'aTargets': [ 3,4,5,6,7 ] } 
                              ], 
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
                               "processing": "Procesando La Información",
                               "paginate": {
                                   "first"   : " |< ",
                                   "previous": "Anterior",
                                   "next"    : "Siguiente",
                                   "last"    : " >| "
                                }
                              },              
              }); 

            } else {              
              $("#tablaf1").empty();              
              $("#datosfamiliares").append(`<div align="center">No hay datos</div>`);
            }          
          } else {
            var messageinvalido = ["Solicitud inválida"];
            MostrarMensajeError(messageinvalido);
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
