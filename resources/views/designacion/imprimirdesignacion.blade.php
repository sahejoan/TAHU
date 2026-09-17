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
		font-size: 12pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;		
	}
	.cssFont_2 {
		font-family: Times New Roman;;
		font-size: 14pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;
	}	
	.cssFont_3 {
		font-family: Times New Roman;;
		font-size: 14pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;
	}
	.cssFont_4 {
		font-family: Times New Roman;;
		font-size: 12pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;
		text-indent: 50px;
		text-align: justify;
		line-height: 25px;
	}
	.cssFont_5 {
		font-family: Times New Roman;;
		font-size: 16pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;		
	}
	.cssFont_6 {
		font-family: Times New Roman;;
		font-size: 10pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;		
	}	
	.cssFont_7 {
		font-family: Times New Roman;;
		font-size: 10pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;		
	}	
	.cssFont_8 {
		font-family: Times New Roman;;
		font-size: 12pt;
		letter-spacing: 0px;
		word-spacing: 0px;
		color: #000000;
		font-weight: normal;
		text-decoration: none;
		font-style: normal;
		font-variant: normal;
		text-transform: none;
		text-indent: 50px;
		text-align: justify;
		line-height: 25px;
	}


	@page {
		margin-left: 2.5cm;
		margin-right: 2.5cm;
	}	
</style>

</head>
<body class="cssFont_1">
	<div>
		<img src="{{ asset('img/logo1.png') }}" width="200" heigth="150"/>
	</div>

	<div class="cssFont_6">SNAT/INTI/GRLL/DA/RH/{{date("Y",strtotime($fecha_desig))}}/<label class="cssFont_5"></label></div>
	<br>

	<div class="cssFont_2"><center>MEMORANDO</center></div>
	<br>
	<table>
		<tr>
			<td>PARA:</td><td>&nbsp;&nbsp;</td><td>{{$nom_func}}</td>
		</tr>
		<tr>
			<td></td><td>&nbsp;&nbsp;</td><td>{{$ced_func}}</td>
		</tr>
		<tr>
			<td></td><td>&nbsp;&nbsp;</td><td>Cargo:&nbsp;{{$condicion_in}}</td>
		</tr>		
		<tr>
			<td><br></td><td>&nbsp;&nbsp;</td><td></td>
		</tr>				
		<tr>
			<td>DE:</td><td>&nbsp;&nbsp;</td><td>GERENTE REGIONAL DE TRIBUTOS INTERNOS</td>
		</tr>		
		<tr>
			<td></td><td>&nbsp;&nbsp;</td><td>REGIÓN {{strtoupper($nom_reg)}}</td>
		</tr>				
		<tr>
			<td><br></td><td>&nbsp;&nbsp;</td><td></td>
		</tr>				
		<tr>
			<td>FECHA:</td><td>&nbsp;&nbsp;</td>
			<td>
			@if ($fecha_desig=="")
			@else
			{{date("d",strtotime($fecha_desig))}}&nbsp;
			@if (date("m",strtotime($fecha_desig))=='01')
				Ene&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='02')
				Feb&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='03')
				Mar&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='04')
				Abr&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='05')
				May&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='06')
				Jun&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='07')
				Jul&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='08')
				Ago&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='09')
				Sep&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='10')
				Oct&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='11')
				Nov&nbsp;
			@endif
			@if (date("m",strtotime($fecha_desig))=='12')
				Dic&nbsp;
			@endif

			{{date("Y",strtotime($fecha_desig))}}
			@endif			
			</td>
		</tr>				
		<tr>
			<td>ASUNTO:</td><td>&nbsp;&nbsp;</td><td>DESIGNACIÓN</td>
		</tr>				
	</table>
	<br>
	<p class="cssFont_4">
	Tengo el agrado de dirigirme a Usted en la oportunidad de extenderle un
	cordial saludo Bolivariano, Revolucionario, Socialista y a la vez, informarle que
	apartir de la fecha de su notificación  ha sido designado para cumplir funciones
	en el Área de {{$nom_area}} - {{$nom_dep}}&nbsp;{{$ubicacion}}, bajo la supervición 
	directa del Jefe 
	@if((strpos($nom_dep, 'Sector') !== false) || (strpos($nom_dep, 'SECTOR') !== false))
	del sector
	@else
	de la dependencia
	@endif	
	conservando su adscripción a esta Gerencia Regional de Tributos Internos Región {{$nom_reg}}.
	</p>
	<p class="cssFont_4">
	Espero que asuma las funciones que le serán
	asignadas apartir de ahora, con la misma eficiencia y eficacia demostrada hasta este 
	momento para el logro de los objetivos de esta organización.
	</p>

	<br><br>
	<center>Atentamente,</center>	
	<br>
	<center>{{$nom_firma}}</center>
	<center>GERENTE REGIONAL DE TRIBUTOS INTERNOS</center>
	<center>REGION&nbsp;{{strtoupper($nom_reg)}}</center>
    <center>Providencia Administrativa N° {{$provi_jefe}} de fecha {{date("d/m/Y",strtotime($fecha_provi))}}</centre>
    <center>Gaceta Oficial N° {{$gaceta_provi}} de fecha {{date("d/m/Y",strtotime($fecha_gaceta))}}</center>

    <br><br>

    <div class="cssFont_7" align="left">
       Notificado:__________________________<br>
       Fecha:_______________________________<br>
       Firma:_______________________________<br>
       C.I.N°:______________________________<br>
       {{$siglas_firma}}/{{$siglas_jefeth}}/{{$siglas_coorth}}<br>
       @if($fecha_desig=="")
		{{date("d/m/Y",strtotime($horaactual))}}
       @else       
       	{{date("d/m/Y",strtotime($fecha_desig))}}
       @endif
	</div>
</body>
</html>