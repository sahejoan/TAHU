<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="es" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    
    <title>@yield('title', 'I-LLANOS') | {{ config('app.name', 'SENIAT') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{!!URL::to('img/logoicon.png')!!}">
    
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <title>GRAFICO</title>            
        <style>
          @page {
            margin-left: 0.5cm;
            margin-right: 0.5cm;
          }

          .page-break {
              page-break-after: always;
          }

          .box {
            display: inline-block;
            width: 4cm;
            height: 5cm;            
          }

          hr {
            page-break-after: always;
            border: 0;
            margin: 0;
            padding: 0;
          } 

          header {
                position: fixed;
                top: -60px;
                left: 0px;
                right: 0px;
                height: 50px;

                /** Extra personal styles **/
                background-color: #ffffff;
                color: black;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed; 
                bottom: -60px; 
                left: 0px; 
                right: 0px;
                height: 50px; 

                /** Extra personal styles **/
                background-color: #ffffff;
                color: black;
                text-align: center;
                line-height: 35px;
            }  


                                                     .dataTables_wrapper .dataTables_processing {
      position: absolute;
      top: 30%;
      left: 50%;
      width: 30%;
      height: 40px;
      margin-left: -20%;
      margin-top: -25px;
      padding-top: 20px;
      text-align: center;
      font-size: 1.2em;
      background:none;
    } 

            #mapa-container {
                height: 800px;
                max-width: 100%;
                margin: 0 auto;
            }
            .loading {
                margin-top: 10em;
                text-align: center;
                color: gray;
            }

            .chart-outer {
                max-width: 800px;
                margin: 2em auto;
            }

            #container {
                height: 300px;
                margin-top: 2em;
                min-width: 380px;
            }

            .highcharts-data-table table, th, td {
                border: solid 1px;
                border-collapse: collapse;
                border-spacing: 0;
                background: #F2D7D5;
                min-width: 100%;
                margin-top: 10px;
                font-family: sans-serif;
                font-size: 0.9em;
            }

        </style>
    </head>
    <body>
        <header>
        <center><h4>Grafico</h4></center>
        </header>
        <div style="width:21cm;">
        <br>
              <figure class="highcharts-figure">
              <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">                      
                <div id="resultados">                  
                  <div id="datosrecaudacion">

                  </div>                
                </div>                      
                <div id="grafico-container">                  
                    <?php
                    echo $graficopdf;
                    ?>                    
                </div>                             
              </div>              
              </figure>
        </div>

        <script type="text/php">
        if ( isset($pdf) ) {
            // OLD 
            // $font = Font_Metrics::get_font("helvetica", "bold");
            // $pdf->page_text(72, 18, "{PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(255,0,0));
            // v.0.7.0 and greater
            $x = 540;
            $y = 820;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = $fontMetrics->get_font("helvetica", "bold");
            $size = 6;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
        </script>        

  {!! Html::script('/js/Maps/maps.js/highmaps.js') !!} 
  {!! Html::script('/js/Maps/maps.js/exporting.js') !!} 

  {!! Html::script('/js/Graficos/Librerias-Higthcharts/pluggins/Highcharts_7.0.3/code/modules/data.js') !!} 
  {!! Html::script('/js/Graficos/Librerias-Higthcharts/pluggins/Highcharts_7.0.3/code/modules/export-data.js') !!} 
        
    </body>
</html>