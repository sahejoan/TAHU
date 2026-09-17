@extends('layouts.app')

@section('content')
    @include('alert.succes')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading"></h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">                          
                                
                                <div id="contenedor" style="min-width: 320px; height: 400px; margin: 0 auto"></div>                
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <!-- jQuery, Popper.js, Bootstrap JS -->
         {{-- <script src="Higthcharts/JQuery/jquery-3.3.1.min.js"></script> --}}
         {{-- <script src="Higthcharts/popper/popper.min.js"></script> --}}
         {{-- <script src="Higthcharts/Bootstrap_4/js/bootstrap.min.js"></script>      --}}
          <!-- Highcharts JS -->              
         <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/highcharts.js"></script>
         <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/exporting.js"></script>
         <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/export-data.js"></script>        
         <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/accessibility.js"></script>
         {{-- <script src="Higthcharts/pluggins/Highcharts_7.0.3/code/modules/drilldown.js"></script> --}}
         
         {{-- <script src="Higthcharts/codigoJS2.js"></script> --}}
         {{-- Data   --}}
         <script>
            // Data retrieved from https://netmarketshare.com
Highcharts.chart('contenedor', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'column'
    },
    title: {
        text: 'Censo Contribuyentes por Estado 2023 ',
        align: 'left'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            }
        }
    },
    series: [{
        name: 'Estado',
        colorByPoint: true,
        data:  <?= $data ?>
    }]
});
         </script>

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