<script>
auxid_dep = '0';   
auxid_area = '0';   
auxid_divi = '0';   
var auxestatus = 'Activo';
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
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-center" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-4 col-xs-4 col-sm-4 col-md-4 text-center" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                                        
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },                    
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [0,8,9,10 ] } 
                      ], 
      'pagingType'  : "simple_numbers",
      'language'    : {
                       "decimal": ",",                                   
                       "infoPostFix": "",
                       "thousands": ".",
                       "emptyTable": "No hay datos",
                       "loadingRecords": "Cargando...",        
                       "lengthMenu": "Mostrar _MENU_ Registros Por Página.",
                       "zeroRecords": "No Se Encontró Registro.",
                       "info": "_START_ a _END_ [_TOTAL_ Registros En Total]",
                       "infoEmpty": "0 de 0 de 0 registros",
                       "infoFiltered": "(Encontrado de _MAX_registros)",
                       "search": "Busqueda filtrada: ",
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
           url : '{{ route('designacionlist') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json',
           data: function(d) { 
            d.id_dep = auxid_dep;
            d.id_area = auxid_area;
            d.id_divi = auxid_divi;                                    
            d.estatus = auxestatus            
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

            {data: 'numdesig', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'numdesig'},

            {
              data: null,
              bSortable: true,              
              className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-center',              
              mRender: function(data, type, value) {
                if (value['fecha_desig']==null){
                  return '';
                } else {
                  return value['fecha_desig'];                  
                }
              }
            },

            {data: 'estatus', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'estatus'},
            {data: 'nombre', className: 'col-lg-3 col-xs-4 col-sm-4 col-md-4 text-left', name: 'nombre'},
            {data: 'nom_dep', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'nom_dep'},
            {data: 'nom_reg', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'nom_reg'},
            {data: 'ubicacion', className: 'col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left', name: 'ubicacion'},
            {data: 'nom_area', className: 'col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left', name: 'nom_area'},

            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {
                  if (value["ver"]>0) {
                    return '<center><a class="btn btn-info btn-xs far fa-id-card" title="Mostrar registro" data-toggle="modal" data-target="#modalmostrar" onclick="buscardesignacion('+value["ver"]+');"></a></center>';  
                  } else {
                    return '<center><a class="btn btn-info btn-xs far fa-id-card disabled" title="Mostrar registro" data-toggle="modal" data-target="#modalmostrar" style="pointer-events: none"></a></center>';  
                  }
                } else { 
                  if (value["ver"]>0) {
                    return '<center><a class="btn btn-info btn-xs far fa-id-card" title="Mostrar registro" data-toggle="modal" data-target="#modalmostrar" onclick="buscardesignacion('+value["ver"]+');"></a></center>';  
                  } else {
                    return '<center><a class="btn btn-info btn-xs far fa-id-card disabled" title="Mostrar registro" data-toggle="modal" data-target="#modalmostrar" style="pointer-events: none"></a></center>';  
                  }
                }
              }
            },            
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {                
                  if (value["editar"]>0) {
                    var xroute = "{{ route('designacion.edit',['designacion'=>'__iddesignacion']) }}";
                    xroute = xroute.replace('__iddesignacion', value["editar"]);
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit" title="Editar registro" href="'+xroute+'"></a></center>';
                  } else {
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit disabled" title="Editar registro" href="'+xroute+'"></a></center>';
                  }
                } else { 
                  if (value["editar"]>0) {
                    var xroute = "{{ route('designacion.edit',['designacion'=>'__iddesignacion']) }}";
                    xroute = xroute.replace('__iddesignacion', value["editar"]);
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit" title="Editar registro" href="'+xroute+'"></a></center>';
                  } else {
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit disabled" title="Editar registro" href="'+xroute+'"></a></center>';
                  }
                }
              }
            },
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {                                
                  if (value["borrar"]>0) {
                    var xroute = "{{ route('designacion.destroy',['designacion'=>'__iddesignacion']) }}";
                    xroute = xroute.replace('__iddesignacion', value["borrar"]);
                    return '<form method="POST" action="'+xroute+'" accept-charset="UTF-8" id="frmeliminar'+value["borrar"]+'" name="frmeliminar'+value["borrar"]+'">'+
                        '<input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a id="btneliminar'+value["borrar"]+'" data-id="'+value["borrar"]+'" class="btn btn-danger btn-xs " title="Borra registrar" href="#" onclick="confirma(this)"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  } else {
                    return '<form><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a class="btn btn-danger btn-xs disabled" title="Borra registrar" href="#" style="pointer-events: none"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  }
                } else { 
                  if (value["borrar"]>0) {
                    var xroute = "{{ route('designacion.destroy',['designacion'=>'__iddesignacion']) }}";
                    xroute = xroute.replace('__iddesignacion', value["borrar"]);
                    return '<form method="POST" action="'+xroute+'" accept-charset="UTF-8" id="frmeliminar'+value["borrar"]+'" name="frmeliminar'+value["borrar"]+'">'+
                        '<input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a id="btneliminar'+value["borrar"]+'" data-id="'+value["borrar"]+'" class="btn btn-danger btn-xs " title="Borra registrar" href="#" onclick="confirma(this)"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  } else {
                    return '<form><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a class="btn btn-danger btn-xs disabled" title="Borra registrar" href="#" style="pointer-events: none"><i class="fa fa-trash"></i></a></center>'+                                                                               
                        '</form>';
                  }
                }
              }
            },
        ],
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bg-blue fas fa-file-excel"></li> ',
                titleAttr : 'Exporta a excel',
                className : 'btn btn-success btn-xs',
                title:     'Lista de designaciones',
                exportOptions: {
                    columns: [1,2,3,4,5,6,7,8]
                } 
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></li> ',
                titleAttr : 'Exporta a pdf',
                className : 'btn btn-danger btn-xs',
                title:     'Lista de Asignaciones',
                exportOptions: {
                    columns: [1,2,3,4,5,6,7,8],
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
                        //
                        //doc.content[doc.content.length - 1].table.body[(i + 1)][1] = {
                        //    text: obj[1].text,
                        //    style: [obj[1].style],
                        //    alignment: 'center',
                        //    bold: obj[1].text > 60 ? true : false,
                        //    fillColor: obj[1].text > 60 ? 'red' : null
                        //};
                    }

                    doc['header'] = (function (page, pages) {
                        return {
                            table: {
                                widths: ['100%'],
                                headerRows: 0,
                                body: [
                                    [{ image: logo, width: 50 }],
                                    [{ text: 'Lista de designaciones', alignment: 'center', fontSize: 14, bold: true, margin: [0, 10, 0, 0] }],
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
                    columns: [1,2,3,4,5,6,7,8],
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
                            '<br><br><div style="font-size:18px;">Dependencia: '+general_dep+'<br>Ubicación: '+general_ubi+'<br>Región: '+general_reg+'</div><div align="center" style="font-size:20px;">Lista de designaciones</div>'
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

    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }
  });

  function Refrescar() {
      auxid_dep = $('#id_dep').val();   
      auxid_divi = $('#id_divi').val();   
      auxid_area = $('#id_area').val();   
      
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
  </script> 

    <script>  
    function buscardesignacion(id) {           
      var route = "{{ route('verdesignacion') }}";
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
                  $("#resultdatos").append(`<div id="datosdesignacion" align="left">`+

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