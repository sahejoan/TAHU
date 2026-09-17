<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Tarjeta del funcionario</title>            
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
        @foreach($objfuncionarios as $funcionario)
            <div class="box">
              <center>
              <label style="font-size: 8pt;">{{ Arr::get($funcionario,'nombre') }}</label>
              <br>
              <label style="font-size: 8pt;">{{ Arr::get($funcionario,'apellido') }}</label>
              <br>
              </center>
              <div class="">
                <img src='{{ $imprimirQR->getImagen(Arr::get($funcionario,"cedula"), Arr::get($funcionario,"nombre"), Arr::get($funcionario,"apellido"), 300) }}' width="95%" height="95%"/>                                            
              </div>
            </div>            
        @endforeach                                                
        </div>
    </body>
</html>