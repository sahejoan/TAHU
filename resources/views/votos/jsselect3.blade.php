<script>
    $(document).on('keyup', function (e) {
        evt = e ? e : event;
        tcl = (window.Event) ? evt.which : evt.keyCode;          
        if (tcl == 13) {                       
            $("#select2-id_dep-container").css('color','green');
            if ($("#select2-id_dep-results").length==0) {         
                consulta2 = "";
            } else {
                if ($('.select2-search__field').val()=='') {
                    consulta2 = "*";
                } else {
                    consulta2 = $('.select2-search__field').val();
                }
            }   
        
            if (consulta2.length>0) {
                var route = "{{ route('buscarladependencia') }}";
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
                                $("#id_dep").empty();                        
                                if (response.length>0) { 
                                    response.forEach(element => {  
                                        $("#id_dep").append(`<option value='${element.id}'>${element.nombredependencia}</option>`);
                                    });                        
                                    $('#id_dep').select2({         
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