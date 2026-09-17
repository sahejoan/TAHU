@extends('layouts.app')

@section('css')
  {!!Html::style('/AdminLTE/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')!!}

  <style>
    .bootstrap-duallistbox-container select {
    height: 300px !important;
    }

    .bootstrap-duallistbox-container .customButtonBox {
    margin-top: auto;
    margin-bottom: auto;
    padding-top: 105px;
    }

    .bootstrap-duallistbox-container .customButtonBox button {
    margin-bottom: 15px;
    }    
  </style>
@endsection

@section('content')
@include('alert.request')
@include('alert.succes')
@include('alert.errors')
<div>
{!!Form::model($objcargofuncionario, array('name' => 'form', 'route' => array('cargofuncionario.update',$objcargofuncionario->id), 'method' => 'put', 'ENCTYPE'=>'multipart/form-data', 'files' => true))!!}
<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
<input name='id_desig' id='id_desig' type='hidden' value='{{ $id_desig }}'>
<input name='id_area' id='id_area' type='hidden' value='{{ $id_area }}'>

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
                        <h3 class="card-title">Actualizar cargo</h3>
                    </div>
                    <form>
                        <div class="card-body">
                           
                            <div class="row"> 
                              <div class="col-xs-10 col-ms-10 col-md-10">
                                <div id="earea"> 
                                  <div class="form-group">
                                    <label for="label">Cargo</label>
                                    {!!Form::select('id_lcargo', $objlcargo, $objcargofuncionario->id_lcargo, array('id'=>'id_lcargo' , 'class'=>'form-control select2', 'data-placeholder'=>'Escribir [Enter Busca]', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_ex.focus();}'))!!}
                                  </div>
                                </div>          
                              </div>
                            </div> 

                            <div class="row">
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label for="">Fecha de expedición</label><br>
                                  {!!Form::date('fecha_ex', null, array('id' => 'fecha_ex', 'class' => 'input-sm col-md-6 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_inic.focus();}'))!!}                                                                                                                                                                                                   
                                </div>                                
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label for="">Fecha de inicio</label><br>
                                  {!!Form::date('fecha_inic', null, array('id' => 'fecha_inic', 'class' => 'input-sm col-md-6 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.fecha_culm.focus();}'))!!}                                                                                                                                                                                                   
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label for="">Fecha de cuminación</label><br>
                                  {!!Form::date('fecha_culm', null, array('id' => 'fecha_culm', 'class' => 'input-sm col-md-6 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input date-input', 'placeholder' => 'DD/MM/AAAA', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.st_cargo.focus();}'))!!}                                                                                                                                                                                                   
                                </div>
                              </div>                              
                            </div>

                            <div class="row">
                              <div class="col-md-4">
                                <div id="ecargo"> 
                                  <div class="form-group">
                                    <label for="label">Estatus del cargo</label>
                                    {!!Form::select('sta_cargo', $objestatuscargo, $objcargofuncionario->sta_cargo, array('id'=>'sta_cargo' , 'class'=>'form-control select2', 'data-placeholder'=>'Seleccionar', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.st_contrato.focus();}'))!!}
                                  </div>
                                </div>          
                              </div>
                              <div class="col-md-4">
                                <div id="ecargo"> 
                                  <div class="form-group">
                                    <label for="label">Estatus del contrato</label>
                                    {!!Form::select('sta_contrato', $objestatuscontratado, $objcargofuncionario->sta_contrato, array('id'=>'sta_contrato' , 'class'=>'form-control select2', 'data-placeholder'=>'Seleccionar', 'style'=>'width: 100%;', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.observaciones.focus();}'))!!}
                                  </div>
                                </div>                                          
                              </div>
                              <div class="col-md-4">
                              </div>                              
                            </div>

                            <div class="form-group">
                                <label for="">Observaciones</label><br>
                                {!!Form::textarea('observaciones', null, array('id' => 'observaciones', 'rows'=>'3', 'class' => 'input-sm col-md-10 text-base text-coolGray-900 font-normal outline-none focus:border-green-500 border border-coolGray-200 rounded-lg shadow-input', 'placeholder' => 'Observaciones', 'onkeyDown'=>'if(event.keyCode==13 || event.which==13){document.form.lcargo.focus();}'))!!}                                                                                                                    
                            </div>

                            <center><a class="btn btn-info btn-sm fa fa-list-alt" title="Lista" data-toggle="modal" data-target="#modalmostrar"> Lista de funciones</a></center>

                            <div id="listafunciones">
                              <select multiple="multiple" size="10" name="duallistbox_cargo[]" id="duallistbox_cargo[]" title="duallistbox_cargo[]">
                                @foreach($objlfuncion as $lfuncion)
                                  @if ($lfuncion['seleccionado'])
                                    <option value="{{ $lfuncion['id'] }}" selected>{{ $lfuncion['descripcion'] }}</option>
                                  @else
                                    <option value="{{ $lfuncion['id'] }}">{{ $lfuncion['descripcion'] }}</option>
                                  @endif                                
                                @endforeach
                              </select>
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
            <a href="{!!URL::route('buscardesignacionactual', $id_desig)!!}" class="btn btn-danger btn-flat" title="Cerrar Formulario"><i class="fa fa-ban"></i> Cancelar</a>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4">  
        </div>
  </div>
</div>

    <div class="section">                                                                                                                                   
      <!-- Modal -->
      <div class="modal fade" id="modalmostrar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #9999ff;">
              <h5 class="modal-title" id="staticBackdropLabel">Funciones</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                                                   
              <div class="row"> 
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <div class="form-group" align="left">       
                    <div id="resultdatos">
                      <div id="datosfunciones" class="bootstrap-duallistbox-container row moveondoubleclick" width="100%">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Continuar</button>
            </div>
          </div>
        </div>
      </div>
    </div>    
{!!Form::close()!!}                                                                                                                                                          
</div>

@endsection


@section('scripts')

    {!!Html::script('/AdminLTE/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')!!}
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

    <script>
    //Bootstrap Duallistbox
    var tfbox = $('select[name="duallistbox_cargo[]"]').bootstrapDualListbox({              
      nonSelectedListLabel: 'Funciones no asignadas',  // Etiqueta de lista no seleccionada, valor predeterminado falso;
      selectedListLabel: 'Funciones asignadas',  // Etiqueta de la lista seleccionada, por defecto false;
      showFilterInputs: true,                   // Si se muestra el cuadro de entrada de entrada filtrada, el valor predeterminado es verdadero; si es falso, el contenido relacionado con el filtrado no funciona y no se muestra
      filterTextClear: 'Mostrar Todos',         // Borrar el texto del botón de filtro, el valor predeterminado es 'mostrar todo', se puede reemplazar con otro texto;
      filterPlaceHolder: 'Filtrar funciones',            // Marcador de posición del cuadro de entrada de la condición del filtro, contenido personalizable, el valor predeterminado es 'Filter',
      //nonSelectedFilter: 'ion ([7-9] | [1] [0-2])', // La condición del filtro de la opción no seleccionada, el valor predeterminado es una cadena vacía '', también puede usar métodos regulares, por ejemplo: 'ion ([ 7-9] | [1] [0-2]) 'Filtro 7, 8, 9, 10, 11, 12;
      //selectedFilter: '3',                          // La condición del filtro de la opción seleccionada, el valor predeterminado es una cadena vacía ''; Referencia: nonSelectedFilter; generalmente no establece la condición del filtro seleccionado, hará que algunos elementos seleccionados no estén en la condición del filtro de la opción seleccionada No se puede mostrar dentro del rango;

      moveAllLabel: 'Agregar Todos',                          // Agregar la etiqueta de todos los botones de opción, el valor predeterminado es 'Mover todo'
      moveSelectedLabel: 'Agregar Selección',                 // Agregar etiqueta del botón de opción seleccionado, predeterminado 'Mover seleccionado'
      removeAllLabel: 'Remover Todos',                        // Eliminar la etiqueta de todos los botones de opciones, el valor predeterminado es 'Eliminar todo'
      removeSelectedLabel: 'Remover Selección',               // Eliminar la etiqueta del botón de opción seleccionado, el valor predeterminado es 'Eliminar seleccionado'
      helperSelectNamePostfix: '_ ast',                       // El sufijo del nombre del selector es'_helper ', después de que la lista no seleccionada se cose 1, la costura seleccionada 2; también se puede modificar mediante el método setHelperSelectNamePostfix (valor, actualización);
      selectorMinimalHeight: 260,                             // La altura mínima del selector, cuando es inferior a 260 px, el valor predeterminado es una altura fija, y un valor mayor aumentará la altura del selector; no sé el tamaño del valor predeterminado y la unidad de altura
      infoText: 'Total {0}',                                  // Cuando no se filtra, elementos totales de opciones seleccionadas / no seleccionadas; el valor predeterminado es 'Mostrar todo {0}';
      infoTextEmpty: 'Vacio',
      infoTextFiltered: '<span class="label label-warning">Filtrados</span> {0} de {1}',
      infoTextEmpty: 'Lista vacía',                                               // cuando la condición del filtro es '', y el contenido que se muestra cuando la lista seleccionada / no seleccionada no tiene opciones; el valor predeterminado es 'Lista vacía';
      filterOnValues: false,                                                      // No sé el rol específico por el momento

      preserveSelectionOnMove: 'moved',       // 'movió' o 'todos', muestra los elementos movidos a la lista de destino (visualización del color de fondo), el valor predeterminado es falso, no se muestra; no ve la diferencia entre 'movido' y 'todos';
      moveOnSelect: false,                    // si se mueve la opción seleccionada; cuando es falsa, los botones de moveSelected y removeSelected se muestran y surten efecto; el valor predeterminado es verdadero; verdadero es que solo el cursor se puede seleccionar continuamente, suelte el mouse, el elemento seleccionado se moverá; si es falso Se puede usar con Ctrl y Shift del teclado. Haga clic en los botones de moveSelectedLabel y removeSelectedLabel, la opción se moverá;
      sortByInputOrder: true,

      //eventMoveOverride: false,          
      //eventMoveAllOverride: false,         
      //eventRemoveOverride: false,          
      //eventRemoveAllOverride: false,  
      btnClass: 'btn-outline-secondary',     
      // string, sets the text for the "Move" button                                           
      btnMoveText: '<i class="fas fa-solid fa-tag"> Agregar función</i>',       
      // string, sets the text for the "Remove" button                                                       
      btnRemoveText: '<i class="fa fa-times"></i> Quitar función',     
      // string, sets the text for the "Move All" button
      btnMoveAllText: '<i class="fas fa-solid fa-tags"> Agregar todas</i>',    
      // string, sets the text for the "Remove All" button
      btnRemoveAllText: '<i class="fa fa-trash"> Quitar todas</i>'      
    });
    
    tfbox.bootstrapDualListbox('refresh');
    //tfbox.bootstrapDualListbox('destroy', true);
    //CustomizeDuallistbox('duallistbox_cargo');
    var customSettings = tfbox.bootstrapDualListbox('getContainer');
    customSettings.find('.box2').removeClass('col-md-6').addClass('col-md-12');        
    customSettings.find('.box1').removeClass('col-md-6').addClass('col-md-12');        
    elementos = $('.box1').detach().appendTo($('#datosfunciones'));        

    var t1duallistbox = $('[name="duallistbox_cargo[]"] option').length;
    var t2duallistbox = $('[name="duallistbox_cargo[]"]').val().length;

    function ver() {
        t2duallisbox = $('[name="duallistbox_cargo[]"]').val().length;
    }

    function CustomizeDuallistbox(listboxID) {
      var customSettings = $('#' + listboxID).bootstrapDualListbox('getContainer');
      var buttons = customSettings.find('.btn.moveall, .btn.move, .btn.remove, .btn.removeall');

      customSettings.find('.box1, .box2').removeClass('col-md-6').addClass('col-md-5');
      customSettings.find('.box1').after('<div class="customButtonBox col-md-2 text-center"></div>');
      customSettings.find('.customButtonBox').append(buttons);

      customSettings.find('.btn-group.buttons').remove();
    }

    function agregarlistafunciones(listafunciones) {
      console.log(listafunciones);
    }

    function dlb_repopulate (new_population) {
      $('[name=duallistbox_cargo] option').prop('selected', false);
      new_population.forEach(function(option) {
        $('[name=duallistbox_cargo] option[value="'+option+'"]').prop('selected', true);
      });
      $('[name=duallistbox_cargo]').bootstrapDualListbox('refresh', true);
    }

    function dlb_updateopts (new_opts) {
      $('[name=duallistbox_tfbox1]').empty();
      new_opts.forEach(function (opt) {
        $('[name=duallistbox_tfbox1]').append($('<option value="'+opt[0]+'">'+opt[1]+'</option>'));
      });
      $('[name=duallistbox_tfbox1]').bootstrapDualListbox('refresh', true);
    }

    function agregar(label,val) {
      tfbox.append('<option value="'+val+'">'+label+'</option>');
      tfbox.bootstrapDualListbox('refresh',true);
    }    
    </script>
    
@endsection

