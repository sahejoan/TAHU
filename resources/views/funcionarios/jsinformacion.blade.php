<script>
auxid_dep = '0';   
auxestatus = 'Activo';
var table;        
$(document).ready(function(){ 
  btnroute = "{{ route('funcionarioslist') }}";
  var tokent = $("#token").val();    
  table = $('.yajra-datatable').DataTable({        
      'processing'  : true,
      'serverSide'  : true,
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'bProcessing' : true,  
      'autoWidth'   : false,
      'responsive'  : false,
      'pageLength'  : 10,
      'lengthMenu'  : [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'Todos'],
      ],
      "columns": [
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    // { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                                        
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 0,1,8,9,10 ] } 
                      ], 
      'pagingType'  : "simple_numbers",
      'language'    : {
                       "lengthMenu": "Mostrar _MENU_ Registros Por Página.",
                       "zeroRecords": "No Se Encontró Registro.",
                       "info": "_START_ a _END_ [_TOTAL_ Registros En Total]",
                       "infoEmpty": "0 de 0 de 0 registros",
                       "infoFiltered": "(Encontrado de _MAX_registros)",
                       "search": "Buscar: ",
                       "processing": "<div id='loader'><img style='width:50px; height:50px;' src='"+urlImagenLoading+"'/></div>",
                       "paginate": {
                                   "first"   : " |< ",
                                   "previous": "Anterior",
                                   "next"    : "Siguiente",
                                   "last"    : " >| "
                                 }
                      },

        dom: 'Bfrtilp',
        deferRender: true,
        ajax:{
           url : '{{ route('funcionarioslist') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json',
           data: function(d) { 
            d.id_dep = auxid_dep;
            d.estatus = auxestatus;
           },
           beforeSend: function() { $('#idcheck').val('0'); }
         },
        columns: [
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                  return '<div class="custom-control custom-checkbox">'+
                         '<input class="custom-control-input custom-control-input-danger custom-control-input-outline" type="checkbox" name="seleccionado'+value['seleccionado']+'" id="seleccionado'+value['seleccionado']+'" data-id="'+value['seleccionado']+'" value="'+value['seleccionado']+'" onclick="Seleccionar('+value['seleccionado']+');">'+
                         '<label for="seleccionado'+value['seleccionado']+'" class="custom-control-label"></label>'+
                         '</div>';                                      
              }
            },
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                  if (value['estatus']=='Activo') {
                    return '<i class="fa fa-solid fa-thumbtack" title="Designación activa"></i>';
                  }
                  if (value['estatus']=='Culminado') {
                    return '';
                  }
                  if (value['estatus']=='Jubilado') {
                    return '';
                  }
                  if (value['estatus']=='') {
                    return '';
                  }
              }
            },

            {data: 'cedula', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'cedula'},
            {data: 'apellido', className: 'col-lg-4 col-xs-2 col-sm-2 col-md-2 text-left', name: 'apellido'},
            {data: 'nombre', className: 'col-lg-4 col-xs-2 col-sm-2 col-md-2 text-left', name: 'nombre'},
            {data: 'fecha_nac', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'fecha_nac'},            
            {data: 'telefono_p', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'telefono_in'},            
            {data: 'fecha_in', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'fecha_in'},
            // {data: 'sexo', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'sexo'},
                   

            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {
                  if (value["ver"]>0) {
                    return '<center><a class="btn btn-info btn-xs far fa-id-card" title="Mostrar registro" data-toggle="modal" data-target="#modalmostrar" onclick="buscarfotofuncionarios('+value["ver"]+');"></a></center>';  
                  } else {
                    return '<center><a class="btn btn-info btn-xs far fa-id-card disabled" title="Mostrar registro" data-toggle="modal" data-target="#modalmostrar" style="pointer-events: none"></a></center>';  
                  }
                } else { return ''; }
              }
            },            
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {                
                  if (value["editar"]>0) {
                    var xroute = "{{ route('funcionarios.edit',['funcionario'=>'__idfuncionario']) }}";
                    xroute = xroute.replace('__idfuncionario', value["editar"]);
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit" title="Editar registro" href="'+xroute+'"></a></center>';
                  } else {
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit disabled" title="Editar registro" href="'+xroute+'"></a></center>';
                  }
                } else { return ''; }
              }
            },
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {                                
                  if (value["borrar"]>0) {
                    var xroute = "{{ route('funcionarios.destroy',['funcionario'=>'__idfuncionario']) }}";
                    xroute = xroute.replace('__idfuncionario', value["borrar"]);
                    return '<form method="POST" action="'+xroute+'" accept-charset="UTF-8" id="frmeliminar'+value["borrar"]+'" name="frmeliminar'+value["borrar"]+'">'+
                        '<input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a id="btneliminar'+value["borrar"]+'" data-id="'+value["borrar"]+'" class="btn btn-danger btn-xs " title="Borra registrar" href="#" onclick="confirma(this)"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  } else {
                    return '<form><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a class="btn btn-danger btn-xs disabled" title="Borra registrar" href="#" style="pointer-events: none"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  }
                } else { return ''; }
              }
            },
        ],        
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bg-blue fas fa-file-excel"></li> ',
                titleAttr : 'Exporta a excel',
                className : 'btn btn-success btn-xs',
                title:     'Lista de Funcionarios',
                exportOptions: {
                    columns: [2,3,4,5,6,7]
                } 
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></li> ',
                titleAttr : 'Exporta a pdf',
                className : 'btn btn-danger btn-xs',
                title:     'Lista de funcionarios',
                exportOptions: {
                    columns: [2,3,4,5,6,7],
                    search: 'applied',
                    order: 'applied',
                    stripNewlines: false,
                }, 
                orientation: 'portrait',
                //orientation: 'landscape',
                pageSize: 'A4',
                
                customize: function (doc) {
                    var rdoc = doc;
                    var rcout = doc.content[doc.content.length - 1].table.body.length - 1;
                    doc.content.splice(0, 1);                    
                    var now = new Date();
                    var jsDate = now.getDate() + '/' + (now.getMonth() + 1) + '/' + now.getFullYear() + '  Hora:' + now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
                    doc.pageMargins = [10, 90, 30, 30];
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.content[doc.content.length - 1].table.headerRows = 2;                  

                    var logo = urlImagenGlobal;
                    for (var i = 0; i < rcout; i++) {
                        var obj = doc.content[doc.content.length - 1].table.body[i + 1];
                        doc.content[doc.content.length - 1].table.body[(i + 1)][0] = { 
                            text: obj[0].text, 
                            style: [obj[0].style], 
                            bold: true 
                        };

                        //doc.content[doc.content.length - 1].table.body[(i + 1)][3] = {
                        //    text: obj[3].text,
                        //    style: [obj[3].style],
                        //    alignment: 'center',
                        //    bold: obj[3].text > 60 ? true : false,
                        //    fillColor: obj[3].text > 60 ? 'red' : null
                        //};
                    }

                    doc['header'] = (function (page, pages) {
                        return {
                            table: {
                                widths: ['100%'],
                                headerRows: 0,
                                body: [
                                    [{ image: logo, width: 50 }],
                                    [{ text: 'Lista de funcionarios', alignment: 'center', fontSize: 14, bold: true, margin: [0, 10, 0, 0] }],
                                    [
                                        {
                                            text:
                                                [
                                                    { text: 'Dependencia: ', bold: true }, general_dep+'\n',
                                                    { text: 'Ubicación: ', bold: true }, general_ubi+'\n',
                                                    { text: 'Región: ', bold: true }, general_reg,
                                                ]
                                        }
                                    ]
                                ]
                            },
                            layout: 'noBorders',
                            margin: 5
                        }
                    });

                    doc['footer'] = (function (page, pages) {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    text: ['Fecha: ', { text: jsDate.toString() }]
                                },
                                {
                                    alignment: 'center',
                                    text: 'Total ' + rcout.toString() + ' registros'
                                },
                                {
                                    alignment: 'right',
                                    text: ['Página ', { text: page.toString() }, ' de ', { text: pages.toString() }]
                                }
                            ],
                            margin: 10
                        }
                    });

                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return .8; };
                    objLayout['vLineWidth'] = function (i) { return .5; };
                    objLayout['hLineColor'] = function (i) { return '#aaa'; };
                    objLayout['vLineColor'] = function (i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function (i) { return 5; };
                    objLayout['paddingRight'] = function (i) { return 30; };
                    doc.content[doc.content.length - 1].layout = objLayout;                  
                },                                                                              
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></li> ',
                titleAttr : 'Imprimir',
                className : 'btn btn-info btn-xs',
                title: '&nbsp;',

                exportOptions: {
                    columns: [2,3,4,5,6,7],
                    search: 'applied',
                    order: 'applied',
                    stripNewlines: false,                    
                    stripHtml: true,
                },
                
                footer: false,
                orientation: 'portrait',
                //orientation: 'landscape',
                pageSize: 'A4',

                customize: function ( win ) {                  
                    var rcout = $("#tablaordenar tr").length-2;                    
                    var url = urlImagenLogo;
                    var now = new Date();
                    var jsDate = now.getDate() + '/' + (now.getMonth() + 1) + '/' + now.getFullYear() + '  Hora:' + now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();

                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .prepend(                          
                            '<img src="'+url+'" style="width:100px; heigth:100px; position:absolute; top:0; left:0;"/>'+
                            '<br><br><div style="font-size:18px;">Dependencia: '+general_dep+'<br>Ubicación: '+general_ubi+'<br>Región: '+general_reg+'</div><div align="center" style="font-size:20px;">Lista de funcionarios</div>'
                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )                                                
                        .css( 'font-size', 'inherit' );

                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .append(                          
                            '<div align="left"> Fecha: ' + jsDate.toString() + ' Total ' + rcout.toString() + ' registros' + '</div>'
                        );
                  
                }
            }
        ]

    });

    $('input[name="busqueda"]').change(function(){
      Refrescar();
    });

    //if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
    //  //Es seguro ejectura la función
    //  MostrarMensajeSuccess(message);
    //}
  });

  function Refrescar() {      
      auxid_dep = $('#id_dep').val();   
      auxestatus = $('#r1').val();
      if(document.getElementById('radio1').checked) {
        auxestatus = $('#radio1').val();        
      }
      if(document.getElementById('radio2').checked) {
        auxestatus = $('#radio2').val();        
      }
      if(document.getElementById('radio3').checked) {
        auxestatus = $('#radio3').val();        
      }  
      if(document.getElementById('radio4').checked) {
        auxestatus = $('#radio4').val();        
      }  

      var dataTable = $(".yajra-datatable").dataTable();    
      dataTable.fnFilter($("#busqueda").val());

      table.ajax.reload();
  }  

  function clickImprimir() {
    $('.buttons-print').click();
  }  
  function clickExportarPdf() {
    $('.buttons-pdf').click();
  }  
  function clickExportarExcel() {
    $('.buttons-excel').click();
  }
  function clickMostrar() {    
    buscarfotofuncionarios($('#idcheck').val());
    $("#modalmostrar").modal({backdrop: "static", keyboard: true});    
  }
  function clickEditar() {    
    var seleccionactual = $('#idcheck').val();
    document.location.href="{!!URL::to('funcionarios/"+seleccionactual+"/edit')!!}";
  }      
  function clickBorrar() {    
    $('#btneliminar'+$('#idcheck').val()).click();
  } 
  function clickVerDesignacionActual() {
    var id_func = $('#idcheck').val();
    if (id_func > 0) {
      buscardesignacionactual(id_func);            
    }      
  }
  function clickCrearDesignacion() {
    var id_func = $('#idcheck').val();
    if (id_func > 0) {
      document.location.href="{!!URL::to('creardesignacion/"+id_func+"/designacion')!!}";
    }      
  }
  </script>

  <script>
    function buscarfotofuncionarios(id) {           
      var route = "{{ route('verfotofuncionario') }}";
      var token = $("#tokenver").val();            
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {id: id},
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {
              if (response!="Error") {
                  var edad = calculaEdad( moment(), response.fecha_nac);
                  var fecha_nacimiento = moment(response.fecha_nac, 'YYYY-MM-DD').format('DD-MM-YYYY');
                  var fecha_ingreso = moment(response.fecha_in, 'YYYY-MM-DD').format('DD-MM-YYYY');
                  $('#fotofuncionario').remove();            
                  $('#datosfuncionario').remove();            
                  $("#resultfoto").append(`<div id="fotofuncionario" style="width: 100px; height: 100px;"><img src="`+response.foto+`" style="width: 100px; height: 100px; border:solid;"/></div>`);
                  $("#resultdatos").append(`<div id="datosfuncionario" align="left">`+
                    `<table style="font-size:11px;">`+
                    `<tr><td>Cédula: </td><td>`+response.cedula+`</td></tr>`+
                    `<tr><td>Nombre: </td><td>`+response.nombre+`</td></tr>`+
                    `<tr><td>Apellido: </td><td>`+response.apellido+`</td></tr>`+                    
                    `<tr><td>Rif: </td><td>`+response.rif+`</td></tr>`+
                    `<tr><td>Fecha de nacimiento: </td><td>`+fecha_nacimiento+`</td></tr>`+
                    `<tr><td>Edad actual: </td><td>`+edad+`</td></tr>`+
                    `<tr><td>Sexo: </td><td>`+response.sexo+`</td></tr>`+
                    `<tr><td>Cédula: </td><td>`+response.cedula+`</td></tr>`+
                    `<tr><td>Teléfono pesonal: </td><td>`+response.telefono_p+`</td></tr>`+
                    `<tr><td>Teléfono contacto: </td><td>`+response.telefono_cont+`</td></tr>`+
                    `<tr><td>Teléfono pesonal: </td><td>`+response.telefono_p+`</td></tr>`+
                    `<tr><td>Correo personal: </td><td>`+response.correo_alt+`</td></tr>`+
                    `<tr><td>Correo institucional: `+response.correo_i+`</td></tr>`+
                    `<tr><td>Correo personal: </td><td>`+response.correo_alt+`</td></tr>`+
                    `<tr><td>Twitter: </td><td>`+response.twitter+`</td></tr>`+
                    `<tr><td>Fecha de ingreso: </td><td>`+fecha_ingreso+`</td></tr>`+                    
                    `<tr><td>Condición: </td><td>`+response.condicion_in+`</td></tr>`+
                    `<tr><td>Dirección: </td><td>`+response.direccion+`</td></tr>`+
                    `<tr><td>Tipo de Sangre: </td><td>`+response.tipo_s+`</td></tr>`+
                    `<tr><td>Alergias: </td><td>`+response.alergia+`</td></tr>`+
                    `</table>`+
                    `</div>`);                    
              }
          }  
      }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }             
      }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          }                  
      });            
    }  
  </script>

  <script>  
    function buscardesignacionactual(id_func) {           
      var route = "{{ route('verdesignacionactual') }}";
      var token = $("#token").val();      
      $.ajax({
          url: route,
          headers: {'X-CSRF-TOKEN': token},
          type: "POST",
          dataType: 'json',
          data: {id_func: id_func},
          beforeSend: function() {},
          error: function(response) {},
          success: function(response) {                        
              if (response!="Error") {                
                  //var fecha_desig = moment(response.fecha_desig, 'YYYY-MM-DD').format('DD-MM-YYYY');
                  if (response.fecha_desig==null) { 
                    var fecha_desig = "No indica";
                  } else {
                    var fecha_desig = moment(response.fecha_desig, 'YYYY-MM-DD').format('DD-MM-YYYY');
                  }

                  if (response.fecha_cese==null) { 
                    var fecha_cese = "";
                  } else {
                    var fecha_cese = moment(response.fecha_cese, 'YYYY-MM-DD').format('DD-MM-YYYY');
                  }
                  $('#datosdesignacion').remove();            
                  $("#resultdatosd").append(`<div id="datosdesignacion" align="left">`+

                  `<section class="content">
                  <div class="container-fluid">
                  <div class="row">
                  <div class="col-md-12">
                  <div class="timeline">
                  <div class="time-label">
                  <span class="bg-green">Fecha de inicio: `+fecha_desig+`</span>
                  </div>              
                  <div>
                  <i class="fas fa-bookmark bg-blue"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Número</a>&nbsp;`+response.numdesig+`</h3>
                  <div class="timeline-body">`+response.nom_func+`</div>
                  <div class="timeline-footer">
                  </div>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-map-marker-alt bg-green"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header no-border"><a href="#">Región </a>&nbsp;`+ response.nom_reg+`</h3>
                  </div>
                  </div>

                  <div>
                  <i class="fas  fa-angle-right bg-purple"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header no-border"><a href="#">Dependencia </a>&nbsp;</h3>
                  <div class="timeline-body">`+response.nom_dep+`<br>Jefe: `+response.jefe_dep+`</div>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-angle-right bg-purple"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header no-border"><a href="#">División </a>&nbsp;</h3>
                  <div class="timeline-body">`+response.nom_divi+`<br>Jefe: `+response.jefe_div+`</div>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-map-pin bg-aqua"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header no-border"><a href="#">Área </a></h3>
                  <div class="timeline-body">`+response.nom_area+`</div>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-map-signs bg-green"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Ubicación</a></h3>
                  <div class="timeline-body">`+response.ubicacion+`</div>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-sun bg-yellow"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Firma Principal</a></h3>
                  <div class="timeline-body">`+response.nom_firma+`</div>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-sun bg-yellow"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Jefe Talento Humano</a></h3>
                  <div class="timeline-body">`+response.nom_jefeth+`</div>
                  </div>
                  </div>

                  <div>
                  <i class="fas fa-sun bg-yellow"></i>
                  <div class="timeline-item">
                  <span class="time"><i class="fas fa-info"></i></span>
                  <h3 class="timeline-header"><a href="#">Generado por:</a></h3>
                  <div class="timeline-body">`+response.nom_coorth+`</div>
                  <div class="timeline-footer">
                  </div>
                  </div>


                  </div>`
                  +`<div class="time-label">
                  <span class="bg-green">Fecha de culiminación: `+fecha_cese+`&nbsp;&nbsp;`+response.estatus+`</span>
                  </div>`+                                
                  `</div>
                  </div>
                  </div>
                  </div>
                  </section>`+
                  `</div>`);
                  $("#modalmostrard").modal({backdrop: "static", keyboard: true}); 
              }
          }  
      }).done(function(data, textStatus, jqXHR) {
          if ( console && console.log ) {
              console.log( "La solicitud se ha completado correctamente." );
          }             
      }).fail(function( jqXHR, textStatus, errorThrown ) {
          if ( console && console.log ) {
              console.log( "La solicitud a fallado: " +  textStatus);
          }                  
      });            
    }      
  </script>  