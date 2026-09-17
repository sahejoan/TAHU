<?php
$icono = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAACXBIWXMAAAGJAAABiQGeLhE1AAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAMhJREFUKJGtkD8LAXEAhl+cWfENDGxCWS0MBovBN5BJSb6BRfkABl9AMhtMLErJwGQwqRvUXeg6f+J+r+XInbvc4Jmf5x1eH8kggCi8cZZMeeMxmPo9im/+GRCrcR+94QzCLTBuOk7aFQCgbueoN1s43O07ZJwmi0GH5Wqbqiqzkkuy3OhSvwt+MLEE+n7DYirGTCrBdKFG5WLQhjUgBXfLEfPZEteyZpedApLiQVU5Ujjp5ET6OscXQDgScv1OAmAA0H7c/0J/ArS+xGluq7BqAAAAAElFTkSuQmCC';
$iconob = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAAZdEVYdFNvZnR3YXJlAHd3dy5pbmtzY2FwZS5vcmeb7jwaAAAAG0lEQVQoU2P8DwQMJAAmKE00GNVADBj6GhgYADs9BBRigmWoAAAAAElFTkSuQmCC';
?>
<script>
var icono = '<?php echo $icono; ?>';
var iconob = '<?php echo $iconob; ?>';

auxid_dep = '0';   
auxid_area = '0';   
auxid_divi = '0';   
auxestatus = 'Activo';
auxestatusvoto = 'No participo';
var table;        
$(document).ready(function(){ 
  btnroute = "{{ route('votoslist') }}";
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
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },
                    { className: "col-lg-2 col-xs-2 col-sm-2 col-md-2 text-left" },                    
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },                    
                    { className: "col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center" },
                  ],
      'aoColumnDefs': [ 
                        { 'bSortable': false, 'aTargets': [ 0,6,7 ] } 
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
           url : '{{ route('votoslist') }}',
           headers: {'X-CSRF-TOKEN': tokent},
           type: "POST",
           dataType: 'json',
           data: function(d) { 
            d.id_dep = auxid_dep;
            d.id_area = auxid_area;
            d.id_divi = auxid_divi;            
            d.estatus = auxestatus;
            d.estatusvoto = auxestatusvoto;
           },
           beforeSend: function() { }
         },
        columns: [
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
            {data: 'nombre', className: 'col-lg-4 col-xs-2 col-sm-2 col-md-2 text-left', name: 'nombre'},
            {data: 'apellido', className: 'col-lg-4 col-xs-2 col-sm-2 col-md-2 text-left', name: 'apellido'},
            {data: 'fecha_nac', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'fecha_nac'},            
            {data: 'telefono_p', className: 'col-lg-1 col-xs-2 col-sm-2 col-md-2 text-left', name: 'telefono_in'},            
            {
              data: null,
              bSortable: false,
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {
                  if (value["ver"]>0) {
                    return '<center><a class="btn btn-info btn-xs" title="Mostrar centro de votación" data-toggle="modal" data-target="#modalmostrar" onclick="buscarcentrovotacion('+value["ver"]+');"><i class="fas fa-map-marked-alt"></i></a></center><h1 style="color: transparent;font-Size: 0px;">*</h1>';  
                  } else {
                    return '<center><a class="btn btn-info btn-xs disabled" title="Mostrar centro de votación" data-toggle="modal" data-target="#modalmostrar" style="pointer-events: none"><i class="fas fa-map-marked-alt"></i></a></center><h1 style="color: transparent;font-Size: 0px;">*</h1>';  
                  }
                } else { 
                  return '<center><a class="btn btn-info btn-xs" title="Mostrar centro de votación" data-toggle="modal" data-target="#modalmostrar" onclick="buscarcentrovotacion('+value["ver"]+');"><i class="fas fa-map-marked-alt"></i></a></center><h1 style="color: transparent;font-Size: 0px;">*</h1>';
                }
              }
            }, 
            {
              data: null,
              bSortable: false,
              className: 'col-lg-0 col-xs-0 col-sm-0 col-md-0 text-center',
              mRender: function(data, type, value) {
                var info = table.page.info();                 
                if (info.length!=-1) {  
                  if (!value["voto"]) {                              
                    if (value["borrar"]>0) {
                      var xroute = "{{ route('votos.destroy',['voto'=>'__idvoto']) }}";
                      xroute = xroute.replace('__idvoto', value["borrar"]);
                      return '<form method="POST" action="'+xroute+'" accept-charset="UTF-8" id="frmeliminar'+value["borrar"]+'" name="frmeliminar'+value["borrar"]+'">'+
                        '<input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a id="btneliminar'+value["borrar"]+'" data-id="'+value["borrar"]+'" class="btn btn-success btn-xs" title="Actualizar voto" href="#" onclick="confirmavoto(this)"><i class="fas fa-vote-yea"></i></a></center>'+                                                                               
                        '</form><h1 style="color: transparent;font-Size: 0px;">NO</h1>';                        
                    } else {
                      return '<form><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a class="btn btn-danger btn-xs disabled" title="Actualizar voto" href="#" style="pointer-events: none"><i class="fa fa-check"></i></a></center>'+                                                                               
                        '</form><h1 style="color: transparent;font-Size: 0px;">SI</h1>';
                    }
                  } else {
                    return '<i class="fa fa-check"></i><h1 style="color: transparent;font-Size: 0px;">SI</h1>';
                  }
                } else { 
                  if (!value["voto"]) {
                    if (value["borrar"]>0) {
                      var xroute = "{{ route('votos.destroy',['voto'=>'__idvoto']) }}";
                      xroute = xroute.replace('__idvoto', value["borrar"]);
                      return '<form method="POST" action="'+xroute+'" accept-charset="UTF-8" id="frmeliminar'+value["borrar"]+'" name="frmeliminar'+value["borrar"]+'">'+
                        '<input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="{{ csrf_token() }}">'+
                        '<center><a id="btneliminar'+value["borrar"]+'" data-id="'+value["borrar"]+'" class="btn btn-success btn-xs" title="Actualizar voto" href="#" onclick="confirmavoto(this)"><i class="fas fa-vote-yea"></i></a></center>'+                                                                               
                        '</form><h1 style="color: transparent;font-Size: 0px;">NO</h1>';
                    }                    
                  }else{
                    return '<i class="fa fa-check"></i><h1 style="color: transparent;font-Size: 0px;">SI</h1>';
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
                title:     'Lista de Funcionarios',
                exportOptions: {
                    columns: [1,2,3,5,7]
                } 
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></li> ',
                titleAttr : 'Exporta a pdf',
                className : 'btn btn-danger btn-xs',
                title:     'Lista de funcionarios',
                exportOptions: {
                    columns: [1,2,3,5,7],
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

                        doc.content[doc.content.length - 1].table.body[(i + 1)][4] = { 
                            image: obj[4].text == 'NO' ? iconob : icono,
                            width: 10,
                            height: 10,
                            alignment: 'center',
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
      auxid_divi = $('#id_divi').val();   
      auxid_area = $('#id_area').val();   
      auxestatus = $('#r1').val();
      auxestatusvoto = $('#r2').val();

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
      if(document.getElementById('radio5').checked) {
        auxestatus = $('#radio5').val();        
      }  

      if(document.getElementById('radio6').checked) {
        auxestatusvoto = $('#radio6').val();        
      }

      if(document.getElementById('radio7').checked) {
        auxestatusvoto = $('#radio7').val();        
      }

      var dataTable = $(".yajra-datatable").dataTable();    
      dataTable.fnFilter($("#busqueda").val());

      table.ajax.reload();
  }  

  function clickExportarPdf() {
    $('.buttons-pdf').click();
  }  
  function clickExportarExcel() {
    $('.buttons-excel').click();
  }
  function clickBorrar() {    
    $('#btneliminar'+$('#idcheck').val()).click();
  } 

  function confirmavoto(compt) {  
    let id = compt.id;
    var idform = $("#"+id).data("id");
    document.getElementById("frmeliminar"+idform).submit();
  }  
  </script>  