<div class="section">                                                                                                                                   
<div class="modal fade" id="dlgModalAdjuntos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header" style="background-color: #D5DBDB;">
              <h7 class="modal-title" id="staticBackdropLabel">Documetos adjuntos</h7>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <div class="modal-body">
              {!!Form::open(array('id'=>'formuploadfilea', 'name' => 'formuploadfilea', 'ENCTYPE'=>'multipart/form-data', 'files' => true))!!}
                        <input type="hidden" name="_tokena" id="tokena" value="{{ csrf_token() }}">                         
                        <input type="hidden" name="idupload_func" id="idupload_func" value="">                                                  
                         <input type="file" id="adjuntararchivox" name="adjuntararchivox" style="display:none" onchange="handleFiles2(this.files)"/>

              @if (Auth::user()->hasAnyPermission('10_Adjuntar_archivos_funcionarios'))
                         <a href="javascript:doClick2()" id = "examinar" title = "Solo jpeg, png, gif" class="btn-xs fa fa-search"> Examinar</a>
                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         <a href="#" id = "adjuntar" class="btn-xs disabled fa fa-paperclip" disabled="true" style="pointer-events: none"> Adjuntar</a>
              @endif
              {!!Form::close()!!}
              <div id="fileList2"></div>

              <div id="cuerpoadjuntos" class="modal-body">
              </div>

            <div class="modal-footer">
              <a href="#" data-dismiss="modal" class="btn btn-danger">Cerrar</a>
            </div>
          </div>
     </div>
</div>
</div>

