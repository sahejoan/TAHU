@if(\Illuminate\Support\Facades\Auth::user())
          <!-- User Account Menu -->          
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                    <img src="{{ asset('img/logo1.png') }}" class="user-image" alt="User Image">        
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs" style="color:white;">{{\Illuminate\Support\Facades\Auth::user()->name}}</span>
            </a>

            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                
                <img src="{{ asset('img/logo1.png') }}" class="img-circle" alt="User Image">
                
                <p>
                  <small>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->nom_dep}}</small>
                  <small>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->ubicacion}}</small>
                  <small>{{\Illuminate\Support\Facades\Auth::user()->Dependencia->Region->nom_reg}}</small>
                </p>
              </li>

              <!-- Menu Body -->
              <li class="user-body">
                <div align="center" class="pull-right">
                  <a href="{!!URL::to('modificar/claveusuario/p?=')!!}" class="btn btn-default btn-flat"><i class="fa fa-lock"> Cambiar Clave</i></a>
                </div>                
              </li>

              <!-- Menu Footer-->
              <li class="user-footer bg-danger">
                <div align="center">                  
                  <a href="{!!URL::to('logout')!!}" style="font-size=10px;font-weight: normal;color:white;" class=""><i class="fa fa-power-off"> Cerrar Sesión</i></a>
                </div>                
              </li>

            </ul>
          </li>          
@else

@endif