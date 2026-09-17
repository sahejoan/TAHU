@extends('layouts.app')

@section('content')
    @include('alert.succes')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading text-center">Inicio</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            {{-- Aqui inicio de estilos--}}
                            <style>
                                    .highcharts-figure,
                                    .highcharts-data-table table {
                                        min-width: 310px;
                                        max-width: 800px;
                                        margin: 1em auto;
                                    }

                                    .highcharts-data-table table {
                                        font-family: Verdana, sans-serif;
                                        border-collapse: collapse;
                                        border: 1px solid #ebebeb;
                                        margin: 10px auto;
                                        text-align: center;
                                        width: 100%;
                                        max-width: 500px;
                                    }

                                    .highcharts-data-table caption {
                                        padding: 1em 0;
                                        font-size: 1.2em;
                                        color: #555;
                                    }

                                    .highcharts-data-table th {
                                        font-weight: 900;
                                        padding: 0.5em;
                                    }

                                    .highcharts-data-table td,
                                    .highcharts-data-table th,
                                    .highcharts-data-table caption {
                                        padding: 0.5em;
                                    }

                                    .highcharts-data-table thead tr,
                                    .highcharts-data-table tr:nth-child(even) {
                                        background: #f8f8f8;
                                    }

                                    .highcharts-data-table tr:hover {
                                        background: #fff1f6;
                                    }
                            </style>
                            {{-- Fin de estilos  --}}
                                {{-- Aqui inicio de contenido --}}                                
                                                                        

                                     <figure class="highcharts-figure">
                                      <div id="container"></div>
                                     </figure>

                        {{-- <script src="https://code.highcharts.com/modules/series-label.js"></script>
                        <script src="https://code.highcharts.com/modules/exporting.js"></script>
                        <script src="https://code.highcharts.com/modules/export-data.js"></script>
                        <script src="https://code.highcharts.com/modules/accessibility.js"></script> --}}
                        <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/highcharts.js"></script>
                        <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/exporting.js"></script>
                        <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/export-data.js"></script>        
                        <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/accessibility.js"></script>
                        <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/series-label.js"></script>

                                                            
    <script>
        // Data retrieved https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature
        Highcharts.chart('container', {
            chart: {
                type: 'spline'
            },
            title: {
                text: 'Monthly Average Temperature'
            },
            subtitle: {
                text: 'Source: ' +
                    '<a href="https://en.wikipedia.org/wiki/List_of_cities_by_average_temperature" ' +
                    'target="_blank">Wikipedia.com</a>'
            },
            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                accessibility: {
                    description: 'Months of the year'
                }
            },
            yAxis: {
                title: {
                    text: 'Temperature'
                },
                labels: {
                    format: '{value}°'
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                name: 'Tokyo',
                marker: {
                    symbol: 'square'
                },
                data: [5.2, 5.7, 8.7, 13.9, 18.2, 21.4, 25.0, {
                    y: 26.4,
                    marker: {
                        symbol: 'url(https://www.highcharts.com/samples/graphics/sun.png)'
                    },
                    accessibility: {
                        description: 'Sunny symbol, this is the warmest point in the chart.'
                    }
                }, 22.8, 17.5, 12.1, 7.6]

            }, {
                name: 'Bergen',
                marker: {
                    symbol: 'diamond'
                },
                data: [{
                    y: 1.5,
                    marker: {
                        symbol: 'url(https://www.highcharts.com/samples/graphics/snow.png)'
                    },
                    accessibility: {
                        description: 'Snowy symbol, this is the coldest point in the chart.'
                    }
                }, 1.6, 3.3, 5.9, 10.5, 13.5, 14.5, 14.4, 11.5, 8.7, 4.7, 2.6]
            }]
        });
    </script>         

                             {{--   Hasta aquin fin             --}}
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