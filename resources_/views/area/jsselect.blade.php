<script>
    $(document).on('keyup', function (e) {
        evt = e ? e : event;
        tcl = (window.Event) ? evt.which : evt.keyCode;          
        if (tcl == 13) {              
            $("#select2-id_divi-container").css('color','green');
            if ($("#select2-id_divi-results").length==0) {         
                consulta1 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta1 = "*";
                } else {
                    consulta1 = $('.select2-search__field').val();
                }
            }   
  
            if (consulta1.length>0) {
                var route = "{{ route('buscarladivision') }}";
                var token = $("#token").val();

                if (peticion1==0) {
                    peticion1 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta1},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Error'){
                                $("#id_divi").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_divi").append(`<option value='${element.id}'>${element.descripcion}</option>`);
                                    });                        
                                    $('#id_divi').select2({         
                                        width: '100%',
                                        placeholder: "Escribir [Enter Busca]",
                                    });
                                } else {

                                }
                            } else {
                                if (response=='Error') {
                                    
                                }
                            }                                                                                                                                                                                                                                                                
                        }                
                    }).done(function(data, textStatus, jqXHR) {
                        peticion1=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion1=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }                                    
        }
    });   
</script>