@extends('layouts.app')
@section('css')
<style>
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #336699;
    color: white;    
}
.select2-container .select2-choice, .select2-result-label {
  font-size: 1.5em;
  height: 41px; 
  overflow: auto;
}
.select2-arrow, .select2-chosen {
  padding-top: 6px;
}
.select2-dropdown {
  background-color: #ccccff;
  border: 1px solid #aaa;
  border-radius: 4px;
  box-sizing: border-box;
  display: block;
  position: absolute;
  left: -100000px;
  width: 100%;
  z-index: 1051; }

</style>
@endsection
@section('content')
@include('alert.request')
@include('alert.succes')
@include('alert.errors')
<div>

{!!Form::model($objdependencia, array('name' => 'form', 'route' => array('dependencia.update',$objdependencia->id), 'method' => 'put', 'ENCTYPE'=>'multipart/form-data', 'files' => true))!!}
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
                        <h3 class="card-title">Actualizar dependencia</h3>
                    </div>
                    <form>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="label">Nombre de la dependencia</label>
                                {!!Form::text('nom_dep', null, array('id' => 'nom_dep', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Nombre de la dependencia', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_jefedep.focus();}'))!!} 
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="ejefedep"> 
                            <div class="form-group">
                                <label for="label">Nombre del Jefe de la Dependencia</label>
                                {!!Form::select('id_jefedep', $objjefedependencia, $objdependencia->id_jefedep, array('id'=>'id_jefedep' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.ubicacion.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>

                            <div class="form-group">
                                <label for="label">Ubicación</label><br>
                                {!!Form::text('ubicacion', null, array('id' => 'ubicacion', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Ubicación', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.telf_dep.focus();}'))!!}                                                                                    
                            </div>
                            <div class="form-group">
                                <label for="label">Teléfono</label><br>
                                {!!Form::text('telf_dep', null, array('id' => 'telf_dep', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Teléfono de la dependencia', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_reg.focus();}'))!!}                                                                                    
                            </div>                            
                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="ejefedependenciaregion"> 
                            <div class="form-group">
                                <label for="label">Región</label>
                                {!!Form::select('id_reg', $objregion, $objdependencia->id_reg, array('id'=>'id_reg' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_area.focus();}'))!!}
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
        <div class="col-xs-3 col-sm-3 col-md-3">        
        </div>
        <div class="col-xs-5 col-sm-5 col-md-5" align="center" >  
            {!!Form::button(' Guardar', ['type' => 'button', 'title' => 'Guardar', 'class' => 'btn btn-primary btn-flat fa fa-save', 'onClick' => 'document.form.submit();']) !!}
            <a href="{!!URL::route('dependencia.index')!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1">  
            &nbsp;
        </div>        
        <div class="col-xs-3 col-sm-3 col-md-3" align="center">  
            @if (Auth::user()->hasAnyPermission('5_Crear_dependencia'))
            <a class="btn btn-success" href="{{route('dependencia.create')}}">Nueva dependencia</a>
            @endif
        </div>
    </div>
</div>

{!!Form::close()!!}                                                                                                                                                          
</div>
@endsection


@section('scripts')
<script>
    var peticion1=0;
    var peticion2=0;
    //var accionbtn=0;
    
    $(function () {
        $('#id_reg').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });

        $('#id_jefedep').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                            
    });    
</script>

@include('dependencia.jsselect') 

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
