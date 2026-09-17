@extends('layouts.app')

@section('content')
        <style>
                /* Cambiar el tamaño de la fuente del título del evento */
                .fc-event .fc-event-title {
                    font-size: 10px; /* Tamaño de la fuente */
                    
                }
                            /* Fondo oscuro detrás del modal */
                #modalOverlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 99998;
                }

                /* Estilos para el modal */
                #eventModal {
                    display: none;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 99999;
                    background-color: white;
                    padding: 20px;
                    border: 1px solid #ccc;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    text-align: center;
                }

                /* Contenedor de la foto con superposición */
                #modalImageContainer {
                    position: relative;
                    display: inline-block;
                }

                /* Animación para la imagen */
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }

                /* Estilo de foto con sombra interna */
                #modalImage {
                    max-width: 100%;
                    height: auto;
                    border-radius: 10px;
                    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.5); /* Sombra interna */
                }
         </style>
    @include('alert.succes')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Calendario con Cumpleañeros del Mes del presente Año &nbsp;{{ $year }}</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">                          
                           <!-- Modal -->
<div id="eventModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 99999; background-color: white; padding: 20px; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: center;">
    <!-- Fondo decorativo -->
    <div style="position: relative; width: 300px; height: 400px; background-image: url('img/fondo1.jpg'); background-size: cover; background-position: center; border-radius: 10px;">
        <!-- Contenedor para la foto del funcionario -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 3px solid #007bff;">
            <img id="modalImage" src="" alt="Foto del funcionario" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <!-- Nombre del funcionario -->
        <div id="modalName" style="position: absolute; width: 300px; height: 100px; bottom: 0%; left: 50%; transform: translateX(-50%); background-color: rgba(0, 0, 0, 0.7); color: white; padding: 10px; border-radius: 5px; font-size: 12px; font-weight: bold;"></div>
    </div>
    <!-- Botón para cerrar el modal -->
    <button onclick="closeModal()" style="margin-top: 20px; padding: 5px 10px; background-color: #007bff; color: white; border: none; cursor: pointer;">Cerrar</button>
</div>

<!-- Fondo oscuro detrás del modal -->
<div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 99998;"></div>

                            <!-- Contenedor del calendario -->
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>   
@endsection

@section("scripts")
@include('birthdate.jsindex')  
<script>
    $(document).ready(function(){ 
        if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
            // Es seguro ejecutar la función
            MostrarMensajeSuccess(message);
        }

        if( typeof MostrarMensajeError !== 'undefined' && jQuery.isFunction( MostrarMensajeError ) ) {
            // Es seguro ejecutar la función
            MostrarMensajeError(messageerror);
        }        
    });
</script>
@endsection