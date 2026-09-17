<script>
    $(document).on('keyup', function (e) {
        evt = e ? e : event;
        tcl = (window.Event) ? evt.which : evt.keyCode;          
        if (tcl == 13) {            
            $("#select2-id_reg-container").css('color','green');
            if ($("#select2-id_reg-results").length==0) {         
                consulta1 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta1 = "*";
                } else {
                    consulta1 = $('.select2-search__field').val();
                }
            }   

            $("#select2-id_jefedep-container").css('color','green');
            if ($("#select2-id_jefedep-results").length==0) {         
                consulta2 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta2 = "*";
                } else {
                    consulta2 = $('.select2-search__field').val();
                }
            }   


            if (consulta1.length>0) {
                var route = "{{ route('buscardependenciaregion') }}";
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
                                $("#id_reg").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_reg").append(`<option value='${element.id}'>${element.nom_reg}</option>`);
                                    });                        
                                    $('#id_reg').select2({         
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

            if (consulta2.length>0) {
                var route = "{{ route('buscardependenciajefe') }}";
                var token = $("#token").val();

                if (peticion2==0) {
                    peticion2 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta2},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Error'){
                                $("#id_jefedep").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_jefedep").append(`<option value='${element.id}'>${element.nom_jefe}</option>`);
                                    });                        
                                    $('#id_jefedep').select2({         
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
                        peticion2=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion2=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }
        }
    });
</script>