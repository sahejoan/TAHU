<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <meta http-equiv="content-language" content="es" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    
    <title>@yield('title', 'I-LLANOS') | {{ config('app.name', 'SENIAT') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{!!URL::to('img/logoicon.png')!!}">
    
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    @yield('page_css')
  
  <!-- Font Awesome requerido -->  
  {!!Html::style('/AdminLTE/plugins/Ionicons/css/ionicons.min.css')!!}
  {!!Html::style('/AdminLTE/plugins/fontawesome-free/css/all.min.css')!!}

  <!-- DataTables requerido -->
  {!!Html::style('/AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')!!}
  {!!Html::style('/AdminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')!!}
  {!!Html::style('/AdminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')!!}

  {!!Html::style('/AdminLTE/select2/dist/css/select2.min.css')!!}      
  {!!Html::style('/AdminLTE/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')!!}
  {!!Html::style('/AdminLTE/plugins/toastr/toastr.min.css')!!}

  <!-- Calendario style -->   
  <link href="fullcalendar/dist/css/main.min.css" rel="stylesheet" />

  <!-- Theme style -->
  {!!Html::style('/AdminLTE/dist/css/adminlte.min.css')!!}  

    <style type="text/css">/* Chart.js */
    @keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}
    </style>

    @yield('css')

</head>
<body class="sidebar-mini accent-navy" style="height: auto;">
<?php
  $urlImagen = env('APP_URL_NAME').'/img/'.'logo1.png';
  $urlImgLoading =  env('APP_URL_NAME').'/img/'.'loading_.gif';
  $img = file_get_contents($urlImagen);
  $urlImagenGlobal = 'data:image/png;base64,'.base64_encode($img);
  $urlUbicacion = env('APP_URL_GLOB');
?>

<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand bg-warning navbar-light bg-danger">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a id='botonmenu' class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!--
    <ul class="navbar-nav ml-rigth">    
      include('layouts.acciones')
    </ul>
    -->

    <!-- Right navbar links -->   
    <ul class="navbar-nav ml-auto">    
      @include('layouts.header')      
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar elevation-4 sidebar-light-danger">
    <!-- Brand Logo -->
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand" style="background:white;">        
        <a href="{{ url('/') }}">
          <img class="navbar-brand-full app-header-logo" src="{{ asset('img/logo-left.png') }}" width="100%"  alt="I-LLANOS">
        </a>
      </div>
    </aside>

    <!-- Sidebar -->
    <div class="sidebar os-theme-dark">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('img/logo-left.png') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ url('/') }}" class="d-block" style="color:#000000;">Los Llanos</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Buscar" aria-label="Buscar">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
        <div class="sidebar-search-results highlightClass"><div class="list-group" style="background:#000000;"><a href="#" class="list-group-item"><div class="search-title"><strong class="text-light"></strong>N<strong class="text-light"></strong>o<strong class="text-light"></strong> <strong class="text-light"></strong>e<strong class="text-light"></strong>l<strong class="text-light"></strong>e<strong class="text-light"></strong>m<strong class="text-light"></strong>e<strong class="text-light"></strong>n<strong class="text-light"></strong>t<strong class="text-light"></strong> <strong class="text-light"></strong>f<strong class="text-light"></strong>o<strong class="text-light"></strong>u<strong class="text-light"></strong>n<strong class="text-light"></strong>d<strong class="text-light"></strong>!<strong class="text-light"></strong></div><div class="search-path"></div></a></div></div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          @include('layouts.menu')

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="min-height: 1302.12px;">
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
          @include('alert.errors')
          @include('alert.succes')
          
          @yield('content')

      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark" style="display: none;">
    <!-- Control sidebar content goes here -->
  <div class="p-3 control-sidebar-content" style=""><h5>Customize AdminLTE</h5><hr class="mb-2"><div class="mb-4"><input type="checkbox" value="1" class="mr-1"><span>Dark Mode</span></div><h6>Header Options</h6><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Fixed</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Dropdown Legacy Offset</span></div><div class="mb-4"><input type="checkbox" value="1" class="mr-1"><span>No border</span></div><h6>Sidebar Options</h6><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Collapsed</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Fixed</span></div><div class="mb-1"><input type="checkbox" value="1" checked="checked" class="mr-1"><span>Sidebar Mini</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Sidebar Mini MD</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Sidebar Mini XS</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Nav Flat Style</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Nav Legacy Style</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Nav Compact</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Nav Child Indent</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Nav Child Hide on Collapse</span></div><div class="mb-4"><input type="checkbox" value="1" class="mr-1"><span>Disable Hover/Focus Auto-Expand</span></div><h6>Footer Options</h6><div class="mb-4"><input type="checkbox" value="1" class="mr-1"><span>Fixed</span></div><h6>Small Text Options</h6><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Body</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Navbar</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Brand</span></div><div class="mb-1"><input type="checkbox" value="1" class="mr-1"><span>Sidebar Nav</span></div><div class="mb-4"><input type="checkbox" value="1" class="mr-1"><span>Footer</span></div><h6>Navbar Variants</h6><div class="d-flex"><select class="custom-select mb-3 text-light border-0 bg-danger"><option class="bg-primary">Primary</option><option class="bg-secondary">Secondary</option><option class="bg-info">Info</option><option class="bg-success">Success</option><option class="bg-danger">Danger</option><option class="bg-indigo">Indigo</option><option class="bg-purple">Purple</option><option class="bg-pink">Pink</option><option class="bg-navy">Navy</option><option class="bg-lightblue">Lightblue</option><option class="bg-teal">Teal</option><option class="bg-cyan">Cyan</option><option class="bg-dark">Dark</option><option class="bg-gray-dark">Gray dark</option><option class="bg-gray">Gray</option><option class="bg-light">Light</option><option class="bg-warning">Warning</option><option class="bg-white">White</option><option class="bg-orange">Orange</option></select></div><h6>Accent Color Variants</h6><div class="d-flex"></div><select class="custom-select mb-3 border-0"><option>None Selected</option><option class="bg-primary">Primary</option><option class="bg-warning">Warning</option><option class="bg-info">Info</option><option class="bg-danger">Danger</option><option class="bg-success">Success</option><option class="bg-indigo">Indigo</option><option class="bg-lightblue">Lightblue</option><option class="bg-navy">Navy</option><option class="bg-purple">Purple</option><option class="bg-fuchsia">Fuchsia</option><option class="bg-pink">Pink</option><option class="bg-maroon">Maroon</option><option class="bg-orange">Orange</option><option class="bg-lime">Lime</option><option class="bg-teal">Teal</option><option class="bg-olive">Olive</option></select><h6>Dark Sidebar Variants</h6><div class="d-flex"></div><select class="custom-select mb-3 border-0"><option>None Selected</option><option class="bg-primary">Primary</option><option class="bg-warning">Warning</option><option class="bg-info">Info</option><option class="bg-danger">Danger</option><option class="bg-success">Success</option><option class="bg-indigo">Indigo</option><option class="bg-lightblue">Lightblue</option><option class="bg-navy">Navy</option><option class="bg-purple">Purple</option><option class="bg-fuchsia">Fuchsia</option><option class="bg-pink">Pink</option><option class="bg-maroon">Maroon</option><option class="bg-orange">Orange</option><option class="bg-lime">Lime</option><option class="bg-teal">Teal</option><option class="bg-olive">Olive</option></select><h6>Light Sidebar Variants</h6><div class="d-flex"></div><select class="custom-select mb-3 text-light border-0 bg-danger"><option>None Selected</option><option class="bg-primary">Primary</option><option class="bg-warning">Warning</option><option class="bg-info">Info</option><option class="bg-danger">Danger</option><option class="bg-success">Success</option><option class="bg-indigo">Indigo</option><option class="bg-lightblue">Lightblue</option><option class="bg-navy">Navy</option><option class="bg-purple">Purple</option><option class="bg-fuchsia">Fuchsia</option><option class="bg-pink">Pink</option><option class="bg-maroon">Maroon</option><option class="bg-orange">Orange</option><option class="bg-lime">Lime</option><option class="bg-teal">Teal</option><option class="bg-olive">Olive</option></select><h6>Brand Logo Variants</h6><div class="d-flex"></div><select class="custom-select mb-3 border-0 bg-danger text-light"><option>None Selected</option><option class="bg-primary">Primary</option><option class="bg-secondary">Secondary</option><option class="bg-info">Info</option><option class="bg-success">Success</option><option class="bg-danger">Danger</option><option class="bg-indigo">Indigo</option><option class="bg-purple">Purple</option><option class="bg-pink">Pink</option><option class="bg-navy">Navy</option><option class="bg-lightblue">Lightblue</option><option class="bg-teal">Teal</option><option class="bg-cyan">Cyan</option><option class="bg-dark">Dark</option><option class="bg-gray-dark">Gray dark</option><option class="bg-gray">Gray</option><option class="bg-light">Light</option><option class="bg-warning">Warning</option><option class="bg-white">White</option><option class="bg-orange">Orange</option><a href="#">clear</a></select></div></aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">

    @include('layouts.footer')

  </footer>

<!-- ./wrapper -->

@include('profile.change_password')
@include('profile.edit_profile')

</body>

@yield('page_js')

<!-- jQuery requerido-->
{!!Html::script('/AdminLTE/plugins/jquery/jquery.min.js')!!}
<!-- fin jQuery -->

<!-- Bootstrap 4 requerido-->
{!!Html::script('/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')!!}

<!-- DataTables  & Plugins requerido-->
{!!Html::script('/AdminLTE/plugins/datatables/jquery.dataTables.min.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')!!}
{!!Html::script('/AdminLTE/plugins/jszip/jszip.min.js')!!}
{!!Html::script('/AdminLTE/plugins/pdfmake/pdfmake.min.js')!!}
{!!Html::script('/AdminLTE/plugins/pdfmake/vfs_fonts.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-buttons/js/buttons.html5.min.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-buttons/js/buttons.print.min.js')!!}
{!!Html::script('/AdminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js')!!}
{!!Html::script('/AdminLTE/select2/dist/js/select2.full.min.js')!!}
{!!Html::script('/AdminLTE/plugins/bs-custom-file-input/bs-custom-file-input.min.js')!!}

{!!Html::script('/AdminLTE/plugins/sweetalert2/sweetalert2.min.js')!!}
{!!Html::script('/AdminLTE/plugins/toastr/toastr.min.js')!!}

<!-- Calendario JS -->
<script src="fullcalendar/dist/js/index.global.min.js"></script>
<script src="fullcalendar/dist/js/index.global.js"></script>

<!-- AdminLTE App requerido -->
{!!Html::script('/AdminLTE/dist/js/adminlte.min.js')!!}

<!-- Page specific script -->
@yield('scripts')

<script>
    let loggedInUser =@json(\Illuminate\Support\Facades\Auth::user());
    let loginUrl = '{{ route('login') }}';
    const userUrl = '{{url('users')}}';
    // Loading button plugin (removed from BS4)
    (function ($) {
        $.fn.button = function (action) {
            if (action === 'loading' && this.data('loading-text')) {
                this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
            }
            if (action === 'reset' && this.data('original-text')) {
                this.html(this.data('original-text')).prop('disabled', false);
            }
        };
    }(jQuery));
</script>

<?php
  $general_dep = Auth::user()->Dependencia->nom_dep;
  $general_reg = Auth::user()->Dependencia->Region->nom_reg;
  $general_ubi = Auth::user()->Dependencia->ubicacion;
?>

<script>
    //var urlImagenLogo = location.origin+"/Repositorio/Seniat/proyectoi/rr-hh/public/img/logo1.png";
    var urlImagenLogo = '<?php echo $urlImagen; ?>';
    var urlImagenLoading = '<?php echo $urlImgLoading; ?>';
    var urlImagenGlobal = '<?php echo $urlImagenGlobal; ?>';
    var urlUbicacionGlobal = '<?php echo $urlUbicacion; ?>';
    var general_dep = '<?php echo $general_dep; ?>';
    var general_reg = '<?php echo $general_reg; ?>';
    var general_ubi = '<?php echo $general_ubi; ?>';

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
</html>