<script>
    $(document).on('keyup', function (e) {
        evt = e ? e : event;
        tcl = (window.Event) ? evt.which : evt.keyCode;          
        if (tcl == 13) {            
            $("#select2-id_divi-container").css('color','green');
            if ($("#select2-id_divi-results").length==0) {         
                consulta4 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta4 = "*";
                } else {
                    consulta4 = $('.select2-search__field').val();
                }
            }

            $("#select2-id_area-container").css('color','green');
            if ($("#select2-id_area-results").length==0) {         
                consulta1 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta1 = "*";
                } else {
                    consulta1 = $('.select2-search__field').val();
                }
            }

            if (consulta1.length>0) {
                if (peticion1==0) {
                    peticion1 = 1;
                    BuscarArea();   
                }         
            }            

            if (consulta4.length>0) {
                if (peticion4==0) {
                    peticion4 = 1;
                    BuscarDivision();    
                }         
            }                                    
        }        
    });

    function BuscarArea() {
                var route = "{{ route('buscarelarea') }}";
                var token = $("#token").val();
                var iddivi = $("#id_divi").val();
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {
                            b: consulta1,
                            id_divi: iddivi
                        },
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Error'){
                                $("#id_area").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_area").append(`<option value='${element.id}'>${element.nom_area}</option>`);                                        
                                    });                        
                                    $('#id_area').select2({         
                                        width: '100%',
                                        placeholder: "Escribir [Enter Busca]",
                                    });
                                } else {

                                }
                                Refrescar();
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

    function BuscarDivision() {        
        var route = "{{ route('buscarladivision') }}";
        var token = $("#token").val();
        var iddep = $('#id_dep').val();
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {
                            b: consulta4,
                            id_dep : iddep,
                        },
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
                                BuscarArea();
                            } else {
                                if (response=='Error') {
                                    
                                }
                            }                                                                                                                                                                                                                                                                
                        }                
                    }).done(function(data, textStatus, jqXHR) {
                        peticion4=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion4=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });        
    }
</script>