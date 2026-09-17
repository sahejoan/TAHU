$("#btnColumnas").click(function() {
    columnas();
});
$("#btnLineas").click(function() {
    lineas();
});
$("#btnTorta").click(function() {
    $(".modal-header").css("background-color", "#343a40");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Gráfico de Torta");
    $("#modal-1").modal("show");
    torta();
});
$("#btnPrueba").click(function() {
    $(".modal-header").css("background-color", "#dc3545");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Gráfico de pruebas");
    $("#modal-1").modal("show");
    prueba();
});


var chart1, options;
$("#btnBD").click(function() {
    $(".modal-header").css("background-color", "#17a2b8");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("Gráfico desde BD");
    $("#modal-1").modal("show");

    $.ajax({
        url: "datos/graficos.php",
        type: "POST",
        dataType: "json",
        success: function(data) {
            options.series[0].data = data;
            chart1 = new Highcharts.Chart(options);
            console.log(data);
        }
    })
    datos();
});

function datos() {
    var v_modal = $("#modal_1").modal({ show: false });
    options = {
        chart: {
            renderTo: 'contenedor-modal',
            type: 'column'
        },
        title: {
            text: 'Stock de Productos'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: ' Cantidad'
            }
        },
        plotOptions: {
            series: {
                borderWidth: 1,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.0f}'
                }
            }
        },
        tooltip: {
            headerFormat: "<span style='font-size:11px'> {series.name}</span><br>",
            pointFormat: "<span style='color:{point.color}'>{point.name}</span>: <b>{point.y:.0f}</b>"
        },
        series: [{
            name: "Productos",
            colorByPoint: true,
            data: [],
        }]
    }
    v_modal.on("shown", function() {});
    v_modal.modal("show");
}

function columnas() {
    Highcharts.chart('contenedor', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Gráficos de columnas con profundidad'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Censo por dependencias'
            },
        },
        series: [{
            name: 'Censo',
            colorByPoint: true,
            data: [{
                    name: 'Sede Gerencia',
                    y: 5, //cant de modelos
                    drilldown: 'Sede Gerencia'
                }, {
                    name: 'STI San Fernando',
                    y: 6,
                    drilldown: 'STI San Fernando'
                }, {
                    name: 'STI San Juan de los Morros',
                    y: 4,
                    drilldown: 'STI San Juan de los Morros'
                },
                {
                    name: 'STI Valle de la Pascua',
                    y: 4,
                    drilldown: 'STI Valle de la Pascua'
                },
                {
                    name: 'UTI Altagracia de Orituco',
                    y: 4,
                    drilldown: 'UTI Altagracia de Orituco'
                }



            ]
        }],
        drilldown: {
            series: [{
                    name: 'Censo Especifico',
                    id: 'Sede Gerencia',
                    data: [
                        ['Calabozo', 4],
                        ['Camaguan', 2],
                        ['Guayabal', 1],
                        ['Guardatinajas', 2],
                        ['El Rastro', 1]
                    ]
                }, {
                    name: 'Censo Especifico',
                    id: 'STI San Fernando',
                    data: [
                        ['San Fernando', 4],
                        ['Biruaca', 6],
                        ['Acaguas', 7],
                        ['El Saman', 1],
                        ['Aputiro', 3],
                        ['Cunavicha', 5]
                    ]
                }, {
                    name: 'Censo Especifico',
                    id: 'STI San Juan de los Morros',
                    data: [
                        ['San Juan de los Morros', 4],
                        ['Ortis', 2],
                        ['El Sombrero', 3],
                        ['Parapara', 6]
                    ]
                },
                {
                    name: 'Censo Especifico',
                    id: 'STI Valle de la Pascua',
                    data: [
                        ['Valle de la Pascua', 10],
                        ['Santa Maria', 2],
                        ['El Socorro', 3],
                        ['Chaguaramas', 6],
                        ['Las Mercedes', 6],
                        ['Cabruta', 4],
                        ['Zaraza', 7],
                        ['Tucupido', 6]

                    ]
                },
                {
                    name: 'Censo Especifico',
                    id: 'UTI Altagracia de Orituco',
                    data: [
                        ['Altagracia de Orituco', 10],
                        ['San Rafael de Orituco', 2],
                        ['San Jose de Guaribe', 3],


                    ]
                }



            ]
        }
    });
}

function lineas() {
    Highcharts.chart('contenedor', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Crecimiento del empleo por Áreas - Energía sola'
        },
        xAxis: {
            allowDecimals: false
        },
        yAxis: {
            title: {
                text: 'Número de empleados'
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        plotOptions: {
            series: {
                pointStart: 2023
            }
        },
        series: [{
            name: 'Instalación',
            data: [1000, 2000, 3000, 3500, 5000]
        }, {
            name: 'Fabricación',
            data: [1880, 2580, 3900, 4500, 4800]
        }, {
            name: 'Ventas',
            data: [780, 2000, 3100, 3700, 3900]
        }],

    });
}

function torta() {
    Highcharts.chart('contenedor-modal', {
        chart: {
            type: 'pie',
            plotBackgroundColor: '#f8f9fa', //color de fondo del gráfico
            plotBorderwidth: 1,
            plotShadow: false,
        },
        title: {
            text: 'Contribuyentes Censados 2023.'
        },
        tooltip: {
            pointFormat: '{series.name}:<b>{point.percentage:.2f}</b>%',
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.name}:<b>{point.percentage:.2f}</b>%'
                }
            }
        },
        series: [{
            name: 'Contribuyentes',
            colorByPoint: true,
            data: $data
        }]
    });
}

function prueba() {
    //1era forma    
    /*
    Highcharts.chart('contenedor-modal', {
     chart:{
         type: 'line'
     },
        title:{
            text:'Valores mensuales'
        },
        xAxis:{
            categories:['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
        },
        series:[{
            data: [2,3,4,6,7,9,6,4,3,2,1,5]
        }],
    });
    */

    //2da forma
    /*Highcharts.chart('contenedor-modal',{
        xAxis:{
            minPadding:0.05,
            maxPadding:0.05
        },
        series:[{
            data:[
                [0, 29.9],
                [1, 71.5],
                [3, 106.4]
            ]
        }]
    });  */

    //3era forma
    Highcharts.chart('contenedor-modal', {
        chart: {
            type: 'column'
        },
        xAxis: {
            categories: ['Rojo', 'Verde', 'Negro']
        },
        series: [{
            data: [{
                    name: 'Color 1',
                    color: '#ff0031',
                    y: 10
                },
                {
                    name: 'Color 2',
                    color: '#28a745',
                    y: 3
                },
                {
                    name: 'Color 3',
                    color: 'blak',
                    y: 5
                }
            ]
        }]
    });

}