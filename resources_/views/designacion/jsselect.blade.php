<script>
    $(document).on('keyup', function (e) {
        evt = e ? e : event;
        tcl = (window.Event) ? evt.which : evt.keyCode;          
        if (tcl == 13) {            
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
            
            $("#select2-id_func-container").css('color','green');
            if ($("#select2-id_func-results").length==0) {         
                consulta2 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta2 = "*";
                } else {
                    consulta2 = $('.select2-search__field').val();
                }
            }   

            $("#select2-id_dep-container").css('color','green');
            if ($("#select2-id_dep-results").length==0) {         
                consulta3 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta3 = "*";
                } else {
                    consulta3 = $('.select2-search__field').val();
                }
            }   

            $("#select2-id_jefedep-container").css('color','green');
            if ($("#select2-id_jefedep-results").length==0) {         
                consulta4 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta4 = "*";
                } else {
                    consulta4 = $('.select2-search__field').val();
                }
            }   

            $("#select2-id_divi-container").css('color','green');
            if ($("#select2-id_divi-results").length==0) {         
                consulta5 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta5 = "*";
                } else {
                    consulta5 = $('.select2-search__field').val();
                }
            }   

            $("#select2-id_jefediv-container").css('color','green');
            if ($("#select2-id_jefediv-results").length==0) {         
                consulta7 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta7 = "*";
                } else {
                    consulta7 = $('.select2-search__field').val();                    
                }
            }   


            $("#select2-id_firma-container").css('color','green');
            if ($("#select2-id_firma-results").length==0) {         
                consulta6 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta6 = "*";
                } else {
                    consulta6 = $('.select2-search__field').val();
                }
            }   

            $("#select2-id_jefeth-container").css('color','green');
            if ($("#select2-id_jefeth-results").length==0) {         
                consulta9 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta9 = "*";
                } else {
                    consulta9 = $('.select2-search__field').val();
                }
            }   

            $("#select2-id_coorth-container").css('color','green');
            if ($("#select2-id_coorth-results").length==0) {         
                consulta10 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta10 = "*";
                } else {
                    consulta10 = $('.select2-search__field').val();
                }
            }   

            if (consulta1.length>0) {
                var route = "{{ route('buscarelarea') }}";
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
                var route = "{{ route('buscarelfuncionario') }}";
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
                                $("#id_func").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_func").append(`<option value='${element.id}'>${element.nombre} ${element.apellido}</option>`);
                                    });                        
                                    $('#id_func').select2({         
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


            if (consulta3.length>0) {
                var route = "{{ route('buscarladependencia') }}";
                var token = $("#token").val();

                if (peticion3==0) {
                    peticion3 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta3},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {                            
                            if (response!='Error'){
                                $("#id_dep").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_dep").append(`<option value='${element.id}'>${element.nombredependencia}</option>`);
                                    });                        
                                    $('#id_dep').select2({         
                                        width: '100%',
                                        placeholder: "Escribir [Enter Busca]",
                                    });

                                    $("#tempid_dep").val($("#id_dep").val());
                                    BuscarJefe();
                                } else {
                                    $("#tempid_dep").val('0');
                                    BuscarJefe();
                                }
                            } else {
                                if (response=='Error') {
                                    
                                }
                            }                                                                                                                                                                                                                                                                
                        }                
                    }).done(function(data, textStatus, jqXHR) {
                        peticion3=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion3=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }

            if (consulta4.length>0) {
                var route = "{{ route('buscardependenciajefe') }}";
                var token = $("#token").val();

                if (peticion4==0) {
                    peticion4 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta4},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Exit'){
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
            }

            if (consulta5.length>0) {
                var route = "{{ route('buscarladivision') }}";
                var token = $("#token").val();

                if (peticion5==0) {
                    peticion5 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta5},
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

                                    $("#tempid_divi").val($("#id_divi").val());                                    
                                    BuscarJefeDivision();
                                    BuscarAreaDivision();
                                } else {
                                    $("#tempid_divi").val('0');
                                    BuscarJefeDivision();
                                }
                            } else {
                                if (response=='Error') {
                                    
                                }
                            }                                                                                                                                                                                                                                                                
                        }                
                    }).done(function(data, textStatus, jqXHR) {
                        peticion5=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion5=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }

            if (consulta6.length>0) {
                var route = "{{ route('buscarlafirma') }}";
                var token = $("#token").val();

                if (peticion6==0) {
                    peticion6 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta6},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Error'){
                                $("#id_firma").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_firma").append(`<option value='${element.id}'>${element.firma}</option>`);
                                    });                        
                                    $('#id_firma').select2({         
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
                        peticion6=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion6=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }

            if (consulta7.length>0) {
                var route = "{{ route('buscardependenciajefe') }}";
                var token = $("#token").val();

                if (peticion7==0) {
                    peticion7 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta7},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Exit'){
                                $("#id_jefediv").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_jefediv").append(`<option value='${element.id}'>${element.nom_jefe}</option>`);
                                    });                        
                                    $('#id_jefediv').select2({         
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
                        peticion7=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion7=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }

            if (consulta9.length>0) {
                var route = "{{ route('buscareljefeth') }}";
                var token = $("#token").val();

                if (peticion9==0) {
                    peticion9 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta9},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Error'){
                                $("#id_jefeth").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_jefeth").append(`<option value='${element.id}'>${element.nom_jefe}</option>`);
                                    });                        
                                    $('#id_jefeth').select2({         
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
                        peticion9=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion9=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }

            if (consulta10.length>0) {
                var route = "{{ route('buscarelcoorth') }}";
                var token = $("#token").val();

                if (peticion10==0) {
                    peticion10 = 1;
                    $.ajax({
                        url: route,  
                        headers: {'X-CSRF-TOKEN': token},      
                        type: "POST",
                        dataType: 'json',
                        data: {b: consulta10},
                        beforeSend: function(){},
                        error: function(){},
                        afterSend:function(){},
                        success: function(response) {
                            if (response!='Error'){
                                $("#id_coorth").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_coorth").append(`<option value='${element.id}'>${element.nom_jefe}</option>`);
                                    });                        
                                    $('#id_coorth').select2({         
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
                        peticion10=0;
                        if ( console && console.log ) {
                            console.log( "La solicitud se ha completado correctamente." );
                        }             
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        peticion10=0;    
                        if ( console && console.log ) {
                            console.log( "La solicitud a fallado: " +  textStatus);
                        }
                    });    
                }         
            }

        }
    });


    function BuscarJefe() {        
        var consulta = $("#tempid_dep").val();
        var route = "{{ route('buscareljefe') }}";
        var token = $("#token").val();

        $.ajax({
            url: route,  
            headers: {'X-CSRF-TOKEN': token},      
            type: "POST",
            dataType: 'json',
            data: {id_dep: consulta},
            beforeSend: function(){},
            error: function(){},
            afterSend:function(){},
            success: function(response) {                            
                if (response!='Error') {
                    $("#id_jefedep").empty();
                    if (response != 'Exit') {                        
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
                        $('#id_jefedep').select2({         
                            width: '100%',
                            placeholder: "Escribir [Enter Busca]",
                        });
                    }
                } else {
                    if (response=='Error') {
                                    
                    }
                }                                                                                                                                                                                                                                                                
            }                
        }).done(function(data, textStatus, jqXHR) {
            peticion5=0;
            if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
            }             
        }).fail(function( jqXHR, textStatus, errorThrown ) {
            peticion5=0;    
            if ( console && console.log ) {
                console.log( "La solicitud a fallado: " +  textStatus);
            }
        });
    }

    function BuscarJefeDivision() {        
        var consulta = $("#tempid_divi").val();        
        var route = "{{ route('buscareljefedivision') }}";
        var token = $("#token").val();

        $.ajax({
            url: route,  
            headers: {'X-CSRF-TOKEN': token},      
            type: "POST",
            dataType: 'json',
            data: {id_div: consulta},
            beforeSend: function(){},
            error: function(){},
            afterSend:function(){},
            success: function(response) {                            
                console.log("Desde aqui:");
                console.log(response);
                if (response!='Error') {
                    $("#id_jefediv").empty();
                    if (response != 'Exit') {                        
                        if (response.length>0) { 
                            response.forEach(element => {  
                                $("#id_jefediv").append(`<option value='${element.id}'>${element.nom_jefe}</option>`);
                            });                        
                            $('#id_jefediv').select2({         
                                width: '100%',
                                placeholder: "Escribir [Enter Busca]",
                            });
                        } else {

                        }
                    } else {
                        $('#id_jefediv').select2({         
                            width: '100%',
                            placeholder: "Escribir [Enter Busca]",
                        });
                    }
                } else {
                    if (response=='Error') {
                                    
                    }
                }                                                                                                                                                                                                                                                                
            }                
        }).done(function(data, textStatus, jqXHR) {
            peticion6=0;
            if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
            }             
        }).fail(function( jqXHR, textStatus, errorThrown ) {
            peticion6=0;    
            if ( console && console.log ) {
                console.log( "La solicitud a fallado: " +  textStatus);
            }
        });
    }

    function BuscarAreaDivision() {        
        var consulta = $("#tempid_divi").val();
        var route = "{{ route('buscarelareadivision') }}";
        var token = $("#token").val();        

        $.ajax({
            url: route,  
            headers: {'X-CSRF-TOKEN': token},      
            type: "POST",
            dataType: 'json',
            data: {id_div: consulta},
            beforeSend: function(){},
            error: function(){},
            afterSend:function(){},
            success: function(response) {                            
                if (response!='Error') {
                    if (response != 'Exit') {    
                    console.log(response);                    
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
                    } else {
                        $('#id_area').select2({         
                            width: '100%',
                            placeholder: "Escribir [Enter Busca]",
                        });
                    }
                } else {
                    if (response=='Error') {
                                    
                    }
                }                                                                                                                                                                                                                                                                
            }                
        }).done(function(data, textStatus, jqXHR) {
            peticion8=0;
            if ( console && console.log ) {
                console.log( "La solicitud se ha completado correctamente." );
            }             
        }).fail(function( jqXHR, textStatus, errorThrown ) {
            peticion8=0;    
            if ( console && console.log ) {
                console.log( "La solicitud a fallado: " +  textStatus);
            }
        });        
    }
</script>