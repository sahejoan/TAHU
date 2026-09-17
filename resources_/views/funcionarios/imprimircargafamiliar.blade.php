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
 
  <style type="text/css">
  .cssFont_1 {
    font-family: Times New Roman;;
    font-size: 9pt;
    color: #000000;
    font-weight: normal;
    text-decoration: none;
    font-style: normal;
    font-variant: normal;
    text-transform: none;   
  }
  .cssBorder_1 {
    border:solid 1px;
    width: 100%;
    border-collapse: collapse;
  } 
  .cssBorder_2 {
    border:solid 1px;
  } 

  @page {
    margin-left: 1.0cm;
    margin-right: 1.0cm;
  } 
</style>

</head>
<body class="cssFont_1">
  <div>
    <img src="{{ asset('img/logo1.png') }}" width="200" heigth="150"/>
  </div>

    @if (Auth::user()->hasAnyPermission('Solo_dependencia'))
      <center>CARGA FAMILIAR</center>
      <center>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->nom_dep}}</center>
      <center>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->ubicacion}}</center>
      <center>Región {{\Illuminate\Support\Facades\Auth::user()->Dependencia->Region->nom_reg}}</center>
    @else
      @if ($nombredependencia=="")
        <center>CARGA FAMILIAR</center>
        <center>Región <small>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->Region->nom_reg}}</small></center>
      @else
        <center>CARGA FAMILIAR</center>
        <center>{{$nombredependencia}}</center>
        <center>Región <small>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->Region->nom_reg}}</small></center>
      @endif
  @endif
  <center>{{date("d-m-Y")}}</center>
            @if (count($objfuncionarios)>0)
              <div> 
                  @foreach($objfuncionarios as $funcionarios)

                  @php
                  $objcargafamiliar = $funcionarios['familiares'];
                  @endphp

                  @if (count($objcargafamiliar)>0)

                  <table id="tablaordenar" class="">
                  <thead>
                  <tr class="">
                  <th class="" align="center">N°</th>
                  <th class="" align="left">Cédula</th>
                  <th class="" align="left">Funcionario</th>
                  </tr>
                  </thead>
               
                  <tbody>
                  <tr class="">
                  <td class="" align="center">{{ Arr::get($funcionarios,'numreg') }}</td>
                  <td class="" align="left">{{ Arr::get($funcionarios, 'cedula') }}</td>
                  <td class="" align="left">{{ Arr::get($funcionarios,'nombre') }}&nbsp;{{ arr::get($funcionarios,'apellido') }}</td>                  
                  </tr>
                  </tbody>
                  </table>          
                  
                      <table id="tablaordenar" class="cssBorder_1">
                      <thead>
                      <tr class="cssBorder_2">
                      <th class="cssBorder_2" align="left">Cédula</th>
                      <th class="cssBorder_2" align="left">Apellido</th>
                      <th class="cssBorder_2" align="left">Nombre</th>
                      <th class="cssBorder_2" align="center">Fec/Nac</th>
                      <th class="cssBorder_2" align="center">Edad</th>
                      <th class="cssBorder_2" align="center">Parentesco</th>
                      </tr>
                      </thead>               
                      <tbody>                    
                      @foreach($objcargafamiliar as $cargafamiliar)
                      <tr class="cssBorder_2">
                      <td class="cssBorder_2" style="width:60px;" align="left">{{arr::get($cargafamiliar,'cedula')}}</td>
                      <td class="cssBorder_2" style="width:150px;" align="left">{{arr::get($cargafamiliar,'apellido')}}</td>
                      <td class="cssBorder_2" style="width:150px;" align="left">{{arr::get($cargafamiliar,'nombre')}}</td>                  
                      <td class="cssBorder_2" style="width:50px;" align="center">{{date("d-m-Y",strtotime(arr::get($cargafamiliar,'fecha_nac')))}}</td>                  
                      <td class="cssBorder_2" style="width:30px;" align="center">{{$calcularedad->edad(arr::get($cargafamiliar,'fecha_nac'))}}</script></td> 
                      <td class="cssBorder_2" style="width:40px;" align="center">{{arr::get($cargafamiliar,'parentesco')}}</td>                  
                      </tr>
                      @endforeach
                      </tbody>
                      </table>                                
                  @endif

                @endforeach                
            </div>      
            @else
              <center>SIN REGISTRO PARA LA CONSULTA</center>
            @endif  

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
