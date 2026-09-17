  <script>
  $(document).ready(function(){ 
    $('#tablaordenar').DataTable({    
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'bProcessing' : true,  
      'autoWidth'   : false,
      'responsive'  : false,
      'order'       : [[1, 'asc']],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 0,2,3 ] } 
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
                       "processing": "Procesando La Información",
                       "paginate": {
                                   "first"   : " |< ",
                                   "previous": "Anterior",
                                   "next"    : "Siguiente",
                                   "last"    : " >| "
                                 }
                      },

        dom: 'Bfrtilp',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bg-blue fas fa-file-excel"></li> ',
                titleAttr : 'Exporta a excel',
                className : 'btn btn-success',
                title:     'Lista de Permisos',
                exportOptions: {
                    columns: [1]
                } 
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></li> ',
                titleAttr : 'Exporta a pdf',
                className : 'btn btn-danger',
                title:     'Lista de Permisos',
                exportOptions: {
                    columns: [1],
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
                                    [{ text: 'Lista de Permisos', alignment: 'center', fontSize: 14, bold: true, margin: [0, 10, 0, 0] }],
                                    [
                                        {
                                            text:
                                                [
                                                    { text: 'SENIAT: ', bold: true }, 'Región los Llanos\n',
                                                    { text: 'Sede: ', bold: true }, 'Calabozo',
                                                ]
                                        }
                                    ]
                                ]
                            },
                            layout: 'noBorders',
                            margin: 10
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
                className : 'btn btn-info',
                title: '&nbsp;',

                exportOptions: {
                    columns: [1],
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
                            '<br><br><div style="font-size:18px;">SENIAT: Región Los Llanos<br>Sede: Calabozo</div><div align="center" style="font-size:20px;">Lista de Permisos</div>'
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

    if( typeof MostrarMensajeSuccess !== 'undefined' && jQuery.isFunction( MostrarMensajeSuccess ) ) {
      //Es seguro ejectura la función
      MostrarMensajeSuccess(message);
    }
  });
  </script>
