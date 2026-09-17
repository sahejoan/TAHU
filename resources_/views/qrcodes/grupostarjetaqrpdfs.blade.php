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

    <title>Lista de QR funcionarios</title>            
        <style>
          @page {
            margin-left: 0.5cm;
            margin-right: 0.5cm;
          }

          .page-break {
              page-break-after: always;
          }

          .carnet {
            display: inline-block;
            width: 5.4cm;
            height: 8.5cm;            
            border: solid 1px;            
            align-items: center;            
            justify-content: center;
            margin-bottom: 8px;
          }

          .box {        
            width: 4cm;
            height: 5cm;                        
            margin: 50px auto;
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
        </style>
    </head>
    <body>        
        <br>        
        <div style="width:28cm;">
        <br>
        @php
        $contador = 0;
        @endphp

        @php
        $tpag = 0;
        @endphp 

        @foreach($objfuncionarios as $funcionario)
            @php
            $tpag = $tpag + 1;
            $contador = $contador + 1;
            @endphp

            <div class="carnet">
            <div class="box">
              <center>
              <label for="nombre" id="nombre" style="font-size: 8pt;">{{ Arr::get($funcionario,'nombre') }}</label>
              <br>
              <label for="apellido" id="apellido" style="font-size: 8pt;">{{ Arr::get($funcionario,'apellido') }}</label>
              <br>
              </center>
              <div class="">
                <img src='{{ $imprimirQR->getImagen(Arr::get($funcionario,'cedula'), Arr::get($funcionario,'nombre'), Arr::get($funcionario,'apellido'), 300) }}' width="95%" height="95%"/>                                            
              </div>
            </div> 
            </div>

            @if($contador==10)
                @php
                $contador = 0;
                $tpag = $tpag + 1;
                @endphp

                @if(count($objfuncionarios)==($tpag*10))
                @else
                    <hr>
                    <br>
                    <br>
                @endif                            
            @endif                               
        @endforeach                                                
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
    </body>    
</html>