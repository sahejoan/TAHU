<script>
function hexToRgb(hex) {
  var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result
    ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16),
      }
    : null;
}
function numberParser(value, locale) {
  let number = Intl.NumberFormat(locale).format(.1),
    decimalSep = number[1],
    pattern = new RegExp('[^\-0-9' + decimalSep + ']', 'g'),
    matchs = value.match(new RegExp('['+decimalSep+']', 'g'));
  if (matchs && matchs.length > 1) return NaN;
  number = value.replace(pattern, '').replace(decimalSep, '.');
  return parseFloat(number);
}

function Grafico(_datac,ancho,alto,subtitulo,vr) {
/**
 * Create a global getSVG method that takes an array of charts as an argument.
 * The SVG is returned as an argument in the callback.
 */
Highcharts.getSVG = function (charts, options, callback) {
    let top = 0,
        width = 0;

    const svgArr = [],
        addSVG = function (svgres) {
            // Grab width/height from exported chart
            const svgWidth = +svgres.match(
                    /^<svg[^>]*width\s*=\s*\"?(\d+)\"?[^>]*>/
                )[1],
                svgHeight = +svgres.match(
                    /^<svg[^>]*height\s*=\s*\"?(\d+)\"?[^>]*>/
                )[1];

            // Offset the position of this chart in the final SVG
            let svg = svgres.replace(
                '<svg',
                `<g transform="translate(0,${top})" `
            );
            svg = svg.replace('</svg>', '</g>');
            top += svgHeight;
            width = Math.max(width, svgWidth);
            svgArr.push(svg);
        },
        exportChart = function (i) {
            if (i === charts.length) {
                return callback(
                    `<svg version="1.1" width="${width}" height="${top}"
                            viewBox="0 0 ${width} ${top}"
                            xmlns="http://www.w3.org/2000/svg">
                        ${svgArr.join('')}
                    </svg>`
                );
            }
            charts[i].getSVGForLocalExport(options, {}, function () {
                console.log('Failed to get SVG');
            }, function (svg) {
                addSVG(svg);
                // Export next only when this SVG is received
                return exportChart(i + 1);
            });
        };
    exportChart(0);
};

/**
 * Create a global exportCharts method that takes an array of charts as an
 * argument, and exporting options as the second argument
 */
Highcharts.exportCharts = function (charts, options) {
    options = Highcharts.merge(Highcharts.getOptions().exporting, options);

    // Get SVG asynchronously and then download the resulting SVG
    Highcharts.getSVG(charts, options, function (svg) {
        Highcharts.downloadSVGLocal(svg, options, function () {
            console.log('Failed to export on client side');
        });
    });
};

// Set global default options for all charts
Highcharts.setOptions({
    exporting: {
        // Ensure the export happens on the client side or not at all
        fallbackToExportServer: false
    }
});

// Create the charts
const chart1 = Highcharts.chart('container1', {

    chart: {
        backgroundColor: {
          linearGradient: {
            x1: 0,
            y1: 0,
            x2: 1,
            y2: 1
          },
          stops: [
            [0, 'rgb(133, 146, 158)'],
            [1, 'rgb(200, 200, 255)']
          ]
        },

        //backgroundColor: '#B2BABB',
        width: 450,
        height: 450,
        spacingBottom: 10,
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45,
            beta: 20,
            depth: 35,
            viewDistance: 25            
        },
        /*
        events: {
          render: function() {                  
            //scrollGrafico();            
          },
        */
    },

    title: {
        text:'<div class="logopdf"></div>',
        align: 'center',
        useHTML: true,
        //margin: 5,
        //floating: true,
        style: {
          color: '#000000',
          fontWeight: 'bold',
          fontSize: '15',
        }        
    },

    subtitle: {
        text: 'ELECCIONES PRESIDENCIALES 2024'+'<br>'+subtitulo,
        
        align: 'center',
        //margin: 50,
        //floating: true,
        style: {
          color: '#000000',
          fontWeight: 'bold',
          fontSize: '13',
        }        
    },

    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },

    mapNavigation: {
        enabled: false,
        buttonOptions: {
          verticalAlign: 'top',
                alignTo: 'spacingBox',
                x: 10          
        }
    },

      plotOptions: {
        pie: {
            depth: 15,
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<span style="font-size: 1.2em"><b>{point.name}</b>'+'</span><br>' +
                        '<span style="opacity: 0.9">{point.percentage:.1f} '+'%</span>',
                connectorColor: 'rgba(0,0,0,0.9)',

                style: {              
                  //fontSize : '1.2em',
                  //width: '80px', // force line-wrap
                  textTransform: 'uppercase',
                  fontWeight: 'bold',
                  //textOutline: 'none',
                  color: '#000000'
                },
                rotation: 0
            },
            showInLegend: true
        }        
      },

      tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
      },

      legend: {
        enabled: true,
      },     
                 
      credits: {
        enabled: false
      },

      exporting: {
        enabled: false // hide button
      },

      series: [{
        name: 'Porcentaje',
        colorByPoint: true,
        colors: [
              '#B0020D', '#999393'
        ],
        data: _datac
      }]
    }, function(chart) {
        chart.renderer.image('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAzcAAACSCAIAAAD3iqNNAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAC4VSURBVHhe7Z39cxRVvv/9I7a+WoU/xJKiKCVf0RQqVZHaIMi9przorZTrAt+QyOaCaOCbTSCGC4tAFi4XKoIEc7NW3BQpVhEV2Rh5EkFF5WkRXDDKUzYhCQ9BSMhzMkPuZ+YcZoczT326z+np7nm/6l0pmHSfc3qmJ+fV5/TDPSMAAAAAAMB5wNIAAAAAAJwILA0Al9BzcKTlHgRBEMQ76Sjmf+FjAEsDwCXc+nTk4j0IgiCId3Ll//G/8DGApQHgEmBpCIIgHgssDQCPAEszl6bgz56D/G1MCC3JVmE/EQRB9AWWBoBHgKWZC8lWM/2h8/G30Qi0vFAIgiCIjsDSAPAIUpZGanJjs964ZaiJ2pno/FwRWl4oxIZQO1v1f2puTwvGOBFvxYGWdr3j1vfHzu2pP1zzdn35striwsr83P966cUVL7ywLDwzZ6x6paBicVEVLUNL0vJnGy8NDckcEAPgJWQtTTfNLukvqZHGpzsZtLxQiD1J9PcajLT/CywN8VScYGn9/UPfHPih4o/vzfjtykeemH/fw7mU/xP8ed/Ds4P/CPwUQi+yxe78N/Dz/vT8p6eUkNWtXr5l76dHbnX28joA8DyyM566ccu0YGC6UxbfSOsDSbABWFpCyNKENw1BXJ0kWtrJ4+dXLv3z9OlLR4+fE1SuQELWZTGstFHj8p5/fmnN2/XXO27xWvXz0XsHFszb6I0sLqriWyXP2cZLQmkGU1ZSbefnZTOb1m8XtjdWaEfi6xgElmYuNzbzBksxfCUJGwhLSwgsDfFY7Le0zpu91W99kjW5+N6HAjpFP4NGxdUqpFnWc6fkQNLS8wteXkfdHtXO26GN/1z8J6El7s3YjLl8q+T55sAP5j5QWmvMYwW0Oi/IW7zwwjJhe2OFdiS+jkFgabJpCoZ8yxwdxbwEoVh9gaUlBJaGeCx2WtoP319cXlYz+tECZk5Cn6Q7rFLq/qnzu9x+g7dJA7A0BmmWUJrBsE+KqqYdhpflIWBpDgoJFnXqphlotFXRKLC0hMDSEI/FHks7dujH7Owy1vdQB8xG0WxOqFJqwOjxc1Yu/XOXnnE1WBrDtKWx0Mf0yBPzmy+aHedwKrA0Z0X2ugGB1gfEArUGlpYQWBrisdhgaXvrj1CPK/RAyQ21h5I1ufhWZx9vpTpgaQyLlkahz+ilF1fw4rwCLM1Babpn5PYAb605bmwWy9QaWFpCYGmIx6LV0nx+/6b129PS851maRQmak/9uqi2uqG7u5+3WAWwNIb1sTS222x5Zxcv0RPA0hwUK9OdDJsnPWFpCYGlIR6LPktrb7vx/PNLHehnkXnyqYX9vdYOqcOApTGsj6WxjBqX9+nHh3ih7geWlvyQV7FYnO5ktN4pM7wKTYGlJQSWhngsOixtcHC4trrhzp3PXGBpU59ZxJuuAlgaQ5Wl0S6Ulp7/3Zd/5+W6HFiaU9L6gNxToWLR+b59w2mwtITA0hCPRbml9fUM5OQsD01XyYatGAz7d+7o8XPSH59HIWOg3vruZQIJX1027A4gmys+4q1XASyNocrSWKgl0vcPcySwNEeE2mb6BhwCt3thaQ4CloZ4LGotretmb3Z2mRV/GjUuj7qxlUv/vH3r/jOnmsj5eNF3uPlL94mj5+pqdhW9umlCZmFQ5sRCjIcaSTW2NF/jpasAlsZQaGl3lH12+bJaXrprgaUlPyRVsg/ujA+7cZpQi47A0hICS0M8FoWW1t8/lPPvfzAhZ6wDHvNYwZo36tpar/PijEGCtfXd3SE1FEqOH9b3S/eFiYClMdSOpYXyTuVOXoE7SS1Ls22QSSrUqoFG3kgl9J0Uq9AUWFpCYGmIx6LQ0qo27CDpCd7xX+xv4oeWJ0VrPN3MCzLFuvKtspZGoVWUP4wIlsbQYWn0ed3/8Gy1Y582k0KWRjLkTEsjfVSOPQOHsLSEwNIQj0WVpTWda6cenTpRFqG/iRq2GCla1uTiM6eaeEEW+Orzk09PKaEy2Q1sjTQjO7uMr6wOWBpDk6XRz+nTl0ZOhbuF1LI0oUYnhFqldrqTYc/TomBpCSFLc+aOhyDmosTS+vuHpj6zKOhnYk8TP2np+eXLantuKbu1rM/n37Dm/dDj24XqhFBr1V43wIClMTTNeLLkzVrj8/t5Ta4ihSxNqM45UTvdyeg7aYcckIIMXtAefdweEOtSnrYxsDTEU1FiadSdGLGi8NDCo8blffbJt7wIpdzq7CsurCQJi98k+m3TuXa+jjp0WNq9D/3zCVd2xpmWxj7Zolc38ZpcBSwtyQncgEMPrU6d4ZWNPmxwWSofloZ4KdYtrebt+vvlH81JvWzFH9/jRehhc8VHVEscUXt6SglfVCnKLY1txSsFFVSyzbFyTaXWsTQKvSdrV9bxytwDLC2Zof67833ePOXYeeM0rdGHbZdZIIhnYtHS9tYfoe5EdqKT+lcyJJ9P+4wVGSRTHEpkGzT18dS5CnVZD7X2h+8v8gpcgm5Lo9COlzuzvKOji1fpBmBpSQspVLPlB3fG4XYvv4bA7a6mD1gagsjGiqX5b49kTS4WuhYjIefYU3+Yl6KZ1cu3RFoae+XYoR/5QkrRYWkUWFqsPPvs6wof8KUbWFpywsxJx3UD4dhzdpru6AOWhiCysWJpO7d/KdiPkdwXfG4mGZ49UEUvvbgisp1jM+ZqOv0clsawzdIo9BEPDg7zip0NLC1pIX/Scd2AgAeuMdQHLA1BZGPF0ubkrZWf6wz8XDj/LV6ELZDfCKfNUTOos+S/Vg0sjWGnpVFqqxt4xc4Glpa06LhNWiSd74v1ui76gKUhiGxMW1pfz8CYxwpkx9JIj2gV+zvUmTNWCS1ZufTP/HeqgaUxbLa0sRlzLd4Y2R5gaUmL7ulOxvAVjKXFBJaGILIxbWnv1e4VOhWDIUs7fVLBPWyluHjhalp6PlUdGvw7uOcE/51qYGkMmy2NkjHxtbaWDl69U4GlJSf2THcy3H77e33A0hBENqYtbcZvVwqdipGwsbSknOtdVlLNGkA/ydgG+gb5L1QDS2PYb2n3PpQ7Kev3yh/5pRZYWnKi7zZpkfQcFGt3V/QBS0MQ2ZiztL6eARIdoVMxmDGPFfBS7KWlKTScNlvfSWkELI1hv6WxDzc7u8zJogZLsztNwdCbYx++wDlw7p331AcsDUFkY87SPm84St2h0KkYCa1l5Xb2Ftn67m7Wka8r38pf0gAsjZGUsbTg55s7+tGCQ1+c4u1wGLC0JKTlnsDNzOzkxmZYWhRgaQgiG3OWVr6s1pylUagH5aUkg9yZ5VOfWXTsG41nqMDSGPZbWijsYMCZ7xgsze6QLZEc2IyrryHQBywNQWRjztLyc/9L6FEMho12DA/7eEFeBJbGSKqlBXYzTU8Aswgsze60jeHtsZnWiJa4JfqApSGIbMxZ2pSpi8yNpbHus6XpKi/Ii8DSGMmyNHaBCPuH1qltc8DS7M6Nzbw9NkP1Uu1uHFHTBywNQWRjztLYafhCp2IkzNI+/fgQL8iLwNIYSRxLY6E9jfa3Le/s4g1yBrA0+0KGRBm+wttjM+6d9NQHLA1BZGPC0i633xBu5W88zNKKCyt5WV4ElsZwgqXRz1Hj8hx1VOAUSyOB0I1Qo/2hbUz0B04vbnxalNYdA5aGILIxYWknjp4TuhPZpD8+zy1PXTQBLI2RdEtjIVdLS8//7su/82YlG42W1nMwcFcw49GNUF1SQlqQRGQ/EYdEHwONYl0IgsTP9Tf41ycGUSzti93Hhe7ERLa+u5sX5zlgaQznWNp9wedHbd+6n7csqWi0NAAAAClGFEv77JNvhO5ENtRxTsgsTMoTCGwAlsZwiKWxBF1ttr6HtxoHlgYAAEAVUSxt+9b9QndiItRlOu20blXA0hjOszT6mXv8W7se5hgDWBoAAABVRLE0089ZFzJx0sKum/beENwWYGkMR1laKE9PKelM6l4HSwMAAKCKKJa2Y9tBoTsxl/sezqUey+HPxjYBLI3hQEtj857Tpy/t60nabDssDQAAgCqinpf2rdCdmAvrMtPS8z029QlLY0hZ2tiMubQzCC/qS+7Mcp/fzxtqL7A0AAAAqohiad99+XehOzGX0LOx6efioqr+/iFegcuBpTGkLO2dyp3TppUKL+oL7XIL57/FG2ovsDQAAACqiGJpZxsvCd2JkkyZuqjxdDOvw83A0hhSlvZe7d5r17omTlpo24gaVbR6+RbeVhuBpQEAAFBFFEvr7x0QuhMloV4zLT1/w5r3h4bc/Sx2WBpD1tJolaZz7Y88MV/4lb7QLjdzxiqbz4yEpQEAAFBFFEsjgmcRiZ2KklCxrxRU8GrcCSyNYcLSCCX3eZFK0aubWNX2oM/SWpqufvDBd8bDV9OGUJ1tOXOqibfAAMcOXxBWp1xu7+S/Vkd//5BQi2PDW6yBa9e6hLoQBImfb776mX9/YhDd0qY+s0jTzBQr9smnFta8XZ+s87stAktjmLM0+tDn5K2l3UDTDhaZUePy9tQfZrXbgD5Lo+/zPU+WGA9fTRtCdbbli32neQsScfv2yKPZ5cLqlNVrd/IllPLk82uEipwZ3lwNHP7mnFAXgiDxM/vVd/j3JwbRLa3o1U1Cj6Iw7FHu1ElPmbqIenpepXuApTHMWRrR3z9UvqyW5Mk2Vxs9fs7hr8/w6jUDS9Masi5yL4N8eeBHYXWWB6e+4fOpP0SsqTkgVOTM8OZqAJaGILIxaWnUp9rQfbLLP/NmrTnbeIlX7AZgaQzTlsZg+5g9lkYZmzFXaqbMNLA0ranY+Bmv3gD0509YPRTjA3LG6brZe09mqVCRA8ObqwFYmsZklloZrKXDGzo4EV5EnBCTltbSfM2e7pOJ2qhxecWFlaf+dp5X72xgaQyLlkZsrvjINkujijImvkY7Nq9bG7A0jcksvXrF6CllJ47/I44z/W5BDV9OKQsW1wkVOTC8rRqApelL9qwNsn8BwkOHN67YOVMwJi2NmDJ1kdCp6A4Z27RppXvrj/AWOBVYGsO6pRH0ZpI/2eZqk7J+39HRxevWAyxNX+gd4HUnoq9nIMHIQWapjieJHTt8QazIeeFt1QAsLUEsDLWuXrtzoG/wV1lLhdcNJbP0cnvnjh1HxdcNJ1CvG8aJ3Rjzlrbxvz8Idp9i16I1rMPOzi7b/9kx3g7nAUtjKLE04pWCCtv2NLZ3ab03ByxNU7JnbeAVG6Cu7mth9cjU1BzgSyvF+dcQ8IZqAJYWPxUbPxNeMZ4vD/xI7/CyFduF141kxn/8D61785du4XXjoS+LWy6OcV3MWxpJQ/DhAWLXYk+oN5Xtw2wDlsZQZWnd3f3pj8+jT1xYRUeCOjj7kSfm63siOyxNUzZV7uYVG4C6JWH1yLCuSzmr1+4UKnJaeEM1AEuLn/M/t0/OWSe8aDBsEuCbr34WXjeS0AFJ1EueEyeztL93gA6TxNcRFTFvaf7bI08+Zd+d4oWwep97bkltdYPTntcOS2OosjRiT/3hUePyhFV0hFkaHXvMyVur6UYwsDRNoR6OV5yInlt9RiaGaBkdT+V3/qQnb6gGYGnxc/OXbtlvMcuDU99g7/Dg4HBgTCuzlPZe+gc5Hx1s3LW3Z5bmvFxJr9Nv2RUD9NvQ+bi/W1DzzyUNp2TJX0yviySMeUsj3qncmayxtPCMfrRgeVnN5fYbvFnJRrmlMXVIZUsjtr67+45CaT8wYLUUF1byupUCS9MR5dOdLPSW8nWU4vC5Id5KDcDS4oRsid6i27dHEg6n0ZILFtctW7G94s2Gqqp9tJeeOP4P9g4Twk1kIsduSaf6e6Mffly71nXmVBN9THt2naJia2oOiI2JOPmMGsOG8Zw/SOzSWLK0gb7BjImvCV1LUkJ96ujxc6hXc4KrabK0xUVV61dttTktTVf5Vsmj1tKIE0fPTcgspLdCWFd52BtOoXeA160OWJqOSOmU8UklKfkzjsNvnMZbqQFYWpzQbsnepdCs5YNT38idV81UTNAjMi0yKrZ8LKiDjjW+RXW1XkrQV7a1Xo88MYCM4fzP7fX1fyMno8KpnNCZBsYPfhCpWLI0YtuWfXe6NLGPSUqYq9lz46tYKLe0JIZMi2+VPMotjTj/Uys7R40ilKA8wdMuZ9dWN/C6FQFLUx46mu/vH+K1JiJwA46IEuLEyoFKLLpu9lKbhYqcE95KDXjf0ixc5xjeGZ9tvHTzl27278uXO6OeLka70LIV22PJFjlc/KMRUkA6WhgcHOYr3A0pV6xddMHiOr7Q3TSebqajGnM7ds7LlcIrSChWLY1Yv2qrPb2mkZAsspY899yS7778O2+ivcDSGDosjThx9NzoRwtsOCpge/WocXl//fBrXrcKYGnKQ30Vr9IAsjeFWq3naVGxHnvghPAmasDzlrbkD9uMTmcHb0I7v7i24s2GHTuOXrxwNeozM0joEwz9ZpaSUfGlwzDoPVQ7X+EOQ0O+hOuuWv0xXzoa5Je0e2+q3G3wegL6Su7ZdUp4EQlFgaURSxZVU6fikOG08OTOLD//UytvpV3A0hiaLI34YvfxtPR8e/Y3EjWq69AXp3jdloGlKY/x6wZM3P3/walv+P2Gnzklg2PHD3j7NOB5S6MDhuaLV4yMJ9EyZEihAbOo9PcPGREdUj2+QhgGZ9VPfd/CVwjDiGjGv6SavpKBw6G7v2v0VYr89pGD0mbK/u1KqaixtFudfaPHz3HIcFp4qCOnhn1/5CxvqC3A0hj6LI0oK6kOjnWJ5egI1ZIx8TVesWVgaWrzaHY5r88A9fV/E1Y3Enpj+fpKcezZabx9GvC8pbHZwMBpZBG/iirl5Gq0d8U6CjB4+zSqjq8QRkvTVWGxyJCN8aXvhrRPWDIypFx86QgCf4iiHQvN+I//ISkUZm9pl+CrhL2IhEeNpRG11Q0OtDQKtWr0owWb1m8fGvLxtmoGlsbQammdN3ufnlIiFKIjTARpL1L1MFlYmtpone5koX4l1uk7VjDSjyYlvH0a8LylsWGtgb7B0J5GHka7aOPp5pIlfwktJoRsaceOo5EP+Dd48zN2P1u///bqtTvJ2Kh2tnrCITG2IkFHL9mzNoROwTRy/BD1gbl1dV8HKo01XJ1ZevJEM3XEe3ad+t2CGnpn6GdoRXFh5E6UWRqxdmWdU0Ut0MtSp75tyz7jZxmbBpbG0GppRFtLx4TMQqEcTaH9J2Pia22t13ndFoClqY3W6c5QqBvjpSjFmZOevHEa8LylhXeoZB6027DRASO+FfU6lcS3X84s7e7u77nVx5ekPTyzlHSNerr4ssWuX758uZPazF4hbfpi32l6kY5IQ4tFzYNT3wi5YDgJ92dyuPADnpCYOnZc2QlRaWlE+bJa6l2oS6Pc+5DY6yQr7GI9lqzJxbpvPAZLY+i2NIL+mkzK+j2tTp9seGmaQjtP/PNIjABLUxjW0xjESk9AXSAvRSnmZmB1hzdOA563NNIUvqlhkDBFvUhTSGi2h/aKpnP82CMgTHEPLSbnrKPj1chhM6ox1swjS8WbDctWbCczu+v1zFJygosXrgbOIQt//e5Qyax5tGTJkr+wWUsiznhhKFGvPKDNDNw0xOxBlLej2NKITeu3h5RI6HWcEGpVWnr+lnd28eZqAJbGsMHSCDoyK3h5nT07G9Xyb//2nxbvRw9LU5hQb2GEhBNA8ZJZ2t52kxekjuFhX/zuMCnhjdOAhy2NbIkOA6I+WY68RFg4MqHTvEL3hqUCSdd8Pn/8aXo6UBFNKzxxvCf2r6jAOFpJDeu62VtVtY/+wV6h5U+eaKbGGzqRLjjvyTZWgIqlTSbVi7dFqRf1lkZ89N6BsRlzHWtpbAJ09fItvLmqgaUx7LE0gkQtJ2c5FaJ7+Jbt0jNnrLLy8ChYmqrQn3LjJzBYfy6T1HNCjRPqlZ0T3jINeM/SyFQq3myIpR3E0JDPiHOwUeHIyw7Ilpy2h9AmR24RvXL+53Yjf4Joyfg3n29pukrvg5E3LUWixdKIvp6BshJ2ew7Hutrs8mVRLmC2DiyNYZulEXQQNm1aqV2XfM5eMG8jr1geWJqq2HDdQHiov+RlKeXiBcddQ8BbpgHvWdqTz6+hXaum5gB9jnwjI/hi32lhrcj8bkGN28+gf3DqG4k3IbM0dMlCOFevdNK6ufOqIWeR0WVpjFcKKoSOxzlh+nj4qzO8reqApTHstDSi8XSzbZZGFe2tP8IrlgSWpirffPUzr8kASv76x+mJrWBpKlZDeLM04O3z0rJnbQh/nmY41NEKCwshQYk3O+mSJPyWxRKOJX/YJiwpJu7UrbfdTq+lDfQNFhdWsoEroQdyQqivzZpc3N6m+NGfsDSGzZZGrCu37zEYEzILzV3yCUtTEuFisfiouoKMyuElKsXIeUt2hjdLA962tEAyS/mmjoyE3wWN/B6jRPQOsKsiOoPnn9FuH3q8VcKhbjLgwNckwtWozD27TsHSrPLpx4fSH58n9EBOCDtBbcxjBWpvewtLY9hvacTioip7LI1qeXpKSfPFK7xiw8DSFCSz1PgNOFqar8U7EJfJ5DvPw1aL0yY9ebM04ApLi3LlYzD06ZMQJLzaI3SLiqqqfbTw/OJa+mK2t900Mu/p7ZBmVWz8jHwr9H3MebmSuWzgAs+7FxaTWdrR0UX7T/hlDVQUvbGeV387LI243H5jxm9XsnGOYMQOKbmZOGlhd3c/b6tldFiaPeYRHvZJuc7SfH7/nLy1rPFCFTqSlp7/2ceHeN3GgKVZT+hmmEaoeLNBWN1KGk/HPE/cCo66cRpvkwZc0aGuXruzrfW6cKXhgsV1/b0DO3YcDb0SK9euddGWBh5MefexwZPPr3Ha1LYTwsanE84IU9iS1FPTZ0HvJLsAll5x2lC08thkaYzvj5zNnVluv3AkDDWprKSat9Iyyi2NOQe9dQvmbbQ5Vm64nxRLIwYHh8uX1d6fni9UoSP0uYweP0fq7EZYmvUYPyONrF3trS6kLlkwjpHu37bwNmnAFZb2aHY5G+C5eOFqdvA+F/Tp0H+HhgzdNoXWcvKj9B2XzFJ6x4wcpdBnEfhUggwP//NJQnFuGuKN2GppjE8++PKRJ+Y7ytXuezh31Li8k8fP8yZaQ8dYGkX3zXiVkyxLY1CBzG6FinRkbMbcM6eaeMWJgKVZjNS1lsqnmajPDt19VCEGDcCe8DZpwC2TU6E7a/h8/tDppwbdiw4hPO8NajM5Z11gGjTidTGZpVevdLLPIsT5n9vFxTyXJFga0XWzd3FRFYmR0C0lK8ET1HKnT1/K22cNWBojuZZGbK74yB5Lo1oyJr4W9QEvkcDSLKYq2rOlY2FkJkU2e3ad4qUrxTkTN7xBGnCLpdFnwVscRuLrEIMh5xBeQZSE3LetpYN/GHe4erXLkOG5OcmxNMbWd3ez0Q6KQx4nZWWCLwQsjZF0SyOys8vYDiZUpyMzZ6zitcYFlmYxxq8b8PtvC+cGKUnJkr/wCpTiHIPhDdKAWywt6mUinrcBJ+dXWUtjPfzD5/N724yTaWnE/l3HX3pxxahxefb0o/FDbVByn1tYGsMJltZ4utm2x2BQLXTgwSuODSzNSp58fg0v3QBGHnFtIg/eeZiPWoaGfA45u5w3SAOusDT6fKOe+HjxwtXADSM0eD8SPzkvV8Z5ugNx85duHaPmDkmSLY1BXSm5GnVyLEJfZVuoaurRey1f7AlLYzjB0ohjh34cPX4OVcFuvBJeqdpQ4XS8saf+MK84BrA0K5Ga7pxfXCusripR759uncAtORwgAbw1GnC+pWXP2hB58lM41FvhUk3b8quspfX1f+NvfSJOHP+HJ8c7HWFpjM8bjmZNLk6uqFGsP4gdlsZwiKURZE5svFa3pVHICKvf+iTOgz5haeaTWUoHzbz0RPTc6tNnPOR/vBrV6DNL4+FN0YDDLW312p3s5g7xGRwcXrZiu7AuoiMPTn1jcs66UMiPl/xhG/8YgjfZIY2jCGt5LA6yNIK+ISRJjzwxn7oo1uexhHddumP9GgJYGsM5lkawkyCpIn1nQIbvscWFlbziCGBppiPlRrLvhlwyS1uar/GalOIEj+FN0YCTLe3R7PIFi+tC+d2CmtCjJqhjynm5MnvWBvoZ0gVMfSYl5GT9vUPsc0mRQU1nWRqjr2fgo/cO5Pz7H1iHJ3RdunP/w7PjP7E/IbA0hqMsjThx9NyTTy0M7lRi7QrD5lXp59qVdbziu4GlmQ718bxoA9TVfR3e6SqPpqdFEUnve3g7NOD8Gc/wkBCwoTVNJzgi5sLmQAP3DY74lSfjREsLsX/X8SlTFzFXs+0iUKpr2xaJc18igaUxnGZpxPmfWtMfn6d1X6LCmQXSjlTzdj2vOAxYmrmQu/ByvQ75n7DtNoe3QwPusjTKF/tOU7MN3oMDsSe586qHhnypc1M6R1saMTzsy8lZTh2e0IHpC9Ul2zsKwNIYDrQ0oq5mF33EWofTQklLz4+cF4OlmUvUW1h5ksA1BBGbb2d4OzTgOktje91k3ALNSflV1tIzp5qEFz0cp1saMTg4vGn99jGPFQh9mKZQ//3Siyt43aaApTGcaWk+vz9v1hoSNd2js0EXnE1O1t/Pz6JgwNLMRWq60+0kd9KTN0IDrrM08rNbnRovQ0HMxQkX2dgWF1ga43L7jYo/vsceLUUR+jOFIUtLf3wer9UUsDSGMy2N6O8dmD59KVVqw4ga7atz8taGX/IJSzOR1JnuZCR30pM3QgOuszTK6rU7hVcQxM64xtIYdFhTVlJ9v/4J0KZzRu9vHgksjeFYSyM6b/Y+PaVEq+6zsIOKxUVVvGJYmqnoO1XfmdD+mcTxG94IDbjR0jCQhiQ3LrM0xndfnZ44aaHQpalNbXUDr0weWBrDyZZGtLVen5BZaIOoUaiW7Owy9thmfZbWeLq54s0G4+GraUOozkrIWnihKQM5t/Am2BbeAg20Xroh1IUgSPwkvK+vEy2N6OsZKC6sFHo1hQkf/JAFlsZwuKUR539qzZpcLLRER5gLUl3XO27pszQAAACphkMtjfHJB1+GzlRTOyiSk7Oc1yEPLI3hfEsjBgeHC15ep3bniRWq5bnnljz77OvC67ECSwMAABAfR1sa0d3dz54rJfRwFjP1mUW8AnlgaQxXWBpBu9DYjLlCe3TkvjuDagYDSwMAABAfp1sa0XzxysRJC4NdoNjPmc6EzEJeujywNIZbLI349ONDwQd9iq1SHlgaAAAAhbjA0oi2lo6gqCmb94SlWcdFlkbUVjcoH5G1GFgaAACA+LjD0oizjZcU3vkWlmYdd1kasa58K7XEhhE1g4GlAQAAiI9rLI2gnl7VcMi0aaW8UHlgaQzXWRqxuKjKOSNqsDQAAADxcZOlEdnZZUrGQmbOWMVLlAeWxnCjpfn8/jl5ax0ynAZLAwAAEB+XWdrnDUeVnJ1mpYOEpTHcaGnE4OBw+bLatPT8pA+qwdIAAADEx2WWRjz5VOAyAqHDM557HwpciLdtyz5enDywNIZLLY3BZs+TK2rSlrbnA98D6b40Y3kgna+lDbFGmYzM/Q0vxQjf7hZWT0Lo/Zw0kTaaN0ktBz6R+GQtRuuO8cN3YnUIgsTNyOsF/OsTA/dZ2oJ5G4XeTir3BW+XYEWJYGkMV1sakTdrDdsZhKbaFjOWFvENjxO+ljaE6qQy8qHMtm9eKayenJDffLubN0ktPV32WZrWHQOWhiCSUWNpO7YdpF7WyhPKFVLzdr2V84qoV05Lzx/oG+TFyQNLY7jd0tpaOjImvgZLM41QnURId6618lKM8OKzYglJitwQoBRzfyPUpS+8Rh3A0hBEMgosraX5GvVklOVlNfylpHL820YrPSutmzW5mJdlClgaw+2WRjSebh6bMTdZI2qpaGkPBBVNynUunhELSWqoPbxhamGTnraMqPEadQBLQxDJKLC0NW/UBS3N0lOVFHLtWpfQ20mFtqXg5XW8LFPA0hgesDTi2KEfR4+fA0szgVCd8cjNG25eaY+7GAy1hzdMLcODI5MmCnVpCq9RB7A0BJGMVUvz3x5hs0IsLc3X+C+SipU+lXSz+q1PeEGmgKUxvGFpxN76I8HnR9ktaqlpaYFz8MlIDOL3jfxrllBCckPt4W1Tjl0+yqvTASwNQSRj1dLONl6i7oQ6MHZpZNWGHfwXScXieWmn/naeF2QKWBrDM5ZGbH13NyxNFqE6g5Ebi3Jkr69r0rPtAiwNQVItVi1t25Z94f3KlKnJn/Ts7x0Ib5Js0h+f57/NizIHLI3hJUsjJmQW2nyCWipamux1A6uLHTXdGQhtgqZJT8KWawh4XTqApSGIZKxaWvmy2vB+hbqxttbr/HdJ4srlG1a60tyZ5bwgs8DSGB6ztP27jlM7rQzTyiYFLS3h3yMRh013smi80vPDPwl16QivSwewNASRjFVLy5u1JrxfsX5Sl3VOn2wybWm04qb123lBZoGlMTxmacT/n/8W7SEsQvt1JLUs7YH02/83Q24g7Vqr4wbSgglsSE8Xb6Ra2KSn5q3mdekAloYgkrFqaY88MT+8XyFLm5BZODg4zH+dDLZv3R/eJOOhrnfUuDzrF0DA0hjeszSf388OS2BpRhCqS5AH0kdWS94Bx5aBJXMZOaDtYHXub2BpCJI6sWRpTefaI7sreiW5w2llJdVCk4xnxm8VnFACS2N4z9KIvp6B6dOXwtKMIFSXIGRpP3zH1zQIu5mtI4fTpKdujaP/aVG8Ih3A0hBEMpYsjXrWqJY2NmNu4+lmvpDtTMgsFJpkMNTy/Z8d46VYAJbG8KSlEZ03e5+eUiK0X0dSytJIufhqBrHz0Zby0Tjpqf/GabwiHcDSEEQylixtwbyNUQcV6MXR4+ckxSoO7jlBtUdtVZywVVTdlReWxvCqpRFtLR3BSz71jqiliqWRbD2QPrKzlq9mhJ4u0iCxHEeFtkjfpCe7cZo2SeW16ACWhiCSsWRpsToqJj1P/bqou7ufL2oXOTnLTfSdbJVdOw/zUqwBS2N42NKI8z+1pj8+T9gKtUmdsTTpkSfaUgcPpAVClqZv0lPzNQS8Fh3A0hBEMuYt7eYv3bFuH8VepJ8b1rzPl7aFs42XqFLKvQ+JTYofWuWRJ+bzUiwDS2N429KILe/sErZCbVLH0kaK8vg6BlldLJTguJClTZrIW6uDojxYGoKkQsxb2uGvzgidSmTGZsw9fbKJr6CfVwoqmCDKhtbaXPERL8Uyyi2NqScszWn4/P5N67enpefLHhUYjLstbXhQqC5mJKc7b98ese2JlhYz0naBN1o5bRf0zfnyKnQAS0MQyZi3NOqfhE4lMuQW6Y/PO3PKDlGrrW5gNiO0wUimPrNoaMjHC7KMJkurq9lF3mNzyMX5VslDqwsbEidutDTGpx8fGjUuT9gcJXG3pfV0CdXFivR0J/X0Dp/uvJORDyU/QSleL9D0PvDydQBLQxDJmLe0mTNWCZ1KrIzNmPvV5yf5anrYvnW/iZ6S2c+YxwrUDvgpt7Qkhj47vlXypIilEVve2cX2JWGjLCZFLE36NmnOn+68E+kLV6XQZqu8fB3A0hBEMiYtzef3U/8tdCqxQr0XKdTalXX9vQN8faW8U7nT3GDGfQ/nTpy00PptbAVgaYzUsTRiXflWYYusJ1UsTerZ5PrvQ6EyD0g+llQWPQ/I4oXrAJaGIJIxaWnfHzsnO3JASjR6/JzyZbW96i78PPTFqWnTSqlkoa6EYdc9jHmsQMd93WBpjJSyNMLK7ZSjJhUsTXq06dvdQgmODlma1knPD/8UGE5TPaLGC9cBLA1BJGPS0oyclCaEWR39TH98Hq3edbOXl2WKy+03rDxUkVZJS8/fv+s4L04psDRGqlmaz++fk7dW2C4r8b6lSV43EEDbyVhaQhuoddKT3TcOloYg3o1JS7PeG5GrmT5ZjVYc81iBUKDxkKLd+1Du4qIqXpxqYGmMVLM0oru7n/ZM2rtMHDlEJiUsTXZCcNJEN1laMPRW8MbrQMNjPXnJOoClIYhkTFraxEkLrfRDtC7Ls8++XlZSTT302cZL/tu88Khc77hFvX7Vhh0vvLBs1Lg8trpQrJGwFbOzyywO5sUBlsZIQUsj/vrh12np+eZ2TiGetzTpcaaLZwIrus7SNit4OnBM2KRnRKVWwkvWASwNQSRjxtLYQ9at9EPsTLLQ+WTBs8RyqW/Lmlz80osrCl5et2DeRpbcmeXPPbfkkSfmB2tkjsVWkW4Aq4X+kZOzXJ+iEbA0RmpaGnH+p9ZJWb+njbLyHaF41tLIKoJiIT3dyZ6MJJTm/Gi9hkDDw7J4yTqApSGIZMxYWm11g9CduCJM7Ipe3TQ4OMy3RA+wNEbKWhrR1tKRMfE1WFr0BC0tcGt+qalAv0/TJY02xI4bp6nzV16sDmBpCCIZM5aWO7Nc6E4cnqCf5T733JLj3zbybdAJLI2RypZGNJ5uZg/6NO1qnrU0ClnaD9/xJQ3i5g5+ZO5v+FbogN4ZpUOMvFgdwNIQRDJmLO2j9w6MeazA4jiBnUlLz1+/aqvP5+cboBlYGiPFLY048vWZ0ePnwNLEkKLJPriTcM/NbKNE66QnoXSUkZepA1gagkjGjKURLc3XZvx2pZNFLXQSW3Z2mZ3PEiVgaQxYGrG3/sidi13ETU4YL1vat7v5Ygbx+9x0M1shbIZX9iQ8KT78k1iphfAydQBLQxDJmLQ0RnFhJet7nKlr1KqZM1bFv3RUB7A0BiyNsbioijYwKGpyXxMvW9rwIF/MIOzqTpeGWVqiP7WWaLsgVmohvEwdwNIQRDKWLI0EaMe2gy+9uCItPV/oYJKeCZmFtdUNCp+hbhxYGgOWxujo6Hrq10WkaPc+JG51/HjV0sxMdyodK7I7zNImTbyt9YhR3aQnL1AHsDQEkYwlSwvR1np99fItd+6XERgwMDG/YyWsXsro8XOKCysP7jmh+0LOOMDSGLC0EC3N17Kzy0J7qbDtseJBS2O+IjvdSbz4rFiU60IbLnvBhBRMZFVcRsAL1AEsDUEko8bSGENDvr31R16bu4G69mBvJPY6mkJ1jRqXN3360trqhludfbw1yQOWxoClheO/PVL06ibaV42PqHlyLG3kX7MCJ5lJceATJfKR9Oi9va26p0XxAnUAS0MQyai0tBA+n//Mqaat7+5+paCC3TUqIgG1kp0ACq3IQmY2bVop9Xx1Nbva227wuh1A07l2EhRv5PBXZ/hWydN5s1coLU6uXnbQJ6gJn9//3Zd/FzY8TmhH4msaRNbSvt1NAkRrBbKzNnBPr4RhC0cNFUUFhkJqFVEjJTDdGb6Ygeh4VGUSEpz0FDZNcVYXq7E0Kir0sQo7QNSEFo6a8Ba6euYaQZIRLZYmQAq1t/7Ixv/+YHFRVe7M8qzJxexGHhTBw+IkLT3/kSfmP/vs66/N3bBp/faDe050dyV/2AwAByFlaUJ3Tv810sHHWiyytPD/hsdILUJiVeq62LAh1suPLCF+mQk3KrRA/MUQBIkWOywtKr3d/U3n2r8/cvabAz/sqT/81w+/fq92b3i2bdn32SfffPX5yR++v3i5/YbPb9PdzgBwK5JjaQiCIIjDkzRLAwAoBpaGIAjircDSAPAKZGmYVEIQBPFQYGkAeAVYGoIgiLcCSwPAK/R0jVw8gyAIgngniR4BDEsDAAAAAHAisDQAAAAAACcCSwMAAAAAcCKwNAAAAAAAJwJLAwAAAABwIrA0AAAAAAAnAksDAAAAAHAeIyP/CxCUlk7uDW4HAAAAAElFTkSuQmCC', 
        165, 7, 120, 30)
        .add();
    });


const chart2 = Highcharts.chart('container2', {

    chart: {
        type: 'column',
        height: 200
    },

    title: {
        text: 'Second Chart'
    },

    xAxis: {
        categories: [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ]
    },

    series: [{
        data: [
            176.0, 135.6, 148.5, 216.4, 194.1, 95.6,
            54.4, 29.9, 71.5, 106.4, 129.2, 144.0
        ],
        colorByPoint: true,
        showInLegend: false
    }],

    exporting: {
        enabled: false // hide button
    }

});

document.getElementById('export-png').addEventListener('click', () => {
    Highcharts.exportCharts([chart1, chart2]);
});

document.getElementById('export-pdf').addEventListener('click', () => {
    Highcharts.exportCharts([chart1, chart2], {
        type: 'application/pdf'
    });
});

}
</script>