@extends('layouts.app')

@section('content')
@include('alert.request')
@include('alert.succes')
@include('alert.errors')
<div>
{!!Form::open(array('name' => 'form', 'route' => array('firmas.store'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!}     
<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
<div class="wrapper">
	<div class="container">
  		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12">
                <h4 class="text-center bg-purple">Registro</h4>
        	</div>
    	</div>
        <br>
        <!-- Contenido -->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="card card-primary" style="background-color: #ebdef0;">
                    <div class="card-header bg-purple">
                        <h3 class="card-title">Nueva firma</h3>
                    </div>
                    <form>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="label">Nombre del firmante</label>
                                {!!Form::text('firma', null, array('id' => 'firma', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Nombre del firmante', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_dep.focus();}'))!!}                                                                                    
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="eubica"> 
                            <div class="form-group">
                                <label for="label">Ubicación</label>
                                {!!Form::select('id_dep', $objubicacion, '', array('id'=>'id_dep' , 'class'=>'form-control select2', 'data-placeholder'=>'Seleccionar', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.provi_jefe.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>

                            <div class="form-group">
                                <label for="label">Providencia Administrativa</label><br>
                                {!!Form::text('provi_jefe', null, array('id' => 'provi_jefe', 'maxlength'=>'20', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Providencia administrativa', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_provi.focus();}'))!!} 
                            </div>

                            <div class="form-group">
                                <label for="">Fecha de la providencia</label><br>
                                {!!Form::date('fecha_provi', null, array('id' => 'fecha_provi', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.gaceta_provi.focus();}'))!!}                                                                                                                                                                                                   
                            </div>

                            <div class="form-group">
                                <label for="label">Número de Gaceta Oficial</label><br>
                                {!!Form::text('gaceta_provi', null, array('id' => 'gaceta_provi', 'maxlength'=>'20', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Número de la gaceta oficial', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_gaceta.focus();}'))!!} 
                            </div>

                            <div class="form-group">
                                <label for="">Fecha de la gaceta</label><br>
                                {!!Form::date('fecha_gaceta', null, array('id' => 'fecha_gaceta', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.estatus.focus();}'))!!}                                                                                                                                                                                                   
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-1">
                                            <label for="">Actual</label>
                                        </div>
                                        <div class="col-sm-1 form-check">
                                            {!! Form::radio('actual', 1, ($option_actual == 1 ? true : false), array('id'=>'actual', 'class'=>'form-check-input')) !!}      
                                            <label class="form-check-label" for="flexRadioDefault1">Si</label>
                                        </div>
                                        <div class="col-sm-1 form-check">
                                            {!! Form::radio('actual', 0, ($option_actual == 0 ? true : false), array('id'=>'actual', 'class'=>'form-check-input')) !!}                       
                                            <label class="form-check-label" for="flexRadioDefault2">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Fin contenido -->
	</div>
	
	<div class="row">
        <div class="col-xs-4 col-sm-4 col-md-4">  
        </div>
        <div class="col-xs-5 col-sm-5 col-md-5" align="center">  
            {!!Form::button(' Guardar', ['type' => 'button', 'title' => 'Guardar', 'class' => 'btn btn-primary btn-flat fa fa-save', 'onClick' => 'document.form.submit();']) !!}
            <a href="{!!URL::route('firmas.index')!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4">  
        </div>
  	</div>
</div>

{!!Form::close()!!}                                                                                                                                                          
</div>
@endsection


@section('scripts')
<script>
    var peticion1=0;
    
    $(function () {
        $('#id_dep').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                                                                        
    });    
</script>

@include('firmas.jsselect')

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
