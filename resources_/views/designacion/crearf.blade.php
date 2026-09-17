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
{!!Form::open(array('name' => 'form', 'route' => array('designacion.store'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!}     
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
                        <h3 class="card-title">Nueva designación</h3>
                    </div>
                    <form>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="label">Número</label><br>
                                {!!Form::text('numdesig', null, array('id' => 'numdesig', 'maxlength'=>'10', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Número de la designación', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_desig.focus();}'))!!} 
                            </div>

                            <div class="form-group">
                                <label for="">Fecha</label><br>
                                {!!Form::date('fecha_desig', null, array('id' => 'fecha_desig', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_cese.focus();}'))!!}                                                                                                                                                                                                   
                            </div>

                            <div class="form-group">
                                <label for="">Fecha de culminación</label><br>
                                {!!Form::date('fecha_cese', null, array('id' => 'fecha_cese', 'class' => 'input-sm col-md-3 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_dep.focus();}'))!!}                                                                                                                                                                                                   
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="efunc"> 
                            <div class="form-group">
                                <label for="label">Funcionario</label>                                
                                {!!Form::select('id_func', $objfuncionarios, $tid_func, array('id'=>'id_func' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'readonly=true', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_dep.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="edepen"> 
                            <div class="form-group">
                                <label for="label">Dependencia</label>
                                {!!Form::select('id_dep', $objdependencia, 'Escribir [Enter Busca]', array('id'=>'id_dep' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_jefedep.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="ejefedep"> 
                            <div class="form-group">
                                <label for="label">Jefe de la dependencia</label>
                                {!!Form::select('id_jefedep', $objjefedependencia, 'Escribir [Enter Busca]', array('id'=>'id_jefedep' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_divi.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="edivision"> 
                            <div class="form-group">
                                <label for="label">División</label>
                                {!!Form::select('id_divi', $objdivision, 'Escribir [Enter Busca]', array('id'=>'id_divi' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_jefediv.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>                             

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="ejefedivision"> 
                            <div class="form-group">
                                <label for="label">Jefe de la división</label>
                                {!!Form::select('id_jefediv', $objjefedependencia, 'Escribir [Enter Busca]', array('id'=>'id_jefediv' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_area.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>                           

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="earea"> 
                            <div class="form-group">
                                <label for="label">Area</label>
                                {!!Form::select('id_area', $objarea, 'Escribir [Enter Busca]', array('id'=>'id_area' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.estatus.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div> 

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="ecargo"> 
                            <div class="form-group">
                                <label for="label">Estatus</label>
                                {!!Form::select('estatus', $objestatus, '', array('id'=>'estatus' , 'class'=>'form-control select2', 'data-placeholder'=>'Seleccionar', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_firma.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div>

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="earea"> 
                            <div class="form-group">
                                <label for="label">Firma</label>
                                {!!Form::select('id_firma', $objfirmas, 'Escribir [Enter Busca]', array('id'=>'id_firma' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.id_jefeth.focus();}'))!!}
                            </div>
                            </div>          
                            </div>            
                            </div> 

                            <div class="row"> 
                            <div class="col-xs-10 col-sm-10 col-md-10">
                            <div id="ejefedivision"> 
                            <div class="form-group">
                                <label for="label">Jefe de División de Administración</label>
                                {!!Form::select('id_jefeth', $objjefedependencia, 'Escribir [Enter Busca]', array('id'=>'id_jefeth' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.numdesig.focus();}'))!!}
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
            <a href="{!!URL::route('funcionarios.index')!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4">  
        </div>
  	</div>
</div>
<input name='tempid_dep' id='tempid_dep' type='hidden' value='0'>
<input name='tempid_divi' id='tempid_divi' type='hidden' value='0'>
<input name='tempid_coordivi' id='tempid_coordivi' type='hidden' value='0'>
{!!Form::close()!!}                                                                                                                                                          
</div>
@endsection


@section('scripts')

<script>
    var peticion1=0;
    var peticion2=0;
    var peticion3=0;
    var peticion4=0;
    var peticion5=0;
    var peticion6=0;
    var peticion7=0;
    var peticion8=0;
    var peticion9=0;
    var peticion10=0;

    //var accionbtn=0;
    
    $(function () {
        $('#id_area').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                                                      

        $('#id_dep').select2({         
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

        $('#id_jefediv').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });  

        $('#id_divi').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });          

        $('#id_firma').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });                  

        $('#id_jefeth').select2({         
            width: '100%',
            //allowClear: true,
            //multiple: false,
            //maximumSelectionSize: 1,
            placeholder: "Escribir [Enter Busca]",
        });  
    });    
</script>

@include('designacion.jsselect') 

<script>
    $(document).ready(function(){ 
        $("#id_dep").change(event => { 
            $("#tempid_dep").val($("#id_dep").val());
            BuscarJefe();
        });
        $("#id_divi").change(event => { 
            $("#tempid_divi").val($("#id_divi").val());
            $("#tempid_coordivi").val($("#id_divi").val());
            BuscarJefeDivision();
            BuscarAreaDivision();
        });                                        
    });
</script>

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