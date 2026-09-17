<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Lista de funcionarios</title>            
        <style>
          @page {
            margin-left: 1.0cm;
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
        </style>
    </head>
    <body>
        <div style="width:21cm;">
        <br>
        @php
        $contador = 0;
        @endphp

        @foreach($objfuncionarios as $funcionario)
            @php
            $contador = $contador + 1;
            @endphp

            <div class="box">
              <center>
              <label for="nombre" id="nombre" style="font-size: 8pt;">{{ Arr::get($funcionario,'nombre') }}</label>
              <br>
              <label for="apellido" id="apellido" style="font-size: 8pt;">{{ Arr::get($funcionario,'apellido') }}</label>
              <br>
              </center>
              <div class="">
                <img src='{{ $imprimirQR->getImagen(Arr::get($funcionario,"nuevoqr"),Arr::get($funcionario,"cedula"), Arr::get($funcionario,"nombre"), Arr::get($funcionario,"apellido"), 300) }}' width="95%" height="95%"/>                                            
              </div>
            </div>
            @if($contador<count($objfuncionarios))
              <hr>            
            @endif                                           
        @endforeach                                                
        </div>
    </body>
</html>