          <li class="nav-item menu-close">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-folder "></i>
              <p>
                Fichas
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (Auth::user()->hasAnyPermission('1_Region'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('region')!!}" class="nav-link">
                  <i class="fas fa-map-marker-alt"></i>
                  <p>Región
                  </p>
                </a>
              </li>
              @endif
              @if (Auth::user()->hasAnyPermission('2_Division'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('division')!!}" class="nav-link">
                  <i class="fas fa-solid fa-map-signs"></i>
                  <p>División/Coordinación</p>
                </a>
              </li>
              @endif
              @if (Auth::user()->hasAnyPermission('3_Area'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('area')!!}" class="nav-link">
                  <i class="fas fa-solid fa-map-signs"></i>
                  <p>Area</p>
                </a>
              </li>
              @endif
              @if (Auth::user()->hasAnyPermission('4_Jefes'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('jefedependencia')!!}" class="nav-link">
                  <i class="fas fa-solid fa-user-tie"></i>
                  <p>Jefes</p>
                </a>
              </li>              
              @endif
              @if (Auth::user()->hasAnyPermission('5_Dependencia'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('dependencia')!!}" class="nav-link">
                  <i class="fas fa-map-marked-alt"></i>
                  <p>Dependencia</p>
                </a>
              </li>
              @endif              
              @if (Auth::user()->hasAnyPermission('6_Cargo'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('lcargos')!!}" class="nav-link">
                  <i class="fa fa-regular fa-id-card"></i>
                  <p>Cargos</p>
                </a>
              </li>
              @endif              
              @if (Auth::user()->hasAnyPermission('7_Funciones'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('lfunciones')!!}" class="nav-link">
                  <i class="fas fa-address-book"></i>
                  <p>Funciones</p>
                </a>
              </li>
              @endif 
              @if (Auth::user()->hasAnyPermission('8_Firmas'))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('firmas')!!}" class="nav-link">
                  <i class="fas fa-certificate"></i>
                  <p>Firmas</p>
                </a>
              </li>
              @endif              
            </ul>
          </li>   

          <li class="nav-item menu-close">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt "></i>
              <p>
                Control
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if (Auth::user()->hasAnyPermission(['10_Funcionarios']))              
              <li class="nav-item bg-light">
                <a href="{!!URL::to('funcionarios')!!}" class="nav-link">
                  <i class="fas fa-solid fa-street-view"></i>
                  <p>Funcionarios</p>
                </a>
              </li>
              @endif
              @if (Auth::user()->hasAnyPermission(['9_Designacion']))              
              <li class="nav-item bg-light">
                <a href="{!!URL::to('/designacion')!!}" class="nav-link">
                  <i class="fas fa-solid fa-check"></i>
                  <p>Designación</p>
                </a>
              </li>
              @endif
              @if ((Auth::user()->hasAnyPermission(['12_Capturar_Asistencia'])) ||  (Auth::user()->hasAnyPermission(['12_Listar_Asistencia'])) || (Auth::user()->hasAnyPermission(['12_Listar_Asistencia'])))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('qrcodes')!!}" class="nav-link">
                  <i class="fas fa-solid  fa-hand-pointer"></i>
                  <p>Asistencia</p>
                </a>
              </li>
              @endif
              @if ((Auth::user()->hasAnyPermission(['12_Capturar_Asistencia'])) ||  (Auth::user()->hasAnyPermission(['12_Listar_Asistencia'])) || (Auth::user()->hasAnyPermission(['12_Listar_Asistencia'])))
              <li class="nav-item bg-light">
                <a href="{!!URL::to('scan2')!!}" class="nav-link">
                  <i class="fas fa-solid  fa-hand-pointer"></i>
                  <p>Listar Asistencia</p>
                </a>
              </li>
              @endif

              @if (Auth::user()->hasAnyPermission('20_Generaqr'))
              <li class="nav-item bg-light">
                {!!Form::open(array('name' => 'formularioqr', 'route' => array('listadoqr'), 'method' => 'post', 'files' => true, 'enctype' => 'multipart/form-data'))!!} 
                <a href="#" class="nav-link" onclick="document.formularioqr.submit();">
                  <i class="fas fa-solid fa-qrcode"></i>
                  <p>Generar Qr</p>
                </a>
                {!!Form::close()!!}                  
              </li>                          
              @endif

              @if (Auth::user()->hasAnyPermission('20_Generaqr'))              <li class="nav-item bg-light">
                <a href="{!!URL::to('scan3')!!}" class="nav-link">
                  <i class="far fa-id-badge"></i>
                  <p>Registrar nuevo carnet</p>
                </a>
              </li>
              @endif

              @if (Auth::user()->hasAnyPermission('600_Votos'))              <li class="nav-item bg-light">
                <a href="{!!URL::to('votos')!!}" class="nav-link">
                  <i class="fas fa-vote-yea"></i>
                  <p>Elecciones</p>
                </a>
              </li>
              @endif
            </ul>
          </li>
    
          @if (Auth::user()->hasAnyPermission(['11_Permisos_Usuarios']))              
          <li class="nav-item menu-close">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
                Configuración
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">              
              <li class="nav-item bg-light">
                <a href="{!!URL::to('usuarios')!!}" class="nav-link">
                  <i class="fas fa-solid fa-user"></i>
                  <p>Usuarios</p>
                </a>
              </li>
              <li class="nav-item bg-light">
                <a href="{!!URL::to('roles')!!}" class="nav-link">
                  <i class="fas fa-solid fa-user-lock"></i>
                  <p>Roles</p>
                </a>
              </li>
              <li class="nav-item bg-light">
                <a href="{!!URL::to('permisos')!!}" class="nav-link">
                  <i class="fas fa-user-shield"></i>
                  <p>Permisos</p>
                </a>
              </li>

              <li class="nav-item bg-light">
                <a href="{!!URL::to('bootear')!!}" target="_blank" class="nav-link">
                  <i class="fa fa-window-restore"></i>
                  <p>Reinicar TaHu</p>
                </a>
              </li>              
              <li class="nav-item bg-light">
                <a href="{{ env('gnerales.APP_URL_TRIBUTARIO_') }}" target="_blank" class="nav-link">
                  <i class="fa fa-window-restore"></i>
                  <p>Reinicar Tributario</p>
                </a>
              </li>              
              <li class="nav-item bg-light">
                <a href="{{ config('generales.APP_URL_BINAL_') }}" target="_blank" class="nav-link">
                  <i class="fa fa-window-restore"></i>
                  <p>Reiniciar Binal</p>
                </a>
              </li>                                          
            </ul>
          </li>
          @endif
      