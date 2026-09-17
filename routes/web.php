<?php

use Illuminate\Support\Facades\Route;

//agregamos controladores
//use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BootearController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\FuncionariosController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\QRCodeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\scan2BusquedaController;
use App\Http\Controllers\scan3BusquedaController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\MonitorRegionController;
use App\Http\Controllers\MonitorLfuncionesController;
use App\Http\Controllers\MonitorDivisionController;
use App\Http\Controllers\MonitorLcargosController;
use App\Http\Controllers\MonitorAreaController;
use App\Http\Controllers\MonitorDependenciaController;
use App\Http\Controllers\MonitorJefeDependenciaController;
use App\Http\Controllers\MonitorDesignacionController;
use App\Http\Controllers\MonitorFirmasController;
use App\Http\Controllers\PermisosController;
use App\Http\Controllers\LfuncionesController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\LcargosController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\DesignacionController;
use App\Http\Controllers\JefeDependenciaController;
use App\Http\Controllers\ListadoController;
use App\Http\Controllers\CargoFuncionarioController;
use App\Http\Controllers\FamiliaresController;
use App\Http\Controllers\FirmasController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\FromController;
use App\Http\Controllers\UsuarioClaveController;
use App\Http\Controllers\VotosController;
use App\Http\Controllers\UbicacionesController;
use App\Http\Controllers\CalendarioController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//      return view('auth.login');
//});


Auth::routes();

      //Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
      
      Route::get('/', [App\Http\Controllers\FromController::class, 'index'])->name('/');
      Route::get('admin', [App\Http\Controllers\FromController::class, 'admin'])->name('admin');
      Route::get('autentificacion', [App\Http\Controllers\FromController::class, 'autentificacion'])->name('autentificacion');

      Route::get('logout', [App\Http\Controllers\LogController::class, 'logout'])->name('logout');
      Route::resource('log', LogController::class);
      Route::get('cerrarsesion', [App\Http\Controllers\LogController::class, 'CerrarSesion'])->name('cerrarsesion');
      Route::get('cierraconexion', [App\Http\Controllers\LogController::class, 'CierraConexion'])->name('cierraconexion');
      Route::get('errorsesion', [App\Http\Controllers\LogController::class, 'errorSesion'])->name('errorsesion');

      Route::resource('claveusuario', UsuarioClaveController::class);
      Route::get('modificar/claveusuario/{p}', [App\Http\Controllers\UsuarioClaveController::class, 'ModificarClave'])->name('modificarclave');
      Route::post('actualizar/claveusuario', [App\Http\Controllers\UsuarioClaveController::class, 'ActualizarClave'])->name('actualizarclave');

      Route::resource('email', MailController::class);
      Route::resource('funcionarios', FuncionariosController::class);
      Route::post('buscar/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'BuscarFuncionario'])->name('buscarfuncionario');       
      Route::get('buscar/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'index'])->name('buscarfuncionario');             
      Route::post('verfoto/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'VerFotoFuncionario'])->name('verfotofuncionario');       
      Route::post('verfamiliares/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'VerFamiliares'])->name('verfamiliares');       
      Route::post('buscarelfuncionario/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'BuscarElFuncionario'])->name('buscarelfuncionario');             
      Route::post('imprimircargafamiliar/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'ImprimirCargaFamiliar'])->name('imprimircargafamiliar');       
      Route::post('imprimirhijos12/funcionarios', [App\Http\Controllers\FuncionariosController::class, 'ImprimirHijos12'])->name('imprimirhijos12');       
      Route::post('list/funcionarios', [FuncionariosController::class, 'getFuncionarios'])->name('funcionarioslist');
      Route::post('subirarchivo/funcionarios', [FuncionariosController::class, 'SubirArchivo'])->name('subirarchivofuncionario');
      Route::post('veradjuntos/funcionarios', [FuncionariosController::class, 'VerAdjuntos'])->name('verarchivosfuncionario');
      Route::post('borraradjuntos/funcionarios', [FuncionariosController::class, 'BorrarAdjuntos'])->name('borrararchivosfuncionario');
      Route::get('descargadjuntos/funcionarios/{id}', [FuncionariosController::class, 'DescargaAdjuntos'])->name('descargachivosfuncionario');
      Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario');

      Route::resource('bootear', BootearController::class);

      Route::resource('monitor', MonitorController::class);
      Route::post('list/monitor', [MonitorController::class, 'getMonitorFuncionarios'])->name('funcionarioslistmonitor');
      Route::resource('monitorregion', MonitorRegionController::class);
      Route::post('list/monitorregion', [MonitorRegionController::class, 'getMonitorRegion'])->name('regionlistmonitor');
      Route::resource('monitordivision', MonitorDivisionController::class);
      Route::post('list/monitordivision', [MonitorDivisionController::class, 'getMonitorDivision'])->name('divisionlistmonitor');
      Route::resource('monitorarea', MonitorAreaController::class);
      Route::post('list/monitorarea', [MonitorAreaController::class, 'getMonitorArea'])->name('arealistmonitor');
      Route::resource('monitorjefedependencia', MonitorJefeDependenciaController::class);
      Route::post('list/monitorjefedependencia', [MonitorJefeDependenciaController::class, 'getMonitorJefe'])->name('jefelistmonitor');
      Route::resource('monitordependencia', MonitorDependenciaController::class);
      Route::post('list/monitordependencia', [MonitorDependenciaController::class, 'getMonitorDependencia'])->name('dependencialistmonitor');
      Route::resource('monitorlcargos', MonitorLcargosController::class);
      Route::post('list/monitorlcargos', [MonitorLcargosController::class, 'getMonitorLcargos'])->name('lcargoslistmonitor');
      Route::resource('monitorlfunciones', MonitorLfuncionesController::class);
      Route::post('list/monitorlfunciones', [MonitorLfuncionesController::class, 'getMonitorLfunciones'])->name('lfuncioneslistmonitor');
      Route::resource('monitorfirmas', MonitorFirmasController::class);
      Route::post('list/monitorfirmas', [MonitorFirmasController::class, 'getMonitorFirmas'])->name('firmaslistmonitor');
      Route::resource('monitordesignacion', MonitorDesignacionController::class);
      Route::post('list/monitordesignacion', [MonitorDesignacionController::class, 'getMonitorDesignacion'])->name('designacionlistmonitor');

      Route::resource('familiares', FamiliaresController::class);
      Route::post('guardar/familiares', [App\Http\Controllers\FamiliaresController::class, 'GuardarFamiliares'])->name('guardarfamiliares');       
      Route::post('buscar/familiares', [App\Http\Controllers\FamiliaresController::class, 'BuscarFamiliares'])->name('buscarfamiliares');       
      Route::post('actualizar/familiares', [App\Http\Controllers\FamiliaresController::class, 'ActualizarFamiliares'])->name('actualizarfamiliares');       
      Route::post('eliminar/familiares', [App\Http\Controllers\FamiliaresController::class, 'EliminarFamiliares'])->name('eliminarfamiliares');       

      Route::resource('roles', RolController::class);
      Route::post('buscar/roles', [App\Http\Controllers\RolController::class, 'BuscarRoles'])->name('buscarroles');       
      Route::get('buscar/roles', [App\Http\Controllers\RolController::class, 'index'])->name('buscarroles');       

      Route::resource('permisos', PermisosController::class);
      Route::post('buscar/permisos', [App\Http\Controllers\PermisosController::class, 'BuscarPermisos'])->name('buscarpermisos');       
      Route::get('buscar/permisos', [App\Http\Controllers\PermisosController::class, 'index'])->name('buscarpermisos');       

      Route::resource('lfunciones', LfuncionesController::class);
      Route::post('buscar/lfunciones', [App\Http\Controllers\LfuncionesController::class, 'BuscarLfunciones'])->name('buscarlfunciones');       
      Route::get('buscar/lfunciones', [App\Http\Controllers\LfuncionesController::class, 'index'])->name('buscarlfunciones');       
      Route::post('list/lfunciones', [LfuncionesController::class, 'getLfunciones'])->name('lfuncioneslist');

      Route::resource('division', DivisionController::class);
      Route::post('buscar/division', [App\Http\Controllers\DivisionController::class, 'BuscarDivision'])->name('buscardivision');       
      Route::get('buscar/division', [App\Http\Controllers\DivisionController::class, 'index'])->name('buscardivision');
      Route::post('buscarladivision/division', [App\Http\Controllers\DivisionController::class, 'BuscarLaDivision'])->name('buscarladivision');       
      Route::post('buscarladivisionarea/division', [App\Http\Controllers\DivisionController::class, 'BuscarLaDivisionArea'])->name('buscarladivisionarea');             
      Route::post('list/division', [DivisionController::class, 'getDivision'])->name('divisionlist');

      Route::resource('lcargos', LcargosController::class);
      Route::post('buscar/lcargos', [App\Http\Controllers\LcargosController::class, 'BuscarLcargos'])->name('buscarlcargos');       
      Route::get('buscar/lcargos', [App\Http\Controllers\LcargosController::class, 'index'])->name('buscarlcargos');       
      Route::post('buscarelcargo/lcargos', [App\Http\Controllers\LcargosController::class, 'BuscarElCargo'])->name('buscarelcargo');             
      Route::post('list/lcargos', [LcargosController::class, 'getLcargos'])->name('lcargoslist');

      Route::resource('usuarios', UsuarioController::class);
      Route::post('list/usuarios', [UsuarioController::class, 'getUsuarios'])->name('usuarioslist');

      Route::resource('blogs', BlogController::class);

      Route::resource('scan2', scan2BusquedaController::class);
      Route::post('verdatos/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'VerDatosFuncionario'])->name('verdatosfuncionario');
      Route::post('verdatos2/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'VerDatosFuncionario2'])->name('verdatosfuncionario2');
      Route::post('verdatos8/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'VerDatosFuncionario8'])->name('verdatosfuncionario8');      
      Route::post('verdatos82/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'VerDatosFuncionario82'])->name('verdatosfuncionario82');      
      Route::post('registrardatosfuncionario/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'RegistrarDatosFuncionario'])->name('registrardatosfuncionario');
      Route::post('registrardatosfuncionario8/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'RegistrarDatosFuncionario8'])->name('registrardatosfuncionario8');
      Route::post('buscar/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'BuscarAsistencia'])->name('buscarasistencia');       
      Route::get('buscar/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'index'])->name('buscarasistencia');             
      Route::post('imprimir/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'ImprimirAsistencia'])->name('imprimirasistencia');             
      Route::post('goasistencia/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'GOAsistencia'])->name('goasistencia');             
      Route::post('registrarpermiso/scan2', [App\Http\Controllers\scan2BusquedaController::class, 'RegistrarPermiso'])->name('registrarpermiso');             

      Route::resource('scan3', scan3BusquedaController::class);
      Route::post('registrarnuevocarnet/scan3', [App\Http\Controllers\scan3BusquedaController::class, 'RegistrarNuevoCarnet'])->name('registrarnuevocarnet');

      Route::resource('qrcodes', QRCodeController::class);
      Route::get('qrcodes', [App\Http\Controllers\QRCodeController::class, 'index'])->name('codes'); 
      
      Route::post('listadoqr/qrcodes', [App\Http\Controllers\QRCodeController::class, 'listadoqr'])->name('listadoqr');      
      Route::get('listadoqr/qrcodes', [App\Http\Controllers\QrCodeController::class, 'index'])->name('listadoqr');       
      
      Route::post('crear/qrcode', [App\Http\Controllers\QRCodeController::class, 'crearQr'])->name('crearqr');
      Route::get('crear/qrcode', [App\Http\Controllers\QrCodeController::class, 'index'])->name('crearqr');       

      Route::post('listapdf/qrcodes', [App\Http\Controllers\QRCodeController::class, 'ListaPdf'])->name('listapdf');
      Route::get('listapdf/qrcodes', [App\Http\Controllers\QrCodeController::class, 'index'])->name('listapdf');       

      Route::post('listapdfs/qrcodes', [App\Http\Controllers\QRCodeController::class, 'ListaPdfs'])->name('listapdfs');
      Route::get('listapdfs/qrcodes', [App\Http\Controllers\QrCodeController::class, 'index'])->name('listapdfs'); 
      Route::post('list/qrcodes', [QrCodeController::class, 'getListadoqr'])->name('qrlist');

      Route::post('listapdfsagrupados/qrcodes', [App\Http\Controllers\QRCodeController::class, 'ListaPdfsAgrupados'])->name('listapdfsagrupados');
      Route::get('listapdfsagrupados/qrcodes', [App\Http\Controllers\QRCodeController::class, 'index'])->name('listapdfsagrupados');

      Route::get('tarjetapdf/{id}/qrcodes', [App\Http\Controllers\QRCodeController::class, 'tarjetapdf'])->name('tarjetapdf');

      Route::resource('region', RegionController::class);
      Route::post('verregion/region', [App\Http\Controllers\RegionController::class, 'VerRegion'])->name('verregion');       
      Route::post('buscar/region', [App\Http\Controllers\RegionController::class, 'BuscarRegion'])->name('buscarregion');       
      Route::get('buscar/region', [App\Http\Controllers\RegionController::class, 'index'])->name('buscarregion');       
      Route::post('list/region', [RegionController::class, 'getRegion'])->name('regionlist');

      Route::resource('dependencia', DependenciaController::class);
      Route::post('buscar/dependencia', [App\Http\Controllers\DependenciaController::class, 'BuscarDependencia'])->name('buscardependencia');             
      Route::get('buscar/dependencia', [App\Http\Controllers\DependenciaController::class, 'index'])->name('buscardependencia');             
      Route::post('buscardependenciaregion/dependencia', [App\Http\Controllers\DependenciaController::class, 'BuscarDependenciaRegion'])->name('buscardependenciaregion');       
      Route::post('buscardependenciajefe/dependencia', [App\Http\Controllers\DependenciaController::class, 'BuscarDependenciaJefe'])->name('buscardependenciajefe');       
      Route::post('verdependencia/dependencia', [App\Http\Controllers\DependenciaController::class, 'VerDependencia'])->name('verdependencia');       
      Route::post('buscarladependencia/dependencia', [App\Http\Controllers\DependenciaController::class, 'BuscarLaDependencia'])->name('buscarladependencia');             
      Route::post('list/dependencia', [DependenciaController::class, 'getDependencia'])->name('dependencialist');

      Route::resource('designacion', DesignacionController::class);
      Route::get('buscardesignacionactual/{id}/designacion', [App\Http\Controllers\DesignacionController::class, 'BuscarDesignacionActual'])->name('buscardesignacionactual');
      Route::post('verdesignacion/designacion', [App\Http\Controllers\DesignacionController::class, 'VerDesignacion'])->name('verdesignacion');       
      Route::post('verdesignacionactual/designacion', [App\Http\Controllers\DesignacionController::class, 'VerDesignacionActual'])->name('verdesignacionactual');             
      Route::post('buscar/designacion', [App\Http\Controllers\DesignacionController::class, 'BuscarDesignacion'])->name('buscardesignacion');       
      Route::get('buscar/designacion', [App\Http\Controllers\DesignacionController::class, 'index'])->name('buscardesignacion');       
      Route::post('imprimirdesignacion/designacion', [App\Http\Controllers\DesignacionController::class, 'ImprimirDesignacion'])->name('imprimirdesignacion');       
      Route::get('creardesignacion/{id_func}/designacion', [App\Http\Controllers\DesignacionController::class, 'CrearDesignacion'])->name('creardesignacion');
      Route::post('list/designacion', [DesignacionController::class, 'getDesignacion'])->name('designacionlist');
      
      Route::resource('jefedependencia', JefeDependenciaController::class);
      Route::post('buscar/jefedependencia', [App\Http\Controllers\JefeDependenciaController::class, 'BuscarJefeDependencia'])->name('buscarjefedependencia');       
      Route::get('buscar/jefedependencia', [App\Http\Controllers\JefeDependenciaController::class, 'index'])->name('buscarjefedependencia');       
      Route::post('buscareljefe/jefedependencia', [App\Http\Controllers\JefeDependenciaController::class, 'BuscarElJefe'])->name('buscareljefe');       
      Route::post('buscareljefedivision/jefedependencia', [App\Http\Controllers\JefeDependenciaController::class, 'BuscarElJefeDivision'])->name('buscareljefedivision');       
      Route::post('buscareljefeth/jefedependencia', [App\Http\Controllers\JefeDependenciaController::class, 'BuscarElJefeTH'])->name('buscareljefeth');       
      Route::post('buscarelcoorth/jefedependencia', [App\Http\Controllers\JefeDependenciaController::class, 'BuscarElCoorTH'])->name('buscarelcoorth');       
      Route::post('list/jefedependencia', [JefeDependenciaController::class, 'getJefeDependencia'])->name('jefedependencialist');

      Route::resource('area', AreaController::class);
      Route::post('verarea/area', [App\Http\Controllers\AreaController::class, 'VerArea'])->name('verarea');       
      Route::post('buscar/area', [App\Http\Controllers\AreaController::class, 'BuscarArea'])->name('buscararea');       
      Route::get('buscar/area', [App\Http\Controllers\AreaController::class, 'index'])->name('buscararea');       
      Route::post('buscarelarea/area', [App\Http\Controllers\AreaController::class, 'BuscarElArea'])->name('buscarelarea');       
      Route::post('buscarelareadivision/area', [App\Http\Controllers\AreaController::class, 'BuscarElAreaDivision'])->name('buscarelareadivision');       
      Route::post('list/area', [AreaController::class, 'getArea'])->name('arealist');

      Route::resource('cargofuncionario', CargoFuncionarioController::class);
      Route::get('crear/{id}/cargofuncionario', [App\Http\Controllers\CargoFuncionarioController::class, 'CrearCargoFuncionario'])->name('crearcargofuncionario');             
      Route::get('editar/{id}/cargofuncionario', [App\Http\Controllers\CargoFuncionarioController::class, 'EditarCargoFuncionario'])->name('editarcargofuncionario');                   
      Route::get('vercargo/{id}/cargofuncionario', [App\Http\Controllers\CargoFuncionarioController::class, 'VerCargoFuncionario'])->name('vercargofuncionario');                   
      Route::get('borrarcargo/{id_desig}/cargofuncionario', [App\Http\Controllers\CargoFuncionarioController::class, 'BorrarCargoFuncionario'])->name('borrarcargofuncionario');                   

      Route::resource('firmas', FirmasController::class);
      Route::post('buscar/firmas', [App\Http\Controllers\FirmasController::class, 'BuscarFirmas'])->name('buscarfirmas');       
      Route::get('buscar/firmas', [App\Http\Controllers\FirmasController::class, 'index'])->name('buscarfirmas');             
      Route::post('buscarlafirma/firmas', [App\Http\Controllers\FirmasController::class, 'BuscarLaFirma'])->name('buscarlafirma');
      Route::post('list/firmas', [FirmasController::class, 'getFirmas'])->name('firmaslist');

      Route::resource('votos', VotosController::class);
      Route::get('buscar/votos', [App\Http\Controllers\VotosController::class, 'index'])->name('buscarvotos');             
      Route::post('buscarelfuncionario/votos', [App\Http\Controllers\VotosController::class, 'BuscarElFuncionarioVoto'])->name('buscarelfuncionariovoto');             
      Route::post('list/votos', [VotosController::class, 'getVotos'])->name('votoslist');
      Route::post('vercentrovotacion/votos', [App\Http\Controllers\VotosController::class, 'VerCentroVotacion'])->name('vercentrovotacion');             
      Route::post('actualizarcentro/votos', [App\Http\Controllers\VotosController::class, 'ActualizarCentroVotacion'])->name('actualizarcentrovotacion');             
      Route::post('contarvotos/votos', [App\Http\Controllers\VotosController::class, 'ContarVotos'])->name('contarvotos');             

      Route::resource('ubicaciones', UbicacionesController::class);
      Route::post('buscarelarea/ubicaciones', [App\Http\Controllers\UbicacionesController::class, 'BuscarElArea'])->name('buscarelarea');       
      Route::post('buscarladependencia/ubicaciones', [App\Http\Controllers\UbicacionesController::class, 'BuscarLaDependencia'])->name('buscarladependencia');       
      Route::post('buscarladivisio/ubicaciones', [App\Http\Controllers\UbicacionesController::class, 'BuscarLaDivision'])->name('buscarladivision');       

      Route::get('prueba', [App\Http\Controllers\PruebaController::class, 'index'])->name('prueba');       
