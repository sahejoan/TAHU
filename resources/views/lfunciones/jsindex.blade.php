<script>
var table;        
  $(document).ready(function(){ 
  btnroute = "{{ route('lfuncioneslist') }}";
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
                    { className: "col-xs-12 col-sm-12 col-md-12 text-left" },
                    { className: "col-xs-0 col-sm-0 col-md-0 text-center" },
                    { className: "col-xs-0 col-sm-0 col-md-0 text-center" },
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 1,2 ] } 
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
           url : '{{ route('lfuncioneslist') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json'
         },
        columns: [
            {data: 'descripcion', className: 'col-xs-12 col-sm-12 col-md-12 text-left', name: 'descripcion'},
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {                
                  if (value["editar"]>0) {
                    var xroute = "{{ route('lfunciones.edit',['lfuncione'=>'__idlfuncione']) }}";
                    xroute = xroute.replace('__idlfuncione', value["editar"]);
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit" title="Editar registro" href="'+xroute+'"></a></center>';
                  } else {
                    return '<center><a class="btn btn-success btn-xs  fa fa-edit disabled" title="Editar registro" href="'+xroute+'"></a></center>';
                  }
                } else {
                  if (value["editar"]>0) {
                    var xroute = "{{ route('lfunciones.edit',['lfuncione'=>'__idlfuncione']) }}";
                    xroute = xroute.replace('__idlfuncione', value["editar"]);
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
                    var xroute = "{{ route('lfunciones.destroy',['lfuncione'=>'__idlfuncione']) }}";
                    xroute = xroute.replace('__idlfuncione', value["borrar"]);
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
                    var xroute = "{{ route('lfunciones.destroy',['lfuncione'=>'__idlfuncione']) }}";
                    xroute = xroute.replace('__idlfuncione', value["borrar"]);
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
                title:     'Lista de Funciones',
                exportOptions: {
                    columns: [0]
                } 
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></li> ',
                titleAttr : 'Exporta a pdf',
                className : 'btn btn-danger btn-xs',
                title:     'Lista de funciones',
                exportOptions: {
                    columns: [0],
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
                            bold: false
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
                                    [{ text: 'Lista de funciones', alignment: 'center', fontSize: 14, bold: true, margin: [0, 10, 0, 0] }],
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
                    columns: [0],
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
                            '<br><br><div style="font-size:18px;">Dependencia: '+general_dep+'<br>Ubicación: '+general_ubi+'<br>Región: '+general_reg+'</div><div align="center" style="font-size:20px;">Lista de funciones</div>'
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
