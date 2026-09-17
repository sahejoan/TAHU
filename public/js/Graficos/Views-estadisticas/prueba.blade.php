@extends('layouts.app')

@section('content')
    @include('alert.succes')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Inicio</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">                          
                            <header>
                                <h1 class="text-center text-light">Tutorial</h1>
                                <h2 class="text-center text-light">Cómo usar <span class="badge badge-success">HIGHCHARTS JS</span></h2>
                            </header>
                        
                            <div class="text-center">
                                <div class="btn-group" role="group" aria-label="">
                                    <button id="btnColumnas" type="button" class="btn btn-secondary">Columnas</button>
                                    <button id="btnLineas" type="button" class="btn btn-primary">Líneas</button>
                                    <button id="btnTorta" type="button" class="btn btn-dark">Torta</button>
                                    <button id="btnPrueba" type="button" class="btn btn-danger">Gráfico de Prueba</button>
                                    <!-- <button id="btnBD" type="button" class="btn btn-info">Gráficos desde BD</button> -->
                                </div>
                            </div>
                        
                            <!--En este container se muestran los gráficos-->
                            <div id="contenedor" style="min-width: 320px; height: 400px; margin: 0 auto"></div>
                        
                            <!--Modal para gráficos-->
                            <div id="modal-1" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!--En este container se muestran los gráficos-->
                                            <div id="contenedor-modal" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- jQuery, Popper.js, Bootstrap JS -->
                            <script src="JQuery/jquery-3.3.1.min.js"></script>
                            <script src="popper/popper.min.js"></script>
                            <script src="Bootstrap_4/js/bootstrap.min.js"></script>
                            <!-- Highcharts JS -->
                            <script src="pluggins/Highcharts_7.0.3/code/highcharts.js"></script>
                            <script src="pluggins/Highcharts_7.0.3/code/modules/exporting.js"></script>
                            <script src="pluggins/Highcharts_7.0.3/code/modules/export-data.js"></script>
                        
                            <script src="pluggins/Highcharts_7.0.3/code/modules/drilldown.js"></script>
                            <script src="codigoJS.js"></script>
                                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section("scripts")
<script>
    $(document).ready(function(){ 
        if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
        //Es seguro ejectura la función
        MostrarMensajeSuccess(message);
        }

        if( typeof MostrarMensajeError !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
        //Es seguro ejectura la función
        MostrarMensajeError(messageerror);
        }        
    });
</script>
@endsection