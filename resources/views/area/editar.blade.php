@extends('layouts.app')

@section('content')
@include('alert.request')
@include('alert.succes')
@include('alert.errors')
<div>

{!!Form::model($objarea, array('name' => 'form', 'route' => array('area.update',$objarea->id), 'method' => 'put', 'ENCTYPE'=>'multipart/form-data', 'files' => true))!!}
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
                        <h3 class="card-title">Actualizar área</h3>
                    </div>
                    <form>
                        <div class="card-body">
                            <div class="row"> 
                            <div class="col-xs-12 col-sm-12 col-md-12">                            
                            <div class="form-group">
                                <label for="label">Descripción</label>
                                {!!Form::text('nom_area', null, array('id' => 'nom_area', 'maxlength'=>'190', 'class' => 'input-sm col-md-12 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Nombre del área', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.telf_area.focus();}'))!!}                                                                                    
                            </div>
                            <div class="form-group">
                                <label for="label">Teléfono</label>
                                {!!Form::text('telf_area', null, array('id' => 'telf_area', 'maxlength'=>'15', 'class' => 'input-sm col-md-12 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Teléfono', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.ubicacion.focus();}'))!!}                                                                                    
                            </div>
                            <div class="form-group">
                                <label for="label">Ubicación</label>
                                {!!Form::select('ubicacion', $sti, $objarea['ubicacion'], array('id'=>'ubicacion' , 'class'=>'form-control select2', 'data-placeholder'=>'Seleccionar', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.coord_area.focus();}'))!!}
                            </div>                            
                            <div class="form-group">
                                <label for="label">Coordenadas</label>
                                {!!Form::text('coord_area', null, array('id' => 'coord_area', 'maxlength'=>'190', 'class' => 'input-sm col-md-12 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Coordenadas', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_divi.focus();}'))!!}                                                                                    
                            </div>
                            </div>
                            </div>

                            <div class="row"> 
                            <div class="col-xs-12 col-sm-12 col-md-12">
                            <div id="edivision"> 
                            <div class="form-group">
                                <label for="label">División/Coordinación</label>
                                {!!Form::select('id_divi', $objdivision, $objarea->id_divi, array('id'=>'id_divi' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_func.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            

                            <div class="col-xs-12 col-sm-12 col-md-12">
                            <div id="edivision"> 
                            <div class="form-group">
                                <label for="label">Funcionario responsable</label>
                                {!!Form::select('id_func', $objfuncionario, $objarea->id_func, array('id'=>'id_func' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.nom_area.focus();}'))!!}
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
            {!!Form::button(' Guardar', ['type' => 'button', 'title' => 'Guardar área', 'class' => 'btn btn-primary btn-flat fa fa-save', 'onClick' => 'document.form.submit();']) !!}
            <a href="{!!URL::route('area.index')!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1">  
            &nbsp;
        </div>        
        <div class="col-xs-3 col-sm-3 col-md-3" align="center">  
            @if (Auth::user()->hasAnyPermission('3_Crear_area'))
            <a class="btn btn-success" href="{{route('area.create')}}">Nueva área</a>
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
    
    $(function () {
        $('#id_divi').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });    
    });

    $(function () {
        $('#id_func').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });    
    });        
    </script>

    @include('area.jsselect') 

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
