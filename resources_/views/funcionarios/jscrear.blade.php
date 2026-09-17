<script> 
    $("#pestana1-tab").trigger("click");

    $("#foto").click(function(){
        document.getElementById('my_photo_booth').style.display = 'none';     
        document.getElementById('my_photo_booth4').style.display = 'none';     
        document.getElementById('my_photo_booth3').style.display = 'none';     
    });

    function btnactivar(){
        document.getElementById('fileList').style.display = 'none';
        document.getElementById('my_photo_booth').style.display = '';
        document.getElementById('my_photo_booth4').style.display = 'none';
        document.getElementById('my_photo_booth2').style.display = 'none';
        document.getElementById('my_photo_booth5').style.display = 'none';
        document.getElementById('my_photo_booth3').style.display = '';
        activarcamara();
    }

    function activarcamara(){
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
            '<img src="'+data_uri+'"/><br/></br>'
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
            $("#resultqr").append(`<div id="imagenqr" width="70%" height="70%" align="center"><img src="{!! asset('/images/fotografia.jpg') !!}" width="70%" height="70%"/></div>`);
            document.getElementById('codigo_qr_imagen').style.display = '';
            document.getElementById('resultqr').style.display = '';                                
        }    
    }
</script>