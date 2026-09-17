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
		  <center>ASISTENCIA</center>
    	<center>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->nom_dep}}</center>
    	<center>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->ubicacion}}</center>
    	<center>Región {{\Illuminate\Support\Facades\Auth::user()->Dependencia->Region->nom_reg}}</center>
      <br>
	 @else
      @if ($nombredependencia=="")
			  <center>ASISTENCIA GENERAL</center>
	    	<center>Región {{\Illuminate\Support\Facades\Auth::user()->Dependencia->Region->nom_reg}}</center>
    		<br>
		  @else
			  <center>ASISTENCIA</center>
	    	<center>{{$nombredependencia}}</center>
    		<center>Región {{\Illuminate\Support\Facades\Auth::user()->Dependencia->Region->nom_reg}}</center>
        <br>
		  @endif

      @if ($nombredivision=="")
      @else
        <center>División {{$nombredivision}}</center>
        <br>
      @endif

	@endif

	@if (($idesde == "") && ($ihasta == ""))
	  <center>Fecha: Hoy</center> 
  @else
    <center>Fecha: {{date("d/m/Y",strtotime($idesde))}} al {{date("d/m/Y",strtotime($ihasta))}}</center>
	@endif
            @if (count($objasistencia)>0)
              <div> 
                <table id="tablaordenar" class="cssBorder_1">
                <thead>
                  <tr class="cssBorder_2">
                  <th class="cssBorder_2">N°</th>
                  <th class="cssBorder_2">Cédula</th>
                  <th class="cssBorder_2">Funcionario</th>
                  <th class="cssBorder_2">Fecha</th>                  
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">HE / HS</th>
                  <th class="cssBorder_2">Observación</th>
                  </tr>
                </thead>
               
                <tbody>
                  @php
                  $contador = 0;
                  @endphp

                  @foreach($objasistencia as $asistencia)
                  @php
                  $contador = $contador + 1;
                  @endphp

                  <tr class="cssBorder_2">
				          <td class="cssBorder_2">{{$contador}}</td>
                  <td class="cssBorder_2">{{Arr::get($asistencia,'cedula')}}</td>
                  <td class="cssBorder_2">{{Arr::get($asistencia,'nombre')}}&nbsp;{{Arr::get($asistencia,'apellido')}}</td>                  
                  <td class="cssBorder_2">{{$objfechacarbon->versolofecha_a_str(Arr::get($asistencia,'hora_e'))}}</td>

                  <td class="cssBorder_2">
                    <label style="{{ $objfechacarbon->EntroAlas8(Arr::get($asistencia,'hora_e'),9)}}">
                      {{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e'))}}
                    </label>
                    <br>
                    <label style="{{ $objfechacarbon->EoS(Arr::get($asistencia,'hora_e'),Arr::get($asistencia,'hora_s'),Arr::get($asistencia,'hora_e2'),Arr::get($asistencia,'hora_s2'),Arr::get($asistencia,'hora_e3'),Arr::get($asistencia,'hora_s3'),Arr::get($asistencia,'hora_e4'),Arr::get($asistencia,'hora_s4'),Arr::get($asistencia,'hora_e5'),Arr::get($asistencia,'hora_s5'),Arr::get($asistencia,'hora_e6'),Arr::get($asistencia,'hora_s6'),Arr::get($asistencia,'hora_e7'),Arr::get($asistencia,'hora_s7'),Arr::get($asistencia,'hora_e8'),Arr::get($asistencia,'hora_s8'),1,$horaactual,9) }}">
                      {{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s'))}}
                    </label>
                  </td>
                  <td class="cssBorder_2">
                    <label style="{{ $objfechacarbon->EoS(Arr::get($asistencia,'hora_e'),Arr::get($asistencia,'hora_s'),Arr::get($asistencia,'hora_e2'),Arr::get($asistencia,'hora_s2'),Arr::get($asistencia,'hora_e3'),Arr::get($asistencia,'hora_s3'),Arr::get($asistencia,'hora_e4'),Arr::get($asistencia,'hora_s4'),Arr::get($asistencia,'hora_e5'),Arr::get($asistencia,'hora_s5'),Arr::get($asistencia,'hora_e6'),Arr::get($asistencia,'hora_s6'),Arr::get($asistencia,'hora_e7'),Arr::get($asistencia,'hora_s7'),Arr::get($asistencia,'hora_e8'),Arr::get($asistencia,'hora_s8'),2,$horaactual,9) }}">
                    {{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e2'))}}
                    </label>
                    <br>
                    <label style="{{ $objfechacarbon->EoS(Arr::get($asistencia,'hora_e'),Arr::get($asistencia,'hora_s'),Arr::get($asistencia,'hora_e2'),Arr::get($asistencia,'hora_s2'),Arr::get($asistencia,'hora_e3'),Arr::get($asistencia,'hora_s3'),Arr::get($asistencia,'hora_e4'),Arr::get($asistencia,'hora_s4'),Arr::get($asistencia,'hora_e5'),Arr::get($asistencia,'hora_s5'),Arr::get($asistencia,'hora_e6'),Arr::get($asistencia,'hora_s6'),Arr::get($asistencia,'hora_e7'),Arr::get($asistencia,'hora_s7'),Arr::get($asistencia,'hora_e8'),Arr::get($asistencia,'hora_s8'),3,$horaactual,9) }}">
                    {{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s2'))}}
                    </label>
                  </td>
                  <td class="cssBorder_2">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e3'))}}<br>{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s3'))}}</td>
                  <td class="cssBorder_2">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e4'))}}<br>{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s4'))}}</td>
                  <td class="cssBorder_2">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e5'))}}<br>{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s5'))}}</td>
                  <td class="cssBorder_2">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e6'))}}<br>{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s6'))}}</td>
                  <td class="cssBorder_2">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e7'))}}<br>{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s7'))}}</td>
                  <td class="cssBorder_2">{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_e8'))}}<br>{{$objfechacarbon->versolohora_a_str(Arr::get($asistencia,'hora_s8'))}}</td>


                  <td class="cssBorder_2">{{Arr::get($asistencia,'observacion')}}</td>
                  </tr>

                  @endforeach
                
                </tbody>                
                </table>          
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
            $x = 930;
            $y = 590;
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