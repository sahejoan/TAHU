@extends('layouts.app')

@section('content')
@include('alert.request')
@include('alert.succes')
@include('alert.errors')
<div>
{!!Form::open(array('name' => 'form', 'route' => array('division.store'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!}     
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
                        <h3 class="card-title">Nueva división o coordinación</h3>
                    </div>
                    <form>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="label">Descripción de la división</label>
                                {!!Form::text('descripcion', null, array('id' => 'descripcion', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'descripción de la división', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.ubicacion.focus();}'))!!}                                                                                    
                            </div>

                            <div class="form-group">
                                <label for="label">Ubicación</label>
                                {!!Form::text('ubicacion', null, array('id' => 'ubicacion', 'maxlength'=>'190', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'descripción de la ubicación', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_jefedep.focus();}'))!!}                                                                                    
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="ejefedep"> 
                            <div class="form-group">
                                <label for="label">Nombre del funcionario resposable</label>
                                {!!Form::select('id_jefedep', $objjefedependencia, 'Escribir [Enter Busca]', array('id'=>'id_jefedep' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.descripcion.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-1">
                                            <label for="">Función</label>
                                        </div>
                                        &nbsp;&nbsp;
                                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 form-check">
                                            {!! Form::radio('actual', 1, ($option_actual == 1 ? true : false), array('id'=>'actual', 'class'=>'form-check-input')) !!} 
                                            <label class="form-check-label" for="flexRadioDefault1">Jéfe</label>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 form-check">
                                            {!! Form::radio('actual', 0, ($option_actual == 0 ? true : false), array('id'=>'actual', 'class'=>'form-check-input')) !!}                       
                                            <label class="form-check-label" for="flexRadioDefault2">Coordinador</label>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 form-check">
                                            {!! Form::radio('actual', 2, ($option_actual == 2 ? true : false), array('id'=>'actual', 'class'=>'form-check-input')) !!}                       
                                            <label class="form-check-label" for="flexRadioDefault3">Encargado</label>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 form-check">
                                            {!! Form::radio('actual', 3, ($option_actual == 3 ? true : false), array('id'=>'actual', 'class'=>'form-check-input')) !!}                       
                                            <label class="form-check-label" for="flexRadioDefault4">Enlace</label>
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
            <a href="{!!URL::route('division.index')!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
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
    //var accionbtn=0;
    
    $(function () {
        $('#id_jefedep').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                            
    });    
</script>

@include('division.jsselect') 

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
