@extends('layouts.app')

@section('content')
@include('alert.request')
@include('alert.succes')
@include('alert.errors')
<div>

{!!Form::model($objregion, array('name' => 'form', 'route' => array('region.update',$objregion->id), 'method' => 'put', 'ENCTYPE'=>'multipart/form-data', 'files' => true))!!}
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
                        <h3 class="card-title">Actualizar región</h3>
                    </div>
                    <form>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="label">Nombre</label>
                                {!!Form::text('nom_reg', null, array('id' => 'nom_reg', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Nombre de la región', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.telf_reg.focus();}'))!!}                                                                                    
                            </div>
                            <div class="form-group">
                                <label for="label">Teléfono</label>
                                {!!Form::text('telf_reg', null, array('id' => 'telf_reg', 'maxlength'=>'15', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Teléfono', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.direccion.focus();}'))!!}                                                                                    
                            </div>
                            <div class="form-group">
                                <label for="label">Dirección</label>
                                {!!Form::text('direccion', null, array('id' => 'direccion', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Dirección', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.coordenadas.focus();}'))!!}                                                                                    
                            </div>                            
                            <div class="form-group">
                                <label for="label">Coordenadas</label>
                                {!!Form::text('coordenadas', null, array('id' => 'coordenadas', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Coordenadas', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.nom_reg.focus();}'))!!}                                                                                    
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
            <a href="{!!URL::route('region.index')!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1">  
            &nbsp;
        </div>        
        <div class="col-xs-3 col-sm-3 col-md-3" align="center">  

        @if (Auth::user()->hasAnyPermission('1_Crear_region'))
        <a class="btn btn-success" href="{{route('region.create')}}">Nueva Región</a>
        @endif
        
        </div>
    </div>
</div>

{!!Form::close()!!}                                                                                                                                                          
</div>
@endsection


@section('scripts')

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
