@extends('layouts.app')

@section('content')
@include('alert.request')
@include('alert.succes')
@include('alert.errors')
<div>
{!!Form::open(array('name' => 'form', 'route' => array('funcionarios.store'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!}     
<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
<div class="wrapper">
	<div class="container">
  		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
                <h4 class="text-center bg-purple">Registro</h4>

                <div class="color-palette-set">
                  <div class="bg-purple disabled color-palette">
                  	<span>Nuevo funcionario</span>
                  </div>
                </div>
        	</div>
    	</div>
        <br>
        <div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">
          	<ul id="myTab" class="nav nav-tabs nav-tabs-responsive" role="tablist">                
            	<li role="presentation" class="nav-item" style="border-color:#8E44AD;">
              		<a href="#pestana1" class="nav-link" id="pestana1-tab" role="tab" data-toggle="tab" aria-controls="pestana1" aria-expanded="true" style="border-color:#8E44AD;">
                		<span class="text">Información General</span>
              		</a>
            	</li>
            	<li role="presentation" class="nav-item" style="border-color:#8E44AD;">
              		<a href="#pestana2" class="nav-link" role="tab" id="pestana2-tab" data-toggle="tab" aria-controls="pestana2" style="border-color:#8E44AD;">
                		<span class="text">Datos de Contacto</span>
              		</a>
            	</li>

            <!-- Si se desea agregar un menu down tipo pestana
            <li role="presentation" class="dropdown">
              <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="myTabDrop1-contents">
                <span class="text">Dropdown</span>
                <span class="caret">
                </span>
              </a>
              <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents">
                <li>
                  <a href="#dropdown1" tabindex="-1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1">
                    <span>@fat</span>
                  </a>
                </li>
                <li>
                  <a href="#dropdown2" tabindex="-1" role="tab" id="dropdown2-tab" data-toggle="tab" aria-controls="dropdown2">
                    <span>@mdo</span>
                  </a>
                </li>
              </ul>
            </li>
            -->

            	<li role="presentation" class="nav-item" style="border-color:#8E44AD;">
              		<a href="#pestana3" class="nav-link" role="tab" id="pestana3-tab" data-toggle="tab" aria-controls="pestana3" style="border-color:#8E44AD;">
                		<span class="text">Datos de Ingreso</span>
              		</a>
            	</li>

                <li role="presentation" class="nav-item" style="border-color:#8E44AD;">
                  <a href="#pestana4" class="nav-link" role="tab" id="pestana4-tab" data-toggle="tab" aria-controls="pestana4" style="border-color:#8E44AD;" onclick="crear_qr();">
                    <span class="text">Foto</span>
                  </a>
                </li>
          	</ul>

          	<div id="myTabContent" class="tab-content">
            	<!-- Pestana 1 -->
            	<div role="tabpanel" class="tab-pane fade in active" id="pestana1" aria-labelledby="pestana1-tab">
                    <section class="border border-info rounded" style="background-color: #ebdef0;">
                        <br>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Cédula</label>
                                    {!!Form::text('cedula', null, array('id' => 'cedula', 'maxlength'=>'10', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'V-00000000', 'tabindex' => '1', 'autofocus' ,'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.rif.focus();}'))!!}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Rif.</label>
                                    {!!Form::text('rif', null, array('id' => 'rif', 'maxlength'=>'12', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'V-0000000-0', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.apellido.focus();}'))!!}                                                    
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Apellidos</label>
                                    {!!Form::text('apellido', null, array('id' => 'apellido', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Apellidos', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.nombre.focus();}'))!!}                                                    
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Nombres</label>
                                    {!!Form::text('nombre', null, array('id' => 'nombre',  'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Nombres', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.direccion.focus();}'))!!}                                                    
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Dirección</label>
                                    {!!Form::text('direccion', null, array('id' => 'direccion', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Dirección', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_nac.focus();}'))!!}                                                                                                           
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Fecha de Nacimiento</label>
                                    {!!Form::date('fecha_nac', null, array('id' => 'fecha_nac', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.cedula.focus();}'))!!}                                                                                                                                                                                                   
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-1">
                                            <label for="">Sexo</label>
                                        </div>
                                        <div class="col-sm-1 form-check">
                                            {!! Form::radio('sexo', 'M', ($option_sexo == 'M' ? true : false), array('id'=>'sexo', 'class'=>'form-check-input')) !!}      
                                            <label class="form-check-label" for="flexRadioDefault1">M</label>
                                        </div>
                                        <div class="col-sm-1 form-check">
                                            {!! Form::radio('sexo', 'F', ($option_sexo == 'F' ? true : false), array('id'=>'sexob', 'class'=>'form-check-input')) !!}                       
                                            <label class="form-check-label" for="flexRadioDefault2">F</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </section>
            	</div>
            	<!-- FIn pestana 1 -->
            	<!-- Pamel 2 -->
            	<div role="tabpanel" class="tab-pane fade" id="pestana2" aria-labelledby="pestana2-tab">
                    <section class="border border-info rounded" style="background-color: #ebdef0;">
                        <br>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Teléfono personal</label>
                                    {!!Form::text('telefono_p', null, array('id' => 'telefono_p', 'maxlength'=>'15', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => '0000-0000000', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.telefono_cont.focus();}'))!!}                                                                                                           
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Teléfono de contacto</label>
                                    {!!Form::text('telefono_cont', null, array('id' => 'telefono_cont', 'maxlength'=>'15', 'class' => 'input-sm col-md-5 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => '0000-0000000', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.correo_alt.focus();}'))!!}                                                                                                                                                           
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Correo personal</label>
                                    {!!Form::email('correo_alt', null, array('id' => 'correo_alt', 'maxlength'=>'190', 'class' => 'input-sm col-md-8 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'ejemplo@gmail.com', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.correo_i.focus();}'))!!}                                                                                                                                                                                                           
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Correo institucional</label>
                                    {!!Form::email('correo_i', null, array('id' => 'correo_i', 'maxlength'=>'190', 'class' => 'input-sm col-md-8 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'ejemplo@seniat.gov.ve', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.twitter.focus();}'))!!}                                                                                                                                                                                                                                                           
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Twitter</label>
                                    {!!Form::text('twitter', null, array('id' => 'twitter', 'maxlength'=>'190', 'class' => 'input-sm col-md-8 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => '@ejemplo', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.tipo_s.focus();}'))!!}                                                                                                                                                                                                                                                           
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Tipo de sangre</label>
                                    {!!Form::text('tipo_s', null, array('id' => 'tipo_s', 'maxlength'=>'30', 'class' => 'input-sm col-md-8 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Ejemplo A+', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.alergia.focus();}'))!!} 
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Alergias</label>
                                    {!!Form::text('alergia', null, array('id' => 'alergia', 'maxlength'=>'190', 'class' => 'input-sm col-md-8 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Ejemplo olores fuerte', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.telefono_p.focus();}'))!!}
                                </div>
                            </div>                            
                        </div>
                        <br>
                        <br>
                        <br>
                        <br>
                    </section>
            	</div>
            	<!-- Fin panel 2 -->
            	<!-- Panel 3 -->
            	<div role="tabpanel" class="tab-pane fade" id="pestana3" aria-labelledby="pestana3-tab">
                    <section class="border border-info rounded" style="background-color: #ebdef0;">
                        <br>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Condición de ingreso</label>
                                    {!!Form::select('condicion_in', $objcondicion_in, $tempcondicion_id, array('id' => 'condicion_in', 'class' => 'form-select', 'aria-label' => 'Default select example', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_in.focus();}'))!!}
                                </div>
                            </div>                            
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label for="">Fecha de ingreso</label>
                                    {!!Form::date('fecha_in', null, array('id' => 'fecha_in', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'DD-MM-AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_dep.focus();}'))!!}
                                </div>
                            </div>
                        </div>
                        El siguiente campo es opcional para registrar la asistencia: <br>Solo para ubicar el funcionario temporalmente mientras se crea la designación. <br>La designación tendrá prioridad sobre este dato.                        
                        <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="eubica"> 
                            <div class="form-group">
                                <label for="label">Ubicación</label>&nbsp;
                                {!!Form::select('id_dep', $objdependencia, '', array('id'=>'id_dep' , 'class'=>'form-control select2', 'data-placeholder'=>'Seleccionar', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.condicion_in.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                        </div> 
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                    </section>
            	</div>
            	<!--Fin panel 3 -->
              <!-- Panel 4 -->
              <div role="tabpanel" class="tab-pane fade" id="pestana4" aria-labelledby="pestana4-tab">
                    <section class="border border-info rounded" style="background-color: #ebdef0;">
                        <br>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                              
                            <!-- Inicio foto -->
                            <div class="form-group" align="center">       
                                {!!Form::label('Foto ')!!}        
                            <table>
                                <tr height="150">                
                                    <td width="245" class="fototablacelda">
                                        <div width="245" height="150" id="fileList" align="center">                          
                                        </div>

                                        <!-- ---------------------------------------------------- -->
                                        <div id="my_photo_booth">
                                            <div id="my_camera" style="overflow: hidden; width: 240px; height: 240px; transform: scaleX(-1);">
                                                <div>
                                                </div>
                                                <video autoplay="autoplay" style="width: 640px; height: 480px; transform-origin: 0px 0px 0px; transform: scaleX(0.5) scaleY(0.5);"></video>
                                            </div>           
                                        </div>
    
                                        <div id="my_photo_booth4" style="display:none">
                                            <div id="results" style="display:none">
                                            <!-- Imagen capturada... -->
                                            </div>
                                        </div>
                                        <!-- ---------------------------------------------------- -->                       
                                    </td>         
                                </tr>       
            
                                <tr height="50"> 
                                    <td width="245" align="center">
                                        <div id="my_photo_booth2">
                                            <input type="file" id="foto" name = "foto" accept="image/*" style="display:none" onchange="handleFiles(this.files)">
                                            <p>
                                            <br><a href="javascript:doClick()" id = "foto" title = "Solo jpeg, png, gif" class="btn btn-block btn-primary btn-flat"><i class="fa fa-upload"></i> Archivo de Imagen</a>
                                            </p>
                                        </div>
                                        <div id="my_photo_booth5">                 
                                            <a href="javascript:btnactivar()" class = "btn btn-block btn-primary btn-flat" ><i class="fa fa-camera"></i> Cámara</a>
                                        </div>
                                        <div id="my_photo_booth3" style="display:none">
                                            <div id="pre_take_buttons">
                                                <a href="javascript:preview_snapshot()" class = "btn btn-block btn-primary btn-flat" ><i class="fa fa-circle"></i> Capturar</a>                      
                                            </div>
                                            <div id="post_take_buttons" style="display:none">
                                                <a href="javascript:cancel_preview()" class = "btn btn-block btn-primary btn-flat" ><i class="fa fa-camera"></i> Nueva Toma</a>                      
                                                <a href="javascript:save_photo()" class = "btn btn-block btn-primary btn-flat" ><i class="fa fa-thumbs-up"></i> Aceptar Toma</a>                       
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            </div> 
                            <!-- fin foto -->

                            </div>                          

                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <!--Esto es lo que muestra el qr -->
                                <div id="codigo_qr_imagen" style="display:none">
                                    <div id="resultqr" style="display:none">
                                        <div id="imagenqr" width="70%" height="70%" align="center">                                            
                                            <img src="{!! asset('/images/fotografia.jpg') !!}" width="70%" height="70%"/>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div> 
                        <br>
                        <br>
                    </section>                    
              </div>
              <!--Fin panel 4 -->                 
        	</div>
    	</div>
	</div>
	
	<div class="row">
        <div class="col-xs-4 col-sm-4 col-md-4">  
        </div>
        <div class="col-xs-5 col-sm-5 col-md-5" align="center">  
            {!!Form::button(' Guardar', ['type' => 'button', 'title' => 'Guardar', 'class' => 'btn btn-primary btn-flat fa fa-save', 'onClick' => 'document.form.submit();']) !!}
            <a href="{!!URL::route('funcionarios.index')!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4">  
        </div>
  	</div>
</div>

<input name='ancho' id='ancho' type='hidden' value=''>
<input name='alto' id='alto' type='hidden' value=''>
<input name='tipoimagen' id='tipoimagen' type='hidden' value=''>    
<input name='capture' id='capture' type='hidden' value=''>

{!!Form::close()!!}                                                                                                                                                          
</div>
@endsection


@section('scripts')
   {!! Html::script('/js/in/eventosdeimagenes.js') !!} 
   {!! Html::script('/js/in/webcam.js') !!}
   {!!Html::script('/qrcode/html5-qrcode.min.js') !!}

    @include('funcionarios.jscrear')

    <script>
    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }

    if( typeof MostrarMensajeError !== 'undefined' && jQuery.isFunction( MostrarMensajeError ) ) {
      //Es seguro ejectura la función
      MostrarMensajeError(messageerror);
    }
    </script>
@endsection
