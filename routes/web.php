<?php

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

//Route::get('permiso/{nombre}/{slug?}', 'PermisoController@index');
//Route::view('permiso', 'permiso');
//Route::get('admin/sistema/permisos','PermisoController@index')->name('permiso');

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('seguridad/login', 'Seguridad\LoginController@index')->name('login');
Route::post('seguridad/login', 'Seguridad\LoginController@login')->name('login-post');
Route::get('seguridad/logout', 'Seguridad\LoginController@logout')->name('logout');
Route::post('ajax-sesion', 'AjaxController@setSession')->name('ajax')->middleware('auth');



Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'InicioController@index')->name('inicio');
});
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'superadmin']], function () {
    Route::get('', 'AdminController@index');
    /*RUTAS DE USUARIOS*/
    Route::get('usuario', 'UsuarioController@index')->name('usuario');
    Route::get('usuario/crear', 'UsuarioController@crear')->name('crear_usuario');
    Route::post('usuario', 'UsuarioController@guardar')->name('guardar_usuario');
    Route::get('usuario/{id}/editar', 'UsuarioController@editar')->name('editar_usuario');
    Route::put('usuario/{id}', 'UsuarioController@actualizar')->name('actualizar_usuario');
    Route::delete('usuario/{id}', 'UsuarioController@eliminar')->name('eliminar_usuario');
    Route::post('usuario/{id}/ver', 'UsuarioController@ver')->name('ver_usuario');

    /*RUTAS DEL PERMISO*/
    Route::get('permiso', 'PermisoController@index')->name('permiso');
    Route::get('permiso/crear', 'PermisoController@crear')->name('crear_permiso');
    Route::post('permiso', 'PermisoController@guardar')->name('guardar_permiso');
    Route::get('permiso/{id}/editar', 'PermisoController@editar')->name('editar_permiso');
    Route::put('permiso/{id}', 'PermisoController@actualizar')->name('actualizar_permiso');
    Route::delete('permiso/{id}', 'PermisoController@eliminar')->name('eliminar_permiso');
    /*RUTAS DEL MENU*/
    Route::get('menu', 'MenuController@index')->name('menu');
    Route::get('menu/crear', 'MenuController@crear')->name('crear_menu');
    Route::post('menu', 'MenuController@guardar')->name('guardar_menu');
    Route::get('menu/{id}/editar', 'MenuController@editar')->name('editar_menu');
    Route::get('menu/{id}/eliminar', 'MenuController@eliminar')->name('eliminar_menu');
    Route::post('menu/guardar-orden', 'MenuController@guardarOrden')->name('guardar_orden');
    Route::put('menu/{id}', 'MenuController@actualizar')->name('actualizar_menu');
    /*RUTAS ROL*/
    Route::get('rol', 'RolController@index')->name('rol');
    Route::get('rol/crear', 'RolController@crear')->name('crear_rol');
    Route::post('rol', 'RolController@guardar')->name('guardar_rol');
    Route::get('rol/{id}/editar', 'RolController@editar')->name('editar_rol');
    Route::put('rol/{id}', 'RolController@actualizar')->name('actualizar_rol');
    Route::delete('rol/{id}', 'RolController@eliminar')->name('eliminar_rol');
    /*RUTAS MENU-ROL */
    Route::get('menu-rol', 'MenuRolController@index')->name('menu_rol');
    Route::post('menu-rol', 'MenuRolController@guardar')->name('guardar_menu_rol');
    /*MENU PERMISOS-ROL*/
    Route::get('permiso-rol', 'PermisoRolController@index')->name('permiso_rol');
    Route::post('permiso-rol', 'PermisoRolController@guardar')->name('guardar_permiso_rol');

});
//MIGRACION
Route::get('install',function(){
    Artisan::call('migrate'); 

});
//EJECUTAR PHP ARTISAN STORAGE:LINK
Route::get('storagelink', function () {
    Artisan::call('storage:link');
});
//EJECUTAR INSTALACION DE 
Route::get('composerintervention', function () {
    shell_exec('composer require intervention/image');
});



//Route::group(['middleware' => ['auth']], function () {
    /*RUTAS LIBRO*/
    Route::get('libro', 'LibroController@index')->name('libro');
    Route::get('libro/crear', 'LibroController@crear')->name('crear_libro');
    Route::post('libro', 'LibroController@guardar')->name('guardar_libro');
    Route::get('libro/{id}/editar', 'LibroController@editar')->name('editar_libro');
    Route::put('libro/{id}', 'LibroController@actualizar')->name('actualizar_libro');
    Route::delete('libro/{id}', 'LibroController@eliminar')->name('eliminar_libro');
//});
/*RUTAS SUCURSAL*/
Route::get('sucursal', 'SucursalController@index')->name('sucursal');
Route::get('sucursal/crear', 'SucursalController@crear')->name('crear_sucursal');
Route::post('sucursal', 'SucursalController@guardar')->name('guardar_sucursal');
Route::get('sucursal/{id}/editar', 'SucursalController@editar')->name('editar_sucursal');
Route::put('sucursal/{id}', 'SucursalController@actualizar')->name('actualizar_sucursal');
Route::delete('sucursal/{id}', 'SucursalController@eliminar')->name('eliminar_sucursal');
Route::post('sucursal/obtProvincias', 'SucursalController@obtProvincias')->name('obtProvincias');
Route::post('sucursal/obtComunas', 'SucursalController@obtComunas')->name('obtComunas');
Route::post('sucursal/obtsucursalescategoriaprod', 'SucursalController@obtsucursalescategoriaprod')->name('obtsucursalescategoriaprod');
Route::post('sucursal/tablascolsultainv', 'SucursalController@tablasColsultaInv')->name('tablascolsultainv_sucursal');


/*RUTAS EMPRESA*/
Route::get('empresa', 'EmpresaController@index')->name('empresa');
Route::get('empresa/crear', 'EmpresaController@crear')->name('crear_empresa');
Route::post('empresa', 'EmpresaController@guardar')->name('guardar_empresa');
Route::get('empresa/{id}/editar', 'EmpresaController@editar')->name('editar_empresa');
Route::put('empresa/{id}', 'EmpresaController@actualizar')->name('actualizar_empresa');
Route::delete('empresa/{id}', 'EmpresaController@eliminar')->name('eliminar_empresa');

/*RUTAS AREAS*/
Route::get('area', 'AreaController@index')->name('area');
Route::get('area/crear', 'AreaController@crear')->name('crear_area');
Route::post('area', 'AreaController@guardar')->name('guardar_area');
Route::get('area/{id}/editar', 'AreaController@editar')->name('editar_area');
Route::put('area/{id}', 'AreaController@actualizar')->name('actualizar_area');
Route::delete('area/{id}', 'AreaController@eliminar')->name('eliminar_area');

/*RUTAS JEFATURA*/
Route::get('jefatura', 'JefaturaController@index')->name('jefatura');
Route::get('jefatura/crear', 'JefaturaController@crear')->name('crear_jefatura');
Route::post('jefatura', 'JefaturaController@guardar')->name('guardar_jefatura');
Route::get('jefatura/{id}/editar', 'JefaturaController@editar')->name('editar_jefatura');
Route::put('jefatura/{id}', 'JefaturaController@actualizar')->name('actualizar_jefatura');
Route::delete('jefatura/{id}', 'JefaturaController@eliminar')->name('eliminar_jefatura');

/*RUTAS JEFATURAAREASUC*/
Route::get('jefaturaAreaSuc', 'JefaturaAreaSucController@index')->name('jefaturaAreaSuc');
Route::post('jefaturaAreaSuc', 'JefaturaAreaSucController@guardar')->name('guardar_jefaturaAreaSuc');
Route::get('jefaturaAreaSuc/{id}/editar', 'JefaturaAreaSucController@editar')->name('editar_jefaturaAreaSuc');
Route::put('jefaturaAreaSuc/{id}', 'JefaturaAreaSucController@actualizar')->name('actualizar_jefaturaAreaSuc');
Route::post('jefaturaAreaSuc/asignarjefej', 'JefaturaAreaSucController@asignarjefej')->name('asignarjefe_jefaturaAreaSucj');
Route::post('jefaturaAreaSuc/asignarjefe', 'JefaturaAreaSucController@asignarjefe')->name('asignarjefe_jefaturaAreaSuc');

/*RUTAS CATEGORIA*/
Route::get('categoriaprod', 'CategoriaProdController@index')->name('categoriaprod');
Route::get('categoriaprodpage', 'CategoriaProdController@categoriaprodpage')->name('categoriaprodpage');
Route::get('categoriaprod/crear', 'CategoriaProdController@crear')->name('crear_categoriaprod');
Route::post('categoriaprod', 'CategoriaProdController@guardar')->name('guardar_categoriaprod');
Route::get('categoriaprod/{id}/editar', 'CategoriaProdController@editar')->name('editar_categoriaprod');
Route::put('categoriaprod/{id}', 'CategoriaProdController@actualizar')->name('actualizar_categoriaprod');
Route::delete('categoriaprod/{id}', 'CategoriaProdController@eliminar')->name('eliminar_categoriaprod');
Route::post('categoriaprod/categoriaprodArray', 'CategoriaProdController@categoriaprodArray')->name('categoriaprodArray');



/*RUTAS PRODUCTOS*/
Route::get('producto', 'ProductoController@index')->name('producto');
Route::get('productopage', 'ProductoController@productopage')->name('productopage');
Route::get('producto/crear', 'ProductoController@crear')->name('crear_producto');
Route::post('producto', 'ProductoController@guardar')->name('guardar_producto');
Route::get('producto/{id}/editar', 'ProductoController@editar')->name('editar_producto');
Route::put('producto/{id}', 'ProductoController@actualizar')->name('actualizar_producto');
Route::delete('producto/{id}', 'ProductoController@eliminar')->name('eliminar_producto');
Route::post('producto/obtClaseProd', 'ProductoController@obtClaseProd')->name('obtClaseProd');
Route::post('producto/buscarUnProducto', 'ProductoController@buscarUnProducto')->name('buscarUnProducto');
Route::get('producto/{id}/listar', 'ProductoController@listar')->name('listar_producto');
Route::post('producto/obtGrupoProd', 'ProductoController@obtGrupoProd')->name('obtGrupoProd');
Route::get('producto/{id}/acutecexportPdf', 'ProductoController@AcuTecExportPdf')->name('AcuTecExportPdf_producto');
Route::get('producto/productobuscarpage', 'ProductoController@productobuscarpage')->name('productobuscarpage_producto');
Route::get('productobuscarpage', 'ProductoController@productobuscarpage')->name('productobuscarpage');
Route::get('{id}/productobuscarpage', 'ProductoController@productobuscarpageid')->name('productobuscarpageid');
Route::get('producto/productobuscarpage', 'ProductoController@productobuscarpage')->name('productobuscarpage_producto');
Route::get('producto/{id}/productobuscarpage', 'ProductoController@productobuscarpageid')->name('productobuscarpageid_producto');



/*RUTAS CLIENTES*/
Route::get('cliente', 'ClienteController@index')->name('cliente');
Route::get('clientepage', 'ClienteController@clientepage')->name('clientepage');
Route::get('cliente/crear', 'ClienteController@crear')->name('crear_cliente');
Route::post('cliente', 'ClienteController@guardar')->name('guardar_cliente');
Route::get('cliente/{id}/editar', 'ClienteController@editar')->name('editar_cliente');
Route::put('cliente/{id}', 'ClienteController@actualizar')->name('actualizar_cliente');
Route::delete('cliente/{id}', 'ClienteController@eliminar')->name('eliminar_cliente');
Route::post('cliente/eliminarClienteDirec/{id}', 'ClienteController@eliminarClienteDirec')->name('eliminar_clienteDirec');
Route::post('cliente/buscarCli', 'ClienteController@buscarCli')->name('buscarCli');
Route::post('cliente/buscarCliId', 'ClienteController@buscarCliId')->name('buscarCliId');
Route::post('cliente/buscarClixId', 'ClienteController@buscarClixId')->name('buscarClixId');
Route::post('cliente/buscarClisinsuc', 'ClienteController@buscarClisinsuc')->name('buscarClisinsuc');
Route::post('cliente/guardarclientetemp', 'ClienteController@guardarclientetemp')->name('guardarclientetemp');
Route::post('cliente/buscarCliRut', 'ClienteController@buscarCliRut')->name('buscarCliRut');
Route::get('cliente/clientebuscarpage', 'ClienteController@clientebuscarpage')->name('clientebuscarpage_cliente');
Route::get('cliente/{id}/clientebuscarpage', 'ClienteController@clientebuscarpageid')->name('clientebuscarpageid_cliente');
Route::get('clientebuscarpage', 'ClienteController@clientebuscarpage')->name('clientebuscarpage');
Route::get('{id}/clientebuscarpage', 'ClienteController@clientebuscarpageid')->name('clientebuscarpageid');

//Ruta para actualizar el campo giro_id en la tabla clientes
Route::get('cliente/clientegiro', 'ClienteController@clientegiro')->name('clientegiro');
Route::post('cliente/buscarmyCli', 'ClienteController@buscarmyCli')->name('buscarmyCli');
Route::post('cliente/sucursalesXcliente', 'ClienteController@sucursalesXcliente')->name('sucursalesXcliente');


/*RUTAS FORMA DE PAGO*/
Route::get('formapago', 'FormaPagoController@index')->name('formapago');
Route::get('formapagopage', 'FormaPagoController@formapagopage')->name('formapagopage');
Route::get('formapago/crear', 'FormaPagoController@crear')->name('crear_formapago');
Route::post('formapago', 'FormaPagoController@guardar')->name('guardar_formapago');
Route::get('formapago/{id}/editar', 'FormaPagoController@editar')->name('editar_formapago');
Route::put('formapago/{id}', 'FormaPagoController@actualizar')->name('actualizar_formapago');
Route::delete('formapago/{id}', 'FormaPagoController@eliminar')->name('eliminar_formapago');

/*RUTAS PLAZO DE PAGO*/
Route::get('plazopago', 'PlazoPagoController@index')->name('plazopago');
Route::get('plazopago/crear', 'PlazoPagoController@crear')->name('crear_plazopago');
Route::post('plazopago', 'PlazoPagoController@guardar')->name('guardar_plazopago');
Route::get('plazopago/{id}/editar', 'PlazoPagoController@editar')->name('editar_plazopago');
Route::put('plazopago/{id}', 'PlazoPagoController@actualizar')->name('actualizar_plazopago');
Route::delete('plazopago/{id}', 'PlazoPagoController@eliminar')->name('eliminar_plazopago');

/*RUTAS CERTIFICADO*/
Route::get('certificado', 'CertificadoController@index')->name('certificado');
Route::get('certificadopage', 'CertificadoController@certificadopage')->name('certificadopage');
Route::get('certificado/crear', 'CertificadoController@crear')->name('crear_certificado');
Route::post('certificado', 'CertificadoController@guardar')->name('guardar_certificado');
Route::get('certificado/{id}/editar', 'CertificadoController@editar')->name('editar_certificado');
Route::put('certificado/{id}', 'CertificadoController@actualizar')->name('actualizar_certificado');
Route::delete('certificado/{id}', 'CertificadoController@eliminar')->name('eliminar_certificado');

/*RUTAS Materiales de Fabricacion*/
Route::get('matfabr', 'MatFabrController@index')->name('matfabr');
Route::get('matfabr/crear', 'MatFabrController@crear')->name('crear_matfabr');
Route::post('matfabr', 'MatFabrController@guardar')->name('guardar_matfabr');
Route::get('matfabr/{id}/editar', 'MatFabrController@editar')->name('editar_matfabr');
Route::put('matfabr/{id}', 'MatFabrController@actualizar')->name('actualizar_matfabr');
Route::delete('matfabr/{id}', 'MatFabrController@eliminar')->name('eliminar_matfabr');

/*RUTAS Color*/
Route::get('color', 'ColorController@index')->name('color');
Route::get('color/crear', 'ColorController@crear')->name('crear_color');
Route::post('color', 'ColorController@guardar')->name('guardar_color');
Route::get('color/{id}/editar', 'ColorController@editar')->name('editar_color');
Route::put('color/{id}', 'ColorController@actualizar')->name('actualizar_color');
Route::delete('color/{id}', 'ColorController@eliminar')->name('eliminar_color');

/*RUTAS ACUERDOTECTEMP*/
Route::get('acuerdotectemp', 'AcuerdoTecTempController@index')->name('acuerdotectemp');
Route::get('acuerdotectemp/crear', 'AcuerdoTecTempController@crear')->name('crear_acuerdotectemp');
Route::post('acuerdotectemp', 'AcuerdoTecTempController@guardar')->name('guardar_acuerdotectemp');
Route::get('acuerdotectemp/{id}/editar', 'AcuerdoTecTempController@editar')->name('editar_acuerdotectemp');
Route::put('acuerdotectemp/{id}', 'AcuerdoTecTempController@actualizar')->name('actualizar_acuerdotectemp');
Route::delete('acuerdotectemp/{id}', 'AcuerdoTecTempController@eliminar')->name('eliminar_acuerdotectemp');

/*RUTAS UNIDADMEDIDA*/
Route::get('unidadmedida', 'UnidadMedidaController@index')->name('unidadmedida');
Route::get('unidadmedidapage', 'UnidadMedidaController@unidadmedidapage')->name('unidadmedidapage');
Route::get('unidadmedida/crear', 'UnidadMedidaController@crear')->name('crear_unidadmedida');
Route::post('unidadmedida', 'UnidadMedidaController@guardar')->name('guardar_unidadmedida');
Route::get('unidadmedida/{id}/editar', 'UnidadMedidaController@editar')->name('editar_unidadmedida');
Route::put('unidadmedida/{id}', 'UnidadMedidaController@actualizar')->name('actualizar_unidadmedida');
Route::delete('unidadmedida/{id}', 'UnidadMedidaController@eliminar')->name('eliminar_unidadmedida');

/*RUTAS VENDEDORES*/
Route::get('vendedor', 'VendedorController@index')->name('vendedor');
Route::get('vendedor/crear', 'VendedorController@crear')->name('crear_vendedor');
Route::post('vendedor', 'VendedorController@guardar')->name('guardar_vendedor');
Route::get('vendedor/{id}/editar', 'VendedorController@editar')->name('editar_vendedor');
Route::put('vendedor/{id}', 'VendedorController@actualizar')->name('actualizar_vendedor');
Route::delete('vendedor/{id}', 'VendedorController@eliminar')->name('eliminar_vendedor');

/*RUTAS CARGOS*/
Route::get('cargo', 'CargoController@index')->name('cargo');
Route::get('cargo/crear', 'CargoController@crear')->name('crear_cargo');
Route::post('cargo', 'CargoController@guardar')->name('guardar_cargo');
Route::get('cargo/{id}/editar', 'CargoController@editar')->name('editar_cargo');
Route::put('cargo/{id}', 'CargoController@actualizar')->name('actualizar_cargo');
Route::delete('cargo/{id}', 'CargoController@eliminar')->name('eliminar_cargo');

/*RUTAS PERSONA*/
Route::get('persona', 'PersonaController@index')->name('persona');
Route::get('persona/crear', 'PersonaController@crear')->name('crear_persona');
Route::post('persona', 'PersonaController@guardar')->name('guardar_persona');
Route::get('persona/{id}/editar', 'PersonaController@editar')->name('editar_persona');
Route::put('persona/{id}', 'PersonaController@actualizar')->name('actualizar_persona');
Route::delete('persona/{id}', 'PersonaController@eliminar')->name('eliminar_persona');

/*RUTAS TIPOENTREGA*/
Route::get('tipoentrega', 'TipoEntregaController@index')->name('tipoentrega');
Route::get('tipoentrega/crear', 'TipoEntregaController@crear')->name('crear_tipoentrega');
Route::post('tipoentrega', 'TipoEntregaController@guardar')->name('guardar_tipoentrega');
Route::get('tipoentrega/{id}/editar', 'TipoEntregaController@editar')->name('editar_tipoentrega');
Route::put('tipoentrega/{id}', 'TipoEntregaController@actualizar')->name('actualizar_tipoentrega');
Route::delete('tipoentrega/{id}', 'TipoEntregaController@eliminar')->name('eliminar_tipoentrega');

/*RUTAS COTIZACION*/
Route::get('cotizacion', 'CotizacionController@index')->name('cotizacion');
Route::get('cotizacionpage', 'CotizacionController@cotizacionpage')->name('cotizacionpage');
Route::get('cotizacion/crear', 'CotizacionController@crear')->name('crear_cotizacion');
Route::post('cotizacion', 'CotizacionController@guardar')->name('guardar_cotizacion');
Route::get('cotizacion/{id}/editar', 'CotizacionController@editar')->name('editar_cotizacion');
Route::put('cotizacion/{id}', 'CotizacionController@actualizar')->name('actualizar_cotizacion');
Route::delete('cotizacion/{id}', 'CotizacionController@eliminar')->name('eliminar_cotizacion');
Route::post('cotizacion/eliminarCotizacionDetalle/{id}', 'CotizacionController@eliminarCotizacionDetalle')->name('eliminar_cotizaciondetalle');
Route::post('cotizacion/aprobarcotvend/{id}', 'CotizacionController@aprobarcotvend')->name('aprobarcotvend');
Route::post('cotizacion/aprobarcotsup/{id}', 'CotizacionController@aprobarcotsup')->name('aprobarcotsup');
Route::post('cotizacion/buscarCotizacion', 'CotizacionController@buscarCotizacion')->name('buscarCotizacion');
Route::get('cotizacion/{id}/exportPdf', 'CotizacionController@exportPdf')->name('exportPdf_cotizacion');
Route::get('cotizacion/{id}/{stareport}/exportPdfM', 'CotizacionController@exportPdfM')->name('exportPdfM_cotizacion');
Route::get('cotizacion/productobuscarpage', 'CotizacionController@productobuscarpage')->name('productobuscarpage');
Route::get('cotizacion/clientebuscarpage', 'CotizacionController@clientebuscarpage')->name('clientebuscarpage');
Route::get('cotizacion/{id}/productobuscarpage', 'CotizacionController@productobuscarpageid')->name('productobuscarpageid');
Route::get('cotizacion/{id}/clientebuscarpage', 'CotizacionController@clientebuscarpageid')->name('clientebuscarpageid');
Route::post('cotizacion/buscardetcot', 'CotizacionController@buscardetcot')->name('buscardetcot');
Route::post('cotizacion/updateobsdet', 'CotizacionController@updateobsdet')->name('updateobsdet');




/*RUTAS CONSULTAR COTIZACION*/
Route::get('cotizacionconsulta', 'CotizacionConsultaController@index')->name('cotizacionconsulta');
Route::post('cotizacionconsulta/reporte', 'CotizacionConsultaController@reporte')->name('cotizacionconsulta_reporte');
Route::get('cotizacionconsulta/exportPdf', 'CotizacionConsultaController@exportPdf')->name('exportPdf_cotizacionconsulta');


/*RUTAS APROBAR COTIZACION*/
Route::get('cotizacionaprobar', 'CotizacionAprobarController@index')->name('cotizacionaprobar');
Route::get('cotizacionaprobarpage', 'CotizacionAprobarController@cotizacionaprobarpage')->name('cotizacionaprobarpage');

/*RUTAS GIRO*/
Route::get('giro', 'GiroController@index')->name('giro');
Route::get('giro/crear', 'GiroController@crear')->name('crear_giro');
Route::post('giro', 'GiroController@guardar')->name('guardar_giro');
Route::get('giro/{id}/editar', 'GiroController@editar')->name('editar_giro');
Route::put('giro/{id}', 'GiroController@actualizar')->name('actualizar_giro');
Route::delete('giro/{id}', 'GiroController@eliminar')->name('eliminar_giro');

/*RUTAS NOTA DE VENTA*/
Route::get('notaventa', 'NotaVentaController@index')->name('notaventa');
//Route::get('notaventa/paginacion', 'NotaVentaController@paginacion')->name('paginacion_notaventa');
Route::get('notaventapage', 'NotaVentaController@notaventapage')->name('notaventapage');
Route::get('notaventa/crear', 'NotaVentaController@crear')->name('crear_notaventa');
Route::get('notaventa/crearcot/{id}', 'NotaVentaController@crearcot')->name('crearcot_notaventa');
Route::post('notaventa', 'NotaVentaController@guardar')->name('guardar_notaventa');
Route::get('notaventa/{id}/editar', 'NotaVentaController@editar')->name('editar_notaventa');
Route::put('notaventa/{id}', 'NotaVentaController@actualizar')->name('actualizar_notaventa');
Route::delete('notaventa/{id}', 'NotaVentaController@eliminar')->name('eliminar_notaventa');
Route::post('notaventa/eliminarDetalle/{id}', 'NotaVentaController@eliminarDetalle')->name('eliminar_notaventadetalle');
Route::post('notaventa/aprobarcotvend/{id}', 'NotaVentaController@aprobarcotvend')->name('aprobarcotvend');
Route::post('notaventa/aprobarnvsup/{id}', 'NotaVentaController@aprobarnvsup')->name('aprobarnvsup');
Route::get('notaventa/{id}/{stareport}/exportPdf', 'NotaVentaController@exportPdf')->name('exportPdf_notaventa');
Route::post('notaventa/{id}/{stareport}/exportPdfh', 'NotaVentaController@exportPdf')->name('exportPdf_notaventah');

Route::post('notaventa/aprobarnotaventa/{id}', 'NotaVentaController@aprobarnotaventa')->name('aprobar_notaventa');
Route::post('notaventa/anularnotaventa/{id}', 'NotaVentaController@anularnotaventa')->name('anular_notaventa');
Route::get('notaventacerr', 'NotaVentaController@notaventacerr')->name('notaventacerr');
Route::get('notaventacerrpage', 'NotaVentaController@notaventacerrpage')->name('notaventacerrpage');
Route::post('notaventa/visto/{id}', 'NotaVentaController@visto')->name('visto_notaventa');
Route::post('notaventa/inidespacho/{id}', 'NotaVentaController@inidespacho')->name('inidespacho_notaventa');
Route::post('notaventa/buscarguiadespacho/{id}', 'NotaVentaController@buscarguiadespacho')->name('buscarguiadespacho_notaventa');
Route::post('notaventa/actguiadespacho/{id}', 'NotaVentaController@actguiadespacho')->name('actguiadespacho_notaventa');
Route::post('notaventa/findespacho/{id}', 'NotaVentaController@findespacho')->name('findespacho_notaventa');
Route::post('notaventa/buscaroc_id', 'NotaVentaController@buscaroc_id')->name('buscaroc_id_notaventa');
Route::get('notaventa/cerrartodasNV', 'NotaVentaController@cerrartodasNV')->name('cerrartodasNV_notaventa');
Route::post('notaventa/buscarNV', 'NotaVentaController@buscarNV')->name('buscarNV_notaventa');

Route::get('notaventa/cambiarUnidadMedida', 'NotaVentaController@cambiarUnidadMedida')->name('cambiarUnidadMedida');

/*RUTAS CONSULTAR NOTA DE VENTA*/
Route::get('notaventaconsulta', 'NotaVentaConsultaController@index')->name('notaventaconsulta');
Route::post('notaventaconsulta/reporte', 'NotaVentaConsultaController@reporte')->name('notaventaconsulta_reporte');
Route::get('notaventaconsulta/exportPdf', 'NotaVentaConsultaController@exportPdf')->name('exportPdf_notaventaconsulta');

/*RUTAS CONSULTAR PRODUCTOS POR NOTA DE VENTA*/
Route::get('prodxnotaventa', 'ProducxNotaVentaController@index')->name('prodxnotaventa');
Route::post('prodxnotaventa/reporte', 'ProducxNotaVentaController@reporte')->name('prodxnotaventa_reporte');
Route::get('prodxnotaventa/exportPdf', 'ProducxNotaVentaController@exportPdf')->name('prodxnotaventa_exportPdf');


/*IMPORTAR DATOS*/
Route::get('import', 'ImportController@import')->name('import');
Route::get('importdirecciones', 'ImportController@importdirecciones')->name('importdirecciones');
Route::get('importclientesucursal', 'ImportController@importclientesucursal')->name('importclientesucursal');
Route::get('pasarDatosDeDirecAClientes', 'ImportController@pasarDatosDeDirecAClientes')->name('pasarDatosDeDirecAClientes');



/*CAMBIAR CLAVE USUARIO*/
Route::get('usuario/cambclave', 'Admin\UsuarioController@cambclave')->name('cambclave_usuario');
Route::put('usuario/actualizarclave', 'Admin\UsuarioController@actualizarclave')->name('actualizarclave_usuario');
Route::get('usuario/datosbasicos', 'Admin\UsuarioController@datosbasicos')->name('datosbasicos_usuario'); //Editar Datos Basicos
Route::put('usuario/actualizarbasicos', 'Admin\UsuarioController@actualizarbasicos')->name('actualizarbasicos_usuario'); //Actualizar Datos Basicos

/*RUTAS CotizacionAutorizarCliente*/
Route::get('CotizacionAprobarCliente', 'CotizacionAprobarClienteController@index')->name('CotizacionAprobarCliente');
Route::get('CotizacionAprobarCliente/{id}/editar', 'CotizacionAprobarClienteController@editar')->name('editar_CotizacionAprobarCliente');
Route::put('CotizacionAprobarCliente/{id}', 'CotizacionAprobarClienteController@actualizar')->name('actualizar_CotizacionAprobarCliente');

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->middleware('auth');

/*RUTAS COTIZACIONES EN TRANSITO*/
Route::get('cotizaciontrans', 'CotizacionTransController@index')->name('cotizaciontrans');
Route::get('cotizaciontranspage', 'CotizacionTransController@cotizaciontranspage')->name('cotizaciontranspage');
Route::get('cotizaciontrans/{id}/editar', 'CotizacionTransController@editar')->name('editar_cotizaciontrans');


/*RUTAS CLIENTE TEMPORAL*/
Route::get('clientetemp', 'ClienteTempController@index')->name('clientetemp');
Route::get('clientetemp/crear', 'ClienteTempController@crear')->name('crear_clientetemp');
Route::post('clientetemp', 'ClienteTempController@guardar')->name('guardar_clientetemp');
Route::get('clientetemp/{id}/editar', 'ClienteTempController@editar')->name('editar_clientetemp');
Route::put('clientetemp/{id}', 'ClienteTempController@actualizar')->name('actualizar_clientetemp');
Route::delete('clientetemp/{id}', 'ClienteTempController@eliminar')->name('eliminar_clientetemp');
Route::post('clientetemp/buscarCliTemp', 'ClienteTempController@buscarCliTemp')->name('buscarCliTemp');


/*RUTAS AREAPRODUCCION*/
Route::get('areaproduccion', 'AreaProduccionController@index')->name('areaproduccion');
Route::get('areaproduccion/crear', 'AreaProduccionController@crear')->name('crear_areaproduccion');
Route::post('areaproduccion', 'AreaProduccionController@guardar')->name('guardar_areaproduccion');
Route::get('areaproduccion/{id}/editar', 'AreaProduccionController@editar')->name('editar_areaproduccion');
Route::put('areaproduccion/{id}', 'AreaProduccionController@actualizar')->name('actualizar_areaproduccion');
Route::delete('areaproduccion/{id}', 'AreaProduccionController@eliminar')->name('eliminar_areaproduccion');


/*RUTAS CONSULTAR INDICADOR NOTA DE VENTA POR VENDEDOR*/
Route::get('nvindicadorxvend', 'NVIndicadorxVendController@index')->name('nvindicadorxvend');
Route::post('nvindicadorxvend/reporte', 'NVIndicadorxVendController@reporte')->name('nvindicadorxvend_reporte');
Route::get('nvindicadorxvend/exportPdf', 'NVIndicadorxVendController@exportPdf')->name('nvindicadorxvend_exportPdf');
Route::get('nvindicadorxvend/exportPdfkg', 'NVIndicadorxVendController@exportPdfkg')->name('nvindicadorxvend_exportPdfkg');
Route::get('nvindicadorxvend/exportPdfdn', 'NVIndicadorxVendController@exportPdfdn')->name('nvindicadorxvend_exportPdfdn');
Route::post('nvindicadorxvend/imagengrafico', 'NVIndicadorxVendController@imagengrafico')->name('nvindicadorxvend_imagengrafico');

/*RUTAS CONSULTAR INDICADORES*/
Route::get('indicadores', 'IndicadoresController@index')->name('indicadores');
Route::get('indicadores/comercial', 'IndicadoresController@indexcomercial')->name('indicadores_comercial');
Route::get('indicadores/gestion', 'IndicadoresController@indexgestion')->name('indicadores_gestion');
Route::post('indicadores/reportecomercial', 'IndicadoresController@reportecomercial')->name('indicadores_reportecomercial');
Route::post('indicadores/reportegestion', 'IndicadoresController@reportegestion')->name('indicadores_reportegestion');
//Route::post('indicadores/reporte', 'IndicadoresController@reporte')->name('indicadores_reporte');
Route::get('indicadores/comercialPdf', 'IndicadoresController@comercialPdf')->name('indicadores_comercialPdf');
Route::get('indicadores/gestionPdf', 'IndicadoresController@gestionPdf')->name('indicadores_gestionPdf');
Route::get('indicadores/exportPdfkg', 'IndicadoresController@exportPdfkg')->name('indicadores_exportPdfkg');
Route::get('indicadores/comercialPdfkg', 'IndicadoresController@comercialPdfkg')->name('indicadores_comercialPdfkg');
Route::get('indicadores/gestionPdfkg', 'IndicadoresController@gestionPdfkg')->name('indicadores_gestionPdfkg');
Route::get('indicadores/exportPdfdn', 'IndicadoresController@exportPdfdn')->name('indicadores_exportPdfdn');
Route::post('indicadores/imagengrafico', 'IndicadoresController@imagengrafico')->name('indicadores_imagengrafico');
Route::get('indicadores/repkilosxtipoentrega', 'IndicadoresController@repkilosxtipoentrega')->name('indicadores_repkilosxtipoentrega');
Route::post('indicadores/reportekilostipoentrega', 'IndicadoresController@reportekilostipoentrega')->name('indicadores_reportekilostipoentrega');
Route::get('indicadores/reportekilostipoentregapdf', 'IndicadoresController@reportekilostipoentregapdf')->name('indicadores_reportekilostipoentregapdf');


/*RUTAS APROBAR NotaVenta*/
Route::get('notaventaaprobar', 'NotaventaAprobarController@index')->name('notaventaaprobar');
Route::get('notaventaaprobarpage', 'NotaventaAprobarController@notaventaaprobarpage')->name('notaventaaprobarpage');
Route::get('notaventaaprobar/{id}/editar', 'NotaventaAprobarController@editar')->name('editar_notaventaaprobar');

/*RUTAS DESPACHO TEMPORAL NOTA DE VENTA*/
Route::get('despachotempnotaventa', 'DespachoTempNotaVentaController@index')->name('despachotempnotaventa');
Route::post('despachotempnotaventa/reporte', 'DespachoTempNotaVentaController@reporte')->name('despachotempnotaventa_reporte');
Route::get('despachotempnotaventa/exportPdf', 'DespachoTempNotaVentaController@exportPdf')->name('exportPdf_despachotempnotaventa');
Route::get('despachotempconsulta', 'DespachoTempNotaVentaController@despachotempconsulta')->name('despachotempconsulta');

/*RUTAS NO CONFORMIDADES IMAGEN*/
Route::get('noconformidadimagen', 'NoConformidadController@index')->name('noconformidadimagen');
Route::post('noconformidadimagen/{id}', 'NoConformidadController@actualizar')->name('actualizar_noconformidadimagen');
Route::post('eliminarfotoncimagen/{id}', 'NoConformidadController@eliminarfotonc')->name('eliminarfoto_noconformidadimagen');

/*RUTAS Motivo No Conformidad */
Route::get('motivonc', 'MotivoNCController@index')->name('motivonc');
Route::get('motivonc/crear', 'MotivoNCController@crear')->name('crear_motivonc');
Route::post('motivonc', 'MotivoNCController@guardar')->name('guardar_motivonc');
Route::get('motivonc/{id}/editar', 'MotivoNCController@editar')->name('editar_motivonc');
Route::put('motivonc/{id}', 'MotivoNCController@actualizar')->name('actualizar_motivonc');
Route::delete('motivonc/{id}', 'MotivoNCController@eliminar')->name('eliminar_motivonc');

/*RUTAS Forma deteccion No Conformidad */
Route::get('formadeteccionnc', 'FormaDeteccionNCController@index')->name('formadeteccionnc');
Route::get('formadeteccionnc/crear', 'FormaDeteccionNCController@crear')->name('crear_formadeteccionnc');
Route::post('formadeteccionnc', 'FormaDeteccionNCController@guardar')->name('guardar_formadeteccionnc');
Route::get('formadeteccionnc/{id}/editar', 'FormaDeteccionNCController@editar')->name('editar_formadeteccionnc');
Route::put('formadeteccionnc/{id}', 'FormaDeteccionNCController@actualizar')->name('actualizar_formadeteccionnc');
Route::delete('formadeteccionnc/{id}', 'FormaDeteccionNCController@eliminar')->name('eliminar_formadeteccionnc');

/*RUTAS No Conformidad */
Route::get('noconformidad', 'NoConformidadController@index')->name('noconformidad');
Route::get('noconformidad/crear', 'NoConformidadController@crear')->name('crear_noconformidad');
Route::post('noconformidad', 'NoConformidadController@guardar')->name('guardar_noconformidad');
Route::get('noconformidad/{id}/editar', 'NoConformidadController@editar')->name('editar_noconformidad');
Route::put('noconformidad/{id}', 'NoConformidadController@actualizar')->name('actualizar_noconformidad');
Route::delete('noconformidad/{id}', 'NoConformidadController@eliminar')->name('eliminar_noconformidad');
Route::post('noconformidadup/{id}/{sta_val}/{ininom}', 'NoConformidadController@actualizarImagen')->name('actualizarImagen_noconformidad');
Route::post('noconformidaddel/{id}', 'NoConformidadController@delImagen')->name('delImagen_noconformidad');
Route::post('noconformidadprevImg/{id}/{sta_val}', 'NoConformidadController@prevImagen')->name('prevImagen_noconformidad');
Route::get('noconformidadver/{id}/{sta_val}', 'NoConformidadController@ver')->name('ver_noconformidad');

/*RUTAS Bloquear Cliente */
Route::get('clientebloqueado', 'ClienteBloqueadoController@index')->name('clientebloqueado');
Route::get('clientebloqueadopage', 'ClienteBloqueadoController@clientebloqueadopage')->name('clientebloqueadopage');
Route::get('clientebloqueado/crear', 'ClienteBloqueadoController@crear')->name('crear_clientebloqueado');
Route::post('clientebloqueado', 'ClienteBloqueadoController@guardar')->name('guardar_clientebloqueado');
Route::get('clientebloqueado/{id}/editar', 'ClienteBloqueadoController@editar')->name('editar_clientebloqueado');
Route::put('clientebloqueado/{id}', 'ClienteBloqueadoController@actualizar')->name('actualizar_clientebloqueado');
Route::delete('clientebloqueado/{id}', 'ClienteBloqueadoController@eliminar')->name('eliminar_clientebloqueado');
Route::post('clientebloqueado/buscarclibloq', 'ClienteBloqueadoController@buscarclibloq')->name('buscarclibloq');


/*RUTAS Recepcion No Conformidad */
Route::get('noconformidadrecep', 'NoConformidadRecepController@index')->name('noconformidadrecep');
Route::get('noconformidadrecep/crear', 'NoConformidadRecepController@crear')->name('crear_noconformidadrecep');
Route::post('noconformidadrecep', 'NoConformidadRecepController@guardar')->name('guardar_noconformidadrecep');
Route::get('noconformidadrecep/{id}/{sta_val}/editar', 'NoConformidadRecepController@editar')->name('editar_noconformidadrecep');
Route::put('noconformidadrecep/{id}', 'NoConformidadRecepController@actualizar')->name('actualizar_noconformidadrecep');
Route::delete('noconformidadrecep/{id}', 'NoConformidadRecepController@eliminar')->name('eliminar_noconformidadrecep');

Route::post('noconformidadrecep/buscar/{id}', 'NoConformidadRecepController@buscar')->name('buscar_noconformidadrecep');
Route::post('noconformidadrecep/actai/{id}', 'NoConformidadRecepController@actai')->name('actai_noconformidadrecep');
Route::post('noconformidadrecep/actobsvalai/{id}', 'NoConformidadRecepController@actobsvalai')->name('actobsvalai_noconformidadrecep');
Route::post('noconformidadrecep/cumplimiento/{id}', 'NoConformidadRecepController@cumplimiento')->name('cumplimiento_noconformidadrecep');
Route::post('noconformidadrecep/incumplimiento/{id}', 'NoConformidadRecepController@incumplimiento')->name('incumplimiento_noconformidadrecep');
Route::post('noconformidadrecep/aprobpaso2/{id}', 'NoConformidadRecepController@aprobpaso2')->name('aprobpaso2_noconformidadrecep');
Route::post('noconformidadrecep/paso4/{id}', 'NoConformidadRecepController@paso4')->name('paso4_noconformidadrecep');
Route::post('noconformidadrecep/paso5/{id}', 'NoConformidadRecepController@paso5')->name('paso5_noconformidadrecep');

Route::post('noconformidadrecep/actacausa/{id}', 'NoConformidadRecepController@actacausa')->name('actacausa_noconformidadrecep');
Route::post('noconformidadrecep/actacorr/{id}', 'NoConformidadRecepController@actacorr')->name('actacorr_noconformidadrecep');
Route::post('noconformidadrecep/actfeccomp/{id}', 'NoConformidadRecepController@actfeccomp')->name('actfeccomp_noconformidadrecep');
Route::post('noconformidadrecep/actfechaguardado/{id}', 'NoConformidadRecepController@actfechaguardado')->name('actfechaguardado_noconformidadrecep');
Route::post('noconformidadrecep/actvalai/{id}', 'NoConformidadRecepController@actvalai')->name('actvalai_noconformidadrecep');

/*RUTAS Validar Notificaciones */
Route::get('notificaciones', 'NotificacionesController@notificaciones')->name('notificaciones');
Route::get('notificaciones/vista/{id}', 'NotificacionesController@vista')->name('vista_notificaciones');
Route::post('notificaciones/marcarTodasVista', 'NotificacionesController@marcarTodasVista')->name('marcarTodasVista_notificaciones');




/*RUTAS Validar No Conformidad */
Route::get('ncvalidar', 'NoConformidadValidarController@index')->name('ncvalidar');
Route::get('ncvalidar/{id}/{sta_val}/editar', 'NoConformidadValidarController@editar')->name('editar_ncvalidar');
Route::put('noconformidadvalidar/actvalAI/{id}', 'NoConformidadValidarController@actvalAI')->name('actvalAI_ncvalidar');

/*RUTAS Solicitud de Despacho*/
Route::get('despachosol', 'DespachoSolController@index')->name('despachosol');
Route::get('despachosolpage', 'DespachoSolController@despachosolpage')->name('despachosolpage');
Route::get('despachosol/listarnv', 'DespachoSolController@listarnv')->name('listarnv_despachosol');
Route::get('despachosol/crear', 'DespachoSolController@crear')->name('crear_despachosol');
Route::get('despachosol/{id}/crearsol', 'DespachoSolController@crearsol')->name('crearsol_despachosol');
Route::post('despachosol', 'DespachoSolController@guardar')->name('guardar_despachosol');
Route::get('despachosol/{id}/editar', 'DespachoSolController@editar')->name('editar_despachosol');
Route::put('despachosol/{id}', 'DespachoSolController@actualizar')->name('actualizar_despachosol');
Route::delete('despachosol/{id}', 'DespachoSolController@eliminar')->name('eliminar_despachosol');
Route::post('despachosol/reporte', 'DespachoSolController@reporte')->name('reporte_despachosol');
Route::post('despachosol/{id}/anular', 'DespachoSolController@anular')->name('anular_despachosol');
Route::get('despachosol/{id}/{stareport}/exportPdf', 'DespachoSolController@exportPdf')->name('exportPdf_despachosol');
Route::post('despachosol/aproborddesp/{id}', 'DespachoSolController@aproborddesp')->name('aproborddesp_despachosol');

Route::post('despachosol/reportesoldesp', 'DespachoSolController@reportesoldesp')->name('reportesoldesp_despachosol');
Route::post('despachosol/reportesoldespcerrarNV', 'DespachoSolController@reportesoldespcerrarNV')->name('reportesoldespcerrarNV_despachosol');
Route::get('despachosol/{id}/{stareport}/pdfSolDespPrev', 'DespachoSolController@pdfSolDespPrev')->name('pdfSolDespPrev_despachosol');
Route::post('despachosol/devolversoldesp', 'DespachoSolController@devolversoldesp')->name('devolversoldesp_despachosol');
Route::post('despachosol/cerrarsoldesp', 'DespachoSolController@cerrarsoldesp')->name('cerrarsoldesp_despachosol');
Route::get('despachosol/{id}/{stareport}/vistaprevODPdf', 'DespachoSolController@vistaprevODPdf')->name('vistaprevODPdf_despachosol');
Route::get('despachosol/reporteindex', 'DespachoSolController@reporteindex')->name('reporteindex_despachosol');
Route::get('despachosol/pdfpendientesoldesp', 'DespachoSolController@pdfpendientesoldesp')->name('despachosol_pdfpendientesoldesp');
Route::get('despachosol/pdfnotaventapendiente', 'DespachoSolController@pdfnotaventapendiente')->name('despachosol_pdfnotaventapendiente');
Route::get('despachosol/totalizarindex', 'DespachoSolController@totalizarindex')->name('despachosol_totalizarindex');
Route::post('despachosol/guardarfechaed', 'DespachoSolController@guardarfechaed')->name('guardarfechaed_despachosol');

/*RUTAS REPORTE SOLICITUD DESPACHO*/
Route::get('reportsoldesp', 'ReportSolDespController@index')->name('reportsoldesp');
Route::post('reportsoldesp/reporte', 'ReportSolDespController@reporte')->name('reportsoldesp_reporte');
Route::get('reportsoldesp/exportPdf', 'ReportSolDespController@exportPdf')->name('reportsoldesp_exportPdf');

/*RUTAS REPORTE ORDEN DESPACHO*/
Route::get('reportorddesp', 'ReportOrdDespController@index')->name('reportorddesp');
Route::get('reportorddesp/reporte', 'ReportOrdDespController@reporte')->name('reportorddesp_reporte');
Route::post('reportorddesp/reporteanterior', 'ReportOrdDespController@reporteanterior')->name('reportorddesp_reporteanterior');
Route::get('reportorddesp/exportPdf', 'ReportOrdDespController@exportPdf')->name('reportorddesp_exportPdf');
Route::get('reportorddesp/totalizarRep', 'ReportOrdDespController@totalizarRep')->name('reportorddesp_totalizarRep');

/*RUTAS REPORTE ORDEN DESPACHO GUIA FACTURA*/
Route::get('reportorddespguiafact', 'ReportOrdDespGuiaFactController@index')->name('reportorddespguiafact');
Route::get('reportorddespguiafact2', 'ReportOrdDespGuiaFactController@index2')->name('reportorddespguiafact2');
Route::post('reportorddespguiafact/reporte', 'ReportOrdDespGuiaFactController@reporte')->name('reportorddespguiafact_reporte');
Route::get('reportorddespguiafact/exportPdf', 'ReportOrdDespGuiaFactController@exportPdf')->name('reportorddespguiafact_exportPdf');
Route::get('reportorddespguiafact/indexupdateguiafact', 'ReportOrdDespGuiaFactController@indexupdateguiafact')->name('indexupdateguiafact');


/*RUTAS REPORTE ORDEN DESPACHO CERRADAS*/
Route::get('reportorddespcerrada', 'ReportOrdDespCerradaController@index')->name('reportorddespcerrada');
Route::post('reportorddespcerrada/reporte', 'ReportOrdDespCerradaController@reporte')->name('reportorddespcerrada_reporte');
Route::get('reportorddespcerrada/exportPdf', 'ReportOrdDespCerradaController@exportPdf')->name('reportorddespcerrada_exportPdf');

/*RUTAS REPORTE Nota de Venta Pendientes*/
Route::get('reportnvpendientes', 'ReportNVPendientesController@index')->name('reportnvpendientes');
Route::post('reportnvpendientes/reporte', 'ReportNVPendientesController@reporte')->name('reportnvpendientes_reporte');
Route::get('reportnvpendientes/exportPdf', 'ReportNVPendientesController@exportPdf')->name('reportnvpendientes_exportPdf');


/*
Route::get('notaventaconsulta', 'NotaVentaConsultaController@index')->name('notaventaconsulta');
Route::post('notaventaconsulta/reporte', 'NotaVentaConsultaController@reporte')->name('notaventaconsulta_reporte');
*/

/*RUTAS Orden de Despacho*/
Route::get('despachoord', 'DespachoOrdController@index')->name('despachoord');
//Route::get('despachoord', 'DespachoOrdController@listards')->name('listards_despachoord');
Route::get('despachoordpage', 'DespachoOrdController@despachoordpage')->name('despachoordpage');
Route::get('despachoord/crear', 'DespachoOrdController@crear')->name('crear_despachoord');
Route::get('despachoord/{id}/crearord', 'DespachoOrdController@crearord')->name('crearord_despachoord');
Route::post('despachoord', 'DespachoOrdController@guardar')->name('guardar_despachoord');
Route::get('despachoord/{id}/editar', 'DespachoOrdController@editar')->name('editar_despachoord');
Route::put('despachoord/{id}', 'DespachoOrdController@actualizar')->name('actualizar_despachoord');
Route::delete('despachoord/{id}', 'DespachoOrdController@eliminar')->name('eliminar_despachoord');
Route::post('despachoord/reporte', 'DespachoOrdController@reporte')->name('reporte_despachoord');
Route::post('despachoord/{id}/anular', 'DespachoOrdController@anular')->name('anular_despachoord');
Route::get('despachoord/{id}/{stareport}/exportPdf', 'DespachoOrdController@exportPdf')->name('exportPdf_despachoord');
Route::get('despachoord/indexguia', 'DespachoOrdController@indexguia')->name('indexguia_despachoord');
Route::get('despachoord/indexfact', 'DespachoOrdController@indexfact')->name('indexfact_despachoord');
Route::get('despachoord/indexcerrada', 'DespachoOrdController@indexcerrada')->name('indexcerrada_despachoord');
Route::post('despachoord/guardarguiadesp', 'DespachoOrdController@guardarguiadesp')->name('guardarguiadesp_despachoord');
Route::post('despachoord/guardarfactdesp', 'DespachoOrdController@guardarfactdesp')->name('guardarfactdesp_despachoord');
Route::post('despachoord/consultarod', 'DespachoOrdController@consultarod')->name('consultarod_despachoord');
Route::post('despachoord/aproborddesp/{id}', 'DespachoOrdController@aproborddesp')->name('aproborddesp_despachoord');
Route::post('despachoord/listarorddespxnv', 'DespachoOrdController@listarorddespxnv')->name('listarorddespxnv_despachoord');
Route::post('despachoord/buscarguiadesp', 'DespachoOrdController@buscarguiadesp')->name('buscarguiadesp_notaventa');
Route::get('despachoord/totalizarindex', 'DespachoOrdController@totalizarindex')->name('despachoord_totalizarindex');
Route::get('despachoord/listarsoldesp', 'DespachoOrdController@listarsoldesp')->name('listarsoldesp_despachoord');
Route::get('despachoordguia', 'DespachoOrdGuiaController@index')->name('despachoordguia');
Route::get('despachoordguiapage', 'DespachoOrdGuiaController@despachoordguiapage')->name('despachoordguiapage');
Route::get('despachoordguia/totalizarindex', 'DespachoOrdGuiaController@totalizarindex')->name('despachoordguia_totalizarindex');

Route::get('despachoordfact', 'DespachoOrdFactController@index')->name('despachoordfact');
Route::get('despachoordfactpage', 'DespachoOrdFactController@despachoordfactpage')->name('despachoordfactpage');
Route::get('despachoordfact/totalizarindex', 'DespachoOrdFactController@totalizarindex')->name('despachoordfact_totalizarindex');

/*RUTAS DESPACHOOBS*/
Route::get('despachoobs', 'DespachoObsController@index')->name('despachoobs');
Route::get('despachoobs/crear', 'DespachoObsController@crear')->name('crear_despachoobs');
Route::post('despachoobs', 'DespachoObsController@guardar')->name('guardar_despachoobs');
Route::get('despachoobs/{id}/editar', 'DespachoObsController@editar')->name('editar_despachoobs');
Route::put('despachoobs/{id}', 'DespachoObsController@actualizar')->name('actualizar_despachoobs');
Route::delete('despachoobs/{id}', 'DespachoObsController@eliminar')->name('eliminar_despachoobs');



/*RUTAS Anular Guia Despacho*/
Route::post('guardaranularguia', 'DespachoOrdAnulGuiaFactController@guardaranularguia')->name('guardaranularguia');

/*RUTAS NOTA DE VENTA DEVOLVER A VENDEDOR - DEVOLVER NOTA DE VENTA A VENDEDOR*/
Route::get('notaventadevolvend', 'NotaVentaDevolVendController@index')->name('notaventadevolvend');
Route::get('notaventadevolvervenpage', 'NotaVentaDevolVendController@notaventadevolvervenpage')->name('notaventadevolvervenpage');
Route::post('notaventadevolvend/actualizarreg', 'NotaVentaDevolVendController@actualizarreg')->name('actualizarreg_notaventadevolvend');

/*RUTAS NOTA DE VENTA ANULAR */
Route::get('notaventaanular', 'NotaVentaAnularController@index')->name('notaventaanular');
Route::get('notaventaanularpage', 'NotaVentaAnularController@notaventaanularpage')->name('notaventaanularpage');
Route::post('notaventaanular/actualizanular', 'NotaVentaAnularController@actualizanular')->name('actualizanular_notaventaanularpage');

/*RUTAS Carrera Nota Venta */
Route::get('notaventacerrada', 'NotaVentaCerradaController@index')->name('notaventacerrada');
Route::get('notaventacerradapage', 'NotaVentaCerradaController@notaventacerradapage')->name('notaventacerradapage');
Route::get('notaventacerrada/crear', 'NotaVentaCerradaController@crear')->name('crear_notaventacerrada');
Route::post('notaventacerrada', 'NotaVentaCerradaController@guardar')->name('guardar_notaventacerrada');
Route::get('notaventacerrada/{id}/editar', 'NotaVentaCerradaController@editar')->name('editar_notaventacerrada');
Route::put('notaventacerrada/{id}', 'NotaVentaCerradaController@actualizar')->name('actualizar_notaventacerrada');
Route::delete('notaventacerrada/{id}', 'NotaVentaCerradaController@eliminar')->name('eliminar_notaventacerrada');


/*RUTAS ESTADISTICA VENTA*/
Route::get('estadisticaventa', 'EstadisticaVentaController@index')->name('estadisticaventa');
Route::post('estadisticaventa/reporte', 'EstadisticaVentaController@reporte')->name('estadisticaventa_reporte');
Route::get('estadisticaventa/exportPdf', 'EstadisticaVentaController@exportPdf')->name('estadisticaventa_exportPdf');
Route::post('estadisticaventa/grafico', 'EstadisticaVentaController@grafico')->name('estadisticaventa_grafico');

/*RUTAS ESTADISTICA VENTA GUIAS INTERNAS*/
Route::get('estadisticaventagi', 'EstadisticaVentaGIController@index')->name('estadisticaventagi');
Route::get('estadisticaventagipage', 'EstadisticaVentaGIController@estadisticaventagipage')->name('estadisticaventagipage');
Route::get('estadisticaventagi/crear', 'EstadisticaVentaGIController@crear')->name('crear_estadisticaventagi');
Route::post('estadisticaventagi', 'EstadisticaVentaGIController@guardar')->name('guardar_estadisticaventagi');
Route::get('estadisticaventagi/{id}/editar', 'EstadisticaVentaGIController@editar')->name('editar_estadisticaventagi');
Route::put('estadisticaventagi/{id}', 'EstadisticaVentaGIController@actualizar')->name('actualizar_estadisticaventagi');
Route::delete('estadisticaventagi/{id}', 'EstadisticaVentaGIController@eliminar')->name('eliminar_estadisticaventagi');
Route::post('estadisticaventagi/reporte', 'EstadisticaVentaGIController@reporte')->name('estadisticaventagi_reporte');


/*RUTAS REPORTE Pendientes por producto*/
Route::get('reportpendientexprod', 'ReportPendienteXProdController@index')->name('reportpendientexprod');
Route::post('reportpendientexprod/reporte', 'ReportPendienteXProdController@reporte')->name('reportpendientexprod_reporte');
//Route::get('reportpendientexprod/exportPdf', 'ReportPendienteXProdController@exportPdf')->name('reportpendientexprod_exportPdf');
Route::get('reportpendientexprod/exportPdf', 'ReportPendienteXProdController@exportPdf')->name('reportpendientexprod_exportPdf');

/*RUTAS REPORTE CLIENTES*/
Route::get('reportclientes', 'ReportClientesController@index')->name('reportclientes');
Route::post('reportclientes/reporte', 'ReportClientesController@reporte')->name('reportclientes_reporte');
Route::get('reportclientes/exportPdf', 'ReportClientesController@exportPdf')->name('reportclientes_exportPdf');


/*RUTAS CATEGORIAGRUPOVALMES*/
Route::get('categoriagrupovalmes', 'CategoriaGrupoValMesController@index')->name('categoriagrupovalmes');
Route::get('categoriagrupovalmespage/{mesanno}', 'CategoriaGrupoValMesController@categoriagrupovalmespage')->name('categoriagrupovalmespage');
Route::get('categoriagrupovalmes/crear', 'CategoriaGrupoValMesController@crear')->name('crear_categoriagrupovalmes');
Route::post('categoriagrupovalmes', 'CategoriaGrupoValMesController@guardar')->name('guardar_categoriagrupovalmes');
Route::get('categoriagrupovalmes/{id}/editar', 'CategoriaGrupoValMesController@editar')->name('editar_categoriagrupovalmes');
Route::put('categoriagrupovalmes/{id}', 'CategoriaGrupoValMesController@actualizar')->name('actualizar_categoriagrupovalmes');
Route::delete('categoriagrupovalmes/{id}', 'CategoriaGrupoValMesController@eliminar')->name('eliminar_categoriagrupovalmes');
Route::post('categoriagrupovalmesfilcat', 'CategoriaGrupoValMesController@categoriagrupovalmesfilcat')->name('categoriagrupovalmesfilcat');
Route::post('categoriagrupovalmesfilgrupos', 'CategoriaGrupoValMesController@categoriagrupovalmesfilgrupos')->name('categoriagrupovalmesfilgrupos');


Route::get('reportprodpendsoldesp', 'ReportProdPendSolDespController@index')->name('reportprodpendsoldesp');
Route::post('reportprodpendsoldesp/reporte', 'ReportProdPendSolDespController@reporte')->name('reportprodpendsoldesp_reporte');
Route::get('reportprodpendsoldesp/exportpdf', 'ReportProdPendSolDespController@exportPdf')->name('reportprodpendsoldesp_exportpdf');


/*RUTAS GUIA DESPACHO INTERNA*/
Route::get('guiadespint', 'GuiaDespIntController@index')->name('guiadespint');
Route::get('guiadespintpage', 'GuiaDespIntController@guiadespintpage')->name('guiadespintpage');
Route::get('guiadespint/crear', 'GuiaDespIntController@crear')->name('crear_guiadespint');
Route::post('guiadespint', 'GuiaDespIntController@guardar')->name('guardar_guiadespint');
Route::get('guiadespint/{id}/editar', 'GuiaDespIntController@editar')->name('editar_guiadespint');
Route::put('guiadespint/{id}', 'GuiaDespIntController@actualizar')->name('actualizar_guiadespint');
Route::delete('guiadespint/{id}', 'GuiaDespIntController@eliminar')->name('eliminar_guiadespint');
Route::get('guiadespint/{id}/{stareport}/exportPdf', 'GuiaDespIntController@exportPdf')->name('exportPdf_guiadespint');


/*RUTAS Clientes Internos*/
Route::get('clienteinterno', 'ClienteInternoController@index')->name('clienteinterno');
Route::get('clienteinternopage', 'ClienteInternoController@clienteinternopage')->name('clienteinternopage');
Route::get('clienteinterno/crear', 'ClienteInternoController@crear')->name('crear_clienteinterno');
Route::post('clienteinterno', 'ClienteInternoController@guardar')->name('guardar_clienteinterno');
Route::get('clienteinterno/{id}/editar', 'ClienteInternoController@editar')->name('editar_clienteinterno');
Route::put('clienteinterno/{id}', 'ClienteInternoController@actualizar')->name('actualizar_clienteinterno');
Route::delete('clienteinterno/{id}', 'ClienteInternoController@eliminar')->name('eliminar_clienteinterno');
Route::post('clienteinterno/buscarCli', 'ClienteInternoController@buscarCli')->name('buscarCli');

/*RUTAS MATERIA PRIMA*/
Route::get('materiaprima', 'MateriaPrimaController@index')->name('materiaprima');
Route::get('materiaprimapage', 'MateriaPrimaController@materiaprimapage')->name('materiaprimapage');
Route::get('materiaprima/crear', 'MateriaPrimaController@crear')->name('crear_materiaprima');
Route::post('materiaprima', 'MateriaPrimaController@guardar')->name('guardar_materiaprima');
Route::get('materiaprima/{id}/editar', 'MateriaPrimaController@editar')->name('editar_materiaprima');
Route::put('materiaprima/{id}', 'MateriaPrimaController@actualizar')->name('actualizar_materiaprima');
Route::delete('materiaprima/{id}', 'MateriaPrimaController@eliminar')->name('eliminar_materiaprima');


/*RUTAS NOTAS DE VENTA EN TRANSITO*/
Route::get('notaventatrans', 'NotaVentaTransController@index')->name('notaventatrans');
Route::get('notaventatranspage', 'NotaVentaTransController@notaventatranspage')->name('NotaVentaTranspage');


/*RUTAS Rechazo ORDEN DE DEPACHO*/
Route::get('despachoordrec', 'DespachoOrdRecController@index')->name('despachoordrec');
Route::get('despachoordrec/indexapr', 'DespachoOrdRecController@indexapr')->name('indexapr_despachoordrec');
Route::get('despachoordrecpage', 'DespachoOrdRecController@despachoordrecpage')->name('despachoordrecpage');
Route::get('despachoordrecpageapr', 'DespachoOrdRecController@despachoordrecpageapr')->name('despachoordrecpageapr');
Route::get('despachoordrec/crear', 'DespachoOrdRecController@crear')->name('crear_despachoordrec');
Route::post('despachoordrec', 'DespachoOrdRecController@guardar')->name('guardar_despachoordrec');
Route::get('despachoordrec/{id}/editar', 'DespachoOrdRecController@editar')->name('editar_despachoordrec');
Route::put('despachoordrec/{id}', 'DespachoOrdRecController@actualizar')->name('actualizar_despachoordrec');
Route::delete('despachoordrec/{id}', 'DespachoOrdRecController@eliminar')->name('eliminar_despachoordrec');
Route::get('despachoordrec/consultadespordfact', 'DespachoOrdRecController@consultadespordfact')->name('consultadespordfact_despachoordrec');
Route::get('despachoordrec/reporte', 'DespachoOrdRecController@reporte')->name('reporte_despachoordrec');
Route::get('despachoordrec/crearrec/{id}', 'DespachoOrdRecController@crearrec')->name('crearrec_despachoordrec');
Route::post('despachoordrec/{id}/anular', 'DespachoOrdRecController@anular')->name('anular_despachoordrec');
Route::get('despachoordrec/{id}/{stareport}/exportPdf', 'DespachoOrdRecController@exportPdf')->name('exportPdf_despachoordrec');
Route::post('despachoordrec/valNCCerrada', 'DespachoOrdRecController@valNCCerrada')->name('valNCCerrada_despachoordrec');
Route::post('despachoordrec/enviaraprorecod', 'DespachoOrdRecController@enviaraprorecod')->name('enviaraprorecod_despachoordrec');
Route::post('despachoordrec/aprorecod', 'DespachoOrdRecController@aprorecod')->name('aprorecod_despachoordrec');

/*RUTAS Aprobar Rechazo Orden Despacho*/
Route::get('despachoordrecapr', 'DespachoOrdRecAprController@index')->name('despachoordrecapr');
Route::get('despachoordrecpageapr', 'DespachoOrdRecAprController@despachoordrecpageapr')->name('despachoordrecpageapr');


/*RUTAS Motivo Rechazo Orden Despacho*/
Route::get('despachoordrecmotivo', 'DespachoOrdRecMotivoController@index')->name('despachoordrecmotivo');
Route::get('despachoordrecmotivopage', 'DespachoOrdRecMotivoController@despachoordrecmotivopage')->name('despachoordrecmotivopage');
Route::get('despachoordrecmotivo/crear', 'DespachoOrdRecMotivoController@crear')->name('crear_despachoordrecmotivo');
Route::post('despachoordrecmotivo', 'DespachoOrdRecMotivoController@guardar')->name('guardar_despachoordrecmotivo');
Route::get('despachoordrecmotivo/{id}/editar', 'DespachoOrdRecMotivoController@editar')->name('editar_despachoordrecmotivo');
Route::put('despachoordrecmotivo/{id}', 'DespachoOrdRecMotivoController@actualizar')->name('actualizar_despachoordrecmotivo');
Route::delete('despachoordrecmotivo/{id}', 'DespachoOrdRecMotivoController@eliminar')->name('eliminar_despachoordrecmotivo');

/*RUTAS REPORTE RECHAZO ORDEN DESPACHO*/
Route::get('reportorddesprec', 'ReportOrdDespRecController@index')->name('reportorddesprec');
Route::get('reportorddesprec/reporte', 'ReportOrdDespRecController@reporte')->name('reportorddesprec_reporte');
Route::get('reportorddesprec/exportPdf', 'ReportOrdDespRecController@exportPdf')->name('reportorddesprec_exportPdf');
Route::get('reportorddesprec/totalizarRep', 'ReportOrdDespRecController@totalizarRep')->name('reportorddesprec_totalizarRep');

/*RUTAS REPORTE MOVIMIENTO SOLICITUD DESPACHO*/
Route::get('reportmovsoldesp', 'ReportMovSolDespController@index')->name('reportmovsoldesp');
Route::post('reportmovsoldesp/reporte', 'ReportMovSolDespController@reporte')->name('reportmovsoldesp_reporte');
Route::get('reportmovsoldesp/exportPdf', 'ReportMovSolDespController@exportPdf')->name('reportmovsoldesp_exportPdf');

/*RUTAS INVBODEGA*/
Route::get('invbodega', 'InvBodegaController@index')->name('invbodega');
Route::get('invbodegapage', 'InvBodegaController@invbodegapage')->name('invbodegapage');
Route::get('invbodega/crear', 'InvBodegaController@crear')->name('crear_invbodega');
Route::post('invbodega', 'InvBodegaController@guardar')->name('guardar_invbodega');
Route::get('invbodega/{id}/editar', 'InvBodegaController@editar')->name('editar_invbodega');
Route::put('invbodega/{id}', 'InvBodegaController@actualizar')->name('actualizar_invbodega');
Route::delete('invbodega/{id}', 'InvBodegaController@eliminar')->name('eliminar_invbodega');
Route::post('invbodega/obtbodegasxsucursal', 'InvBodegaController@obtbodegasxsucursal')->name('obtbodegasxsucursal');
Route::post('invbodega/buscarTipoBodegaOrdDesp', 'InvBodegaController@buscarTipoBodegaOrdDesp')->name('buscarTipoBodegaOrdDesp');


/*RUTAS INVENTARIO TIPO MOVIMIENTO*/
Route::get('invmovtipo', 'InvMovTipoController@index')->name('invmovtipo');
Route::get('invmovtipopage', 'InvMovTipoController@invmovtipopage')->name('invmovtipopage');
Route::get('invmovtipo/crear', 'InvMovTipoController@crear')->name('crear_invmovtipo');
Route::post('invmovtipo', 'InvMovTipoController@guardar')->name('guardar_invmovtipo');
Route::get('invmovtipo/{id}/editar', 'InvMovTipoController@editar')->name('editar_invmovtipo');
Route::put('invmovtipo/{id}', 'InvMovTipoController@actualizar')->name('actualizar_invmovtipo');
Route::delete('invmovtipo/{id}', 'InvMovTipoController@eliminar')->name('eliminar_invmovtipo');

/*RUTAS Entradas y Salidas de Inventario*/
Route::get('inventsal', 'InvEntSalController@index')->name('inventsal');
Route::get('inventsalpage', 'InvEntSalController@inventsalpage')->name('inventsalpage');
Route::get('inventsal/crear', 'InvEntSalController@crear')->name('crear_inventsal');
Route::post('inventsal', 'InvEntSalController@guardar')->name('guardar_inventsal');
Route::get('inventsal/{id}/editar', 'InvEntSalController@editar')->name('editar_inventsal');
Route::put('inventsal/{id}', 'InvEntSalController@actualizar')->name('actualizar_inventsal');
Route::delete('inventsal/{id}', 'InvEntSalController@eliminar')->name('eliminar_inventsal');
Route::post('inventsal/enviaraprobarinventsal/{id}', 'InvEntSalController@enviaraprobarinventsal')->name('enviaraprobarinventsal_inventsal');
Route::post('inventsal/aprobinventsal/{id}', 'InvEntSalController@aprobinventsal')->name('aprobinventsal_inventsal');
Route::get('inventsal/exportPdf', 'InvEntSalController@exportPdf')->name('exportPdf_inventsal');


/*RUTAS InvBodegaProductoController*/
Route::post('invbodegaproducto/consexistencia', 'InvBodegaProductoController@consexistencia')->name('consexistencia_invbodegaproducto');

/*RUTAS INVCONTROL*/
Route::get('invcontrol', 'InvControlController@index')->name('invcontrol');
Route::get('invcontrolpage', 'InvControlController@invcontrolpage')->name('invcontrolpage');
Route::post('invcontrol/procesarcierreini', 'InvControlController@procesarcierreini')->name('procesarcierreini_invcontrol');

/*RUTAS REPORTE MOVIMIENTO DE INVENTARIO*/
Route::get('reportinvmov', 'ReportInvMovController@index')->name('reportinvmov');
Route::get('reportinvmov/reporte', 'ReportInvMovController@reporte')->name('reportinvmov_reporte');
Route::get('reportinvmov/exportPdf', 'ReportInvMovController@exportPdf')->name('reportinvmov_exportPdf');
Route::get('reportinvmov/totalizarRep', 'ReportInvMovController@totalizarRep')->name('reportinvmov_totalizarRep');

/*RUTAS INV STOCK*/
Route::get('reportinvstock', 'ReportInvStockController@index')->name('reportinvstock');
Route::get('reportinvstockpage', 'ReportInvStockController@reportinvstockpage')->name('reportinvstockpage');
Route::get('reportinvstock/reporte', 'ReportInvStockController@reporte')->name('reportinvstock_reporte');
Route::get('reportinvstock/exportPdf', 'ReportInvStockController@exportPdf')->name('reportinvstock_exportPdf');
Route::get('reportinvstock/totalizarindex', 'ReportInvStockController@totalizarindex')->name('reportinvstock_totalizarindex');


/*RUTAS Movimiento Inventario INVMOV*/
Route::get('invmov', 'InvMovController@index')->name('invmov');
Route::get('invmovpage', 'InvMovController@invmovpage')->name('invmovpage');
Route::get('invmov/exportPdf', 'InvMovController@exportPdf')->name('exportPdf_invmov');


/*RUTAS Entradas y Salidas de Inventario Aprobar y pasar al inventario InvMov*/
Route::get('inventsalaprobar', 'InvEntSalAprobarController@index')->name('inventsalaprobar');
Route::get('inventsalaprobarpage', 'InvEntSalAprobarController@inventsalaprobarpage')->name('inventsalaprobarpage');
Route::post('inventsalaprobar', 'InvEntSalAprobarController@guardar')->name('guardar_inventsalaprobar');
Route::post('inventsalaprobar/aprobar/{id}', 'InvEntSalAprobarController@aprobar')->name('inventsalaprobar_aprobar');

/*RUTAS Generales*/
Route::post('generales_valpremiso', 'GeneralesController@generales_valpremiso')->name('generales_valpremiso');

/*RUTAS INV STOCK VENDEDORES*/
Route::get('reportinvstockvend', 'ReportInvStockVendController@index')->name('reportinvstockvend');
Route::get('reportinvstockvendpage', 'ReportInvStockVendController@reportinvstockvendpage')->name('reportinvstockvendpage');
Route::get('reportinvstockvend/reporte', 'ReportInvStockVendController@reporte')->name('reportinvstockvend_reporte');
Route::get('reportinvstockvend/exportPdf', 'ReportInvStockVendController@exportPdf')->name('reportinvstockvend_exportPdf');


/*RUTAS INVMOVMODULO*/
Route::get('invmovmodulo', 'InvMovModuloController@index')->name('invmovmodulo');
Route::get('invmovmodulopage', 'InvMovModuloController@invmovmodulopage')->name('invmovmodulopage');
Route::get('invmovmodulo/crear', 'InvMovModuloController@crear')->name('crear_invmovmodulo');
Route::post('invmovmodulo', 'InvMovModuloController@guardar')->name('guardar_invmovmodulo');
Route::get('invmovmodulo/{id}/editar', 'InvMovModuloController@editar')->name('editar_invmovmodulo');
Route::put('invmovmodulo/{id}', 'InvMovModuloController@actualizar')->name('actualizar_invmovmodulo');
Route::delete('invmovmodulo/{id}', 'InvMovModuloController@eliminar')->name('eliminar_invmovmodulo');
/*RUTAS APROBAR ACUERDO TECNICO COTIZACION */
Route::get('cotizacionaprobaracutec', 'CotizacionAprobarAcuTecController@index')->name('cotizacionaprobaracutec');
Route::get('cotizacionaprobaracutecpage', 'CotizacionAprobarAcuTecController@cotizacionaprobaracutecpage')->name('cotizacionaprobaracutecpage');
Route::get('cotizacionaprobaracutec/{id}/editar', 'CotizacionAprobarAcuTecController@editar')->name('editar_cotizacionaprobaracutec');
Route::get('cotizacionaprobaracutec/{id}/productobuscarpage', 'CotizacionAprobarAcuTecController@productobuscarpageid')->name('productobuscarpageid');
Route::get('cotizacionaprobaracutec/{id}/clientebuscarpage', 'CotizacionAprobarAcuTecController@clientebuscarpageid')->name('clientebuscarpageid');

/*RUTAS GUIA DESPACHO*/
Route::get('guiadesp', 'GuiaDespController@index')->name('guiadesp');
Route::get('guiadesppage', 'GuiaDespController@guiadesppage')->name('guiadesppage');
Route::get('guiadesp/totalizarindex', 'GuiaDespController@totalizarindex')->name('guiadesp_totalizarindex');
Route::get('guiadesp/{id}/crear', 'GuiaDespController@crear')->name('crear_guiadesp');
Route::post('guiadesp', 'GuiaDespController@guardar')->name('guardar_guiadesp');
Route::get('guiadesp/{id}/editar', 'GuiaDespController@editar')->name('editar_guiadesp');
Route::put('guiadesp/{id}', 'GuiaDespController@actualizar')->name('actualizar_guiadesp');
Route::delete('guiadesp/{id}', 'GuiaDespController@eliminar')->name('eliminar_guiadesp');
Route::get('guiadesp/listarorddesp', 'GuiaDespController@listarorddesp')->name('guiadesp_listarorddesp');
Route::get('guiadesp/listarorddesppage', 'GuiaDespController@listarorddesppage')->name('guiadesp_listarorddesppage');
Route::get('guiadesp/totalizarindex', 'GuiaDespController@totalizarindex')->name('guiadesp_listarorddesp_totalizarindex');
Route::post('guiadesp/guardarguiadesp', 'GuiaDespController@guardarguiadesp')->name('guiadesp_guardarguiadesp');
Route::get('guiadesp/productobuscarpage', 'GuiaDespController@productobuscarpage')->name('guiadesp_productobuscarpage');
Route::get('guiadesp/clientebuscarpage', 'GuiaDespController@clientebuscarpage')->name('guiadesp_clientebuscarpage');
Route::get('guiadesp/{id}/productobuscarpage', 'GuiaDespController@productobuscarpageid')->name('guiadesp_productobuscarpageid');
Route::get('guiadesp/{id}/clientebuscarpage', 'GuiaDespController@clientebuscarpageid')->name('guiadesp_clientebuscarpageid');
Route::post('guiadesp/dteguiadesp', 'GuiaDespController@dteguiadesp')->name('guiadesp_dteguiadesp');
Route::get('guiadesp/{id}/{stareport}/exportPdf', 'GuiaDespController@exportPdf')->name('exportPdf_guiadesp');
Route::post('guiadesp/validarupdated', 'GuiaDespController@validarupdated')->name('validarupdated');
Route::post('guiadesp/consultarguiadesp', 'GuiaDespController@consultarGuiaDesp')->name('consultarGuiaDesp_guiadesp');

/*RUTAS LISTAR ORDEN DESPACHO*/
Route::get('listarorddesp', 'ListarorddespController@index')->name('listarorddesp');
Route::get('listarorddesppage', 'ListarorddespController@listarorddesppage')->name('listarorddesppage');
Route::get('listarorddesp/totalizarindex', 'ListarorddespController@totalizarindex')->name('listarorddesp_totalizarindex');
Route::post('listarorddesp/guardarguiadesp', 'ListarorddespController@guardarguiadesp')->name('guardarguiadesp_listarorddesp');


/*RUTAS INV STOCK BODEGA DE PRODUCTO TERMINADO MAS PIKING*/
Route::get('reportinvstockbp', 'ReportInvStockBPController@index')->name('reportinvstockbp');
Route::get('reportinvstockbppage', 'ReportInvStockBPController@reportinvstockbppage')->name('reportinvstockbppage');
Route::get('reportinvstockbp/reporte', 'ReportInvStockBPController@reporte')->name('reportinvstockbp_reporte');
Route::get('reportinvstockbp/exportPdf', 'ReportInvStockBPController@exportPdf')->name('reportinvstockbp_exportPdf');
Route::get('reportinvstockbp/totalizarindex', 'ReportInvStockBPController@totalizarindex')->name('reportinvstockbp_totalizarindex');

/*RUTAS ASIGNAR PRODUCTO A CLIENTE*/
Route::get('clienteproducto', 'ClienteProductoController@index')->name('clienteproducto');
Route::get('clienteproductopage', 'ClienteProductoController@clienteproductopage')->name('clienteproductopage');
Route::get('clienteproducto/{id}/editar', 'ClienteProductoController@editar')->name('editar_clienteproducto');
Route::put('clienteproducto/{id}', 'ClienteProductoController@actualizar')->name('actualizar_clienteproducto');
Route::get('clienteproducto/productobuscarpage', 'ClienteProductoController@productobuscarpage')->name('productobuscarpage');
Route::get('clienteproducto/{id}/productobuscarpage', 'ClienteProductoController@productobuscarpageid')->name('productobuscarpageid');


/*RUTAS FORMA DE PAGO*/
Route::get('centroeconomico', 'CentroEconomicoController@index')->name('centroeconomico');
Route::get('centroeconomicopage', 'CentroEconomicoController@centroeconomicopage')->name('centroeconomicopage');
Route::get('centroeconomico/crear', 'CentroEconomicoController@crear')->name('crear_centroeconomico');
Route::post('centroeconomico', 'CentroEconomicoController@guardar')->name('guardar_centroeconomico');
Route::get('centroeconomico/{id}/editar', 'CentroEconomicoController@editar')->name('editar_centroeconomico');
Route::put('centroeconomico/{id}', 'CentroEconomicoController@actualizar')->name('actualizar_centroeconomico');
Route::delete('centroeconomico/{id}', 'CentroEconomicoController@eliminar')->name('eliminar_centroeconomico');

/*RUTAS FORMA DE PAGO*/
Route::get('foliocontrol', 'FoliocontrolController@index')->name('foliocontrol');
Route::get('foliocontrolpage', 'FoliocontrolController@foliocontrolpage')->name('foliocontrolpage');
Route::get('foliocontrol/crear', 'FoliocontrolController@crear')->name('crear_foliocontrol');
Route::post('foliocontrol', 'FoliocontrolController@guardar')->name('guardar_foliocontrol');
Route::get('foliocontrol/{id}/editar', 'FoliocontrolController@editar')->name('editar_foliocontrol');
Route::put('foliocontrol/{id}', 'FoliocontrolController@actualizar')->name('actualizar_foliocontrol');
Route::delete('foliocontrol/{id}', 'FoliocontrolController@eliminar')->name('eliminar_foliocontrol');

/*RUTAS REPORTE GUIA DESPACHO*/
Route::get('reportguiadesp', 'ReportGuiaDespController@index')->name('reportguiadesp');
Route::get('reportguiadesp/reportguiadesppage', 'ReportGuiaDespController@reportguiadesppage')->name('reportguiadesppage');
Route::get('reportguiadesp/reporte', 'ReportGuiaDespController@reporte')->name('reportguiadesp_reporte');
Route::get('reportguiadesp/exportPdf', 'ReportGuiaDespController@exportPdf')->name('reportguiadesp_exportPdf');
Route::get('reportguiadesp/totalizarindex', 'ReportGuiaDespController@totalizarindex')->name('reportguiadesp_totalizarindex');

/*RUTAS PRUEBA SOAP*/
Route::get('soap', 'SoapController@index')->name('soap');
Route::get('soap/Reimprimir_DoctoDTE', 'SoapController@Reimprimir_DoctoDTE')->name('Reimprimir_DoctoDTE_soap');

Route::get('guiadespanul', 'GuiaDespAnulController@index')->name('guiadespanul');
Route::get('guiadespanulpage', 'GuiaDespAnulController@guiadespanulpage')->name('guiadespanulpage');
Route::get('guiadespanul/totalizarindex', 'GuiaDespAnulController@totalizarindex')->name('guiadespanul_totalizarindex');
Route::get('guiadespanul/{id}/crear', 'GuiaDespAnulController@crear')->name('crear_guiadespanul');
Route::post('guiadespanul', 'GuiaDespAnulController@guardar')->name('guardar_guiadespanul');
Route::get('guiadespanul/{id}/editar', 'GuiaDespAnulController@editar')->name('editar_guiadespanul');
Route::put('guiadespanul/{id}', 'GuiaDespAnulController@actualizar')->name('actualizar_guiadespanul');
Route::delete('guiadespanul/{id}', 'GuiaDespAnulController@eliminar')->name('eliminar_guiadespanul');
Route::post('guiadespanul/store', 'GuiaDespAnulController@store')->name('store_guiadespanul');


/*RUTAS FACTURA*/
Route::get('factura', 'FacturaController@index')->name('factura');
Route::get('facturapage', 'FacturaController@facturapage')->name('facturapage');
Route::get('factura/totalizarindex', 'FacturaController@totalizarindex')->name('factura_totalizarindex');
Route::get('factura/crear', 'FacturaController@crear')->name('crear_factura');
Route::post('factura', 'FacturaController@guardar')->name('guardar_factura');
Route::get('factura/{id}/editar', 'FacturaController@editar')->name('editar_factura');
Route::put('factura/{id}', 'FacturaController@actualizar')->name('actualizar_factura');
Route::delete('factura/{id}', 'FacturaController@eliminar')->name('eliminar_factura');
Route::get('factura/listarguiadesp', 'FacturaController@listarguiadesp')->name('factura_listarguiadesp');
Route::get('factura/listarguiadesppage', 'FacturaController@listarguiadesppage')->name('factura_listarguiadesppage');
Route::post('factura/guardarfactura', 'FacturaController@guardarfactura')->name('factura_guardarfactura');
Route::get('factura/productobuscarpage', 'FacturaController@productobuscarpage')->name('factura_productobuscarpage');
Route::get('factura/clientebuscarpage', 'FacturaController@clientebuscarpage')->name('factura_clientebuscarpage');
Route::get('factura/{id}/productobuscarpage', 'FacturaController@productobuscarpageid')->name('factura_productobuscarpageid');
Route::get('factura/{id}/clientebuscarpage', 'FacturaController@clientebuscarpageid')->name('factura_clientebuscarpageid');
Route::post('factura/dtefactura', 'FacturaController@dtefactura')->name('factura_dtefactura');
Route::get('factura/{id}/{stareport}/exportPdf', 'FacturaController@exportPdf')->name('exportPdf_factura');
Route::post('factura/validarupdated', 'FacturaController@validarupdated')->name('validarupdated');


/*RUTAS DTE GUIA DESPACHO*/
Route::get('dteguiadesp', 'DteGuiaDespController@index')->name('dteguiadesp');
Route::get('dteguiadesppage', 'DteGuiaDespController@dteguiadesppage')->name('dteguiadesppage');
Route::get('dteguiadesp/totalizarindex', 'DteGuiaDespController@totalizarindex')->name('dteguiadesp_totalizarindex');
//Route::get('dteguiadesp/{id}/crear', 'DteGuiaDespController@crear')->name('crear_dteguiadesp');
Route::get('dteguiadesp/{id}/{updated_at}/crear', 'DteGuiaDespController@crear')->name('crear_dteguiadesp');
Route::post('dteguiadesp', 'DteGuiaDespController@guardar')->name('guardar_dteguiadesp');
Route::get('dteguiadesp/{id}/editar', 'DteGuiaDespController@editar')->name('editar_dteguiadesp');
Route::put('dteguiadesp/{id}', 'DteGuiaDespController@actualizar')->name('actualizar_dteguiadesp');
Route::delete('dteguiadesp/{id}', 'DteGuiaDespController@eliminar')->name('eliminar_dteguiadesp');
Route::get('dteguiadesp/listarorddesp', 'DteGuiaDespController@listarorddesp')->name('dteguiadesp_listarorddesp');
Route::get('dteguiadesp/listarorddesppage', 'DteGuiaDespController@listarorddesppage')->name('dteguiadesp_listarorddesppage');
//Route::get('dteguiadesp/totalizarindex', 'DteGuiaDespController@totalizarindex')->name('dteguiadesp_listarorddesp_totalizarindex');
Route::post('dteguiadesp/guardardteguiadesp', 'DteGuiaDespController@guardardteguiadesp')->name('dteguiadesp_guardardteguiadesp');
Route::get('dteguiadesp/productobuscarpage', 'DteGuiaDespController@productobuscarpage')->name('dteguiadesp_productobuscarpage');
Route::get('dteguiadesp/clientebuscarpage', 'DteGuiaDespController@clientebuscarpage')->name('dteguiadesp_clientebuscarpage');
Route::get('dteguiadesp/{id}/productobuscarpage', 'DteGuiaDespController@productobuscarpageid')->name('dteguiadesp_productobuscarpageid');
Route::get('dteguiadesp/{id}/clientebuscarpage', 'DteGuiaDespController@clientebuscarpageid')->name('dteguiadesp_clientebuscarpageid');
Route::post('dteguiadesp/dtedteguiadesp', 'DteGuiaDespController@dtedteguiadesp')->name('dteguiadesp_dtedteguiadesp');
Route::get('dteguiadesp/{id}/{stareport}/exportPdf', 'DteGuiaDespController@exportPdf')->name('exportPdf_dteguiadesp');
Route::post('dteguiadesp/validarupdated', 'DteGuiaDespController@validarupdated')->name('validarupdated');
Route::post('dteguiadesp/consultardteguiadesp', 'DteGuiaDespController@consultarDteGuiaDesp')->name('consultarDteGuiaDesp_dteguiadesp');
Route::post('dteguiadesp/guiadespanul', 'DteGuiaDespController@guiadespanul')->name('guiadespanul_dteguiadesp');
Route::post('dteguiadesp/volverGenDTE', 'DteGuiaDespController@volverGenDTE')->name('volverGenDTE_dteguiadesp');
Route::post('dteguiadesp/procesar', 'DteGuiaDespController@procesar')->name('procesar_dteguiadesp');

/*RUTAS REPORTE DTE GUIA DESPACHO*/
Route::get('reportdteguiadesp', 'ReportDTEGuiaDespController@index')->name('reportdteguiadesp');
Route::get('reportdteguiadesp/reportdteguiadesppage', 'ReportDTEGuiaDespController@reportdteguiadesppage')->name('reportdteguiadesppage');
Route::get('reportdteguiadesp/reporte', 'ReportDTEGuiaDespController@reporte')->name('reportdteguiadesp_reporte');
Route::get('reportdteguiadesp/exportPdf', 'ReportDTEGuiaDespController@exportPdf')->name('reportdteguiadesp_exportPdf');
Route::get('reportdteguiadesp/totalizarindex', 'ReportDTEGuiaDespController@totalizarindex')->name('reportdteguiadesp_totalizarindex');


/*RUTAS FACTURA*/
Route::get('dtefactura', 'DteFacturaController@index')->name('dtefactura');
Route::get('dtefacturapage', 'DteFacturaController@dtefacturapage')->name('dtefacturapage');
Route::get('dtefactura/totalizarindex', 'DteFacturaController@totalizarindex')->name('dtefactura_totalizarindex');
Route::get('dtefactura/crear', 'DteFacturaController@crear')->name('crear_dtefactura');
Route::post('dtefactura', 'DteFacturaController@guardar')->name('guardar_dtefactura');
Route::get('dtefactura/{id}/editar', 'DteFacturaController@editar')->name('editar_dtefactura');
Route::put('dtefactura/{id}', 'DteFacturaController@actualizar')->name('actualizar_dtefactura');
Route::delete('dtefactura/{id}', 'DteFacturaController@eliminar')->name('eliminar_dtefactura');
Route::get('dtefactura/listarguiadesp', 'DteFacturaController@listarguiadesp')->name('dtefactura_listarguiadesp');
Route::get('dtefactura/listarguiadesppage', 'DteFacturaController@listarguiadesppage')->name('dtefactura_listarguiadesppage');
Route::post('dtefactura/procesar', 'DteFacturaController@procesar')->name('dtefactura_procesar');
Route::get('dtefactura/productobuscarpage', 'DteFacturaController@productobuscarpage')->name('dtefactura_productobuscarpage');
Route::get('dtefactura/clientebuscarpage', 'DteFacturaController@clientebuscarpage')->name('dtefactura_clientebuscarpage');
Route::get('dtefactura/{id}/productobuscarpage', 'DteFacturaController@productobuscarpageid')->name('dtefactura_productobuscarpageid');
Route::get('dtefactura/{id}/clientebuscarpage', 'DteFacturaController@clientebuscarpageid')->name('dtefactura_clientebuscarpageid');
Route::post('dtefactura/dtedtefactura', 'DteFacturaController@dtedtefactura')->name('dtefactura_dtedtefactura');
Route::get('dtefactura/{id}/{stareport}/exportPdf', 'DteFacturaController@exportPdf')->name('exportPdf_dtefactura');
Route::post('dtefactura/validarupdated', 'DteFacturaController@validarupdated')->name('validarupdated');
Route::post('dtefactura/listardtedet', 'DteFacturaController@listardtedet')->name('dtefactura_listardtedet');
Route::post('dtefactura/anular', 'DteFacturaController@anular')->name('dtefactura_anular');
Route::post('dtefactura/buscarfactura', 'DteFacturaController@buscarfactura')->name('dtefactura_buscarfactura');
Route::post('dtefactura/estadoDTE', 'DteFacturaController@estadoDTE')->name('dtefactura_estadoDTE');
Route::post('dtefactura/staverfacdesp', 'DteFacturaController@staverfacdesp')->name('dtefactura_staverfacdesp');
Route::post('dtefactura/devolverguiadesp', 'DteFacturaController@devolverguiadesp')->name('dtefactura_devolverguiadesp');

/*RUTAS REPORTE DTE FACTURA*/
Route::get('reportdtefac', 'ReportDTEFacController@index')->name('reportdtefac');
Route::get('reportdtefac/reportdtefacpage', 'ReportDTEFacController@reportdtefacpage')->name('reportdtefacpage');
Route::get('reportdtefac/reporte', 'ReportDTEFacController@reporte')->name('reportdtefac_reporte');
Route::get('reportdtefac/exportPdf', 'ReportDTEFacController@exportPdf')->name('reportdtefac_exportPdf');
Route::get('reportdtefac/totalizarindex', 'ReportDTEFacController@totalizarindex')->name('reportdtefac_totalizarindex');


/*RUTAS ACUERDO TECNICO*/
Route::post('acuerdotecnico/buscaratxcampos', 'AcuerdoTecnicoController@buscaratxcampos')->name('acuerdotecnico_buscaratxcampos');

/*RUTAS ACUERDO TECNICO TEMPORAL*/
Route::get('acuerdotecnicotemp/exportPdf', 'AcuerdoTecnicoTempController@exportPdf')->name('acuerdotecnicotemp_exportPdf');


/*RUTAS DTE GUIA DESPACHO ANULAR*/
Route::get('dteguiadespanular', 'DteGuiaDespAnularController@index')->name('dteguiadespanular');
Route::get('dteguiadespanularpage', 'DteGuiaDespAnularController@dteguiadespanularpage')->name('dteguiadespanularpage');
Route::get('dteguiadespanular/totalizarindex', 'DteGuiaDespAnularController@totalizarindex')->name('dteguiadespanular_totalizarindex');
//Route::get('dteguiadespanular/{id}/crear', 'DteGuiaDespAnularController@crear')->name('crear_dteguiadespanular');
Route::get('dteguiadespanular/listarguiadesp', 'DteGuiaDespAnularController@listarguiadesp')->name('dteguiadespanular_listarguiadesp');
Route::get('dteguiadespanular/listarguiadesppage', 'DteGuiaDespAnularController@listarguiadesppage')->name('dteguiadespanular_listarguiadesppage');
Route::post('dteguiadespanular/dteguiadespanular', 'DteGuiaDespAnularController@dteguiadespanular')->name('dteguiadespanular_dteguiadespanular');

/*RUTAS NOTA CREDITO FACTURA*/
Route::get('dtencfactura', 'DteNCFacturaController@index')->name('dtencfactura');
Route::get('dtencfacturapage', 'DteNCFacturaController@dtencfacturapage')->name('dtencfacturapage');
Route::get('dtencfactura/totalizarindex', 'DteNCFacturaController@totalizarindex')->name('dtencfactura_totalizarindex');
Route::get('dtencfactura/crear', 'DteNCFacturaController@crear')->name('crear_dtencfactura');
Route::post('dtencfactura', 'DteNCFacturaController@guardar')->name('guardar_dtencfactura');
Route::get('dtencfactura/{id}/editar', 'DteNCFacturaController@editar')->name('editar_dtencfactura');
Route::put('dtencfactura/{id}', 'DteNCFacturaController@actualizar')->name('actualizar_dtencfactura');
Route::delete('dtencfactura/{id}', 'DteNCFacturaController@eliminar')->name('eliminar_dtencfactura');
Route::get('dtencfactura/listarguiadesp', 'DteNCFacturaController@listarguiadesp')->name('dtencfactura_listarguiadesp');
Route::get('dtencfactura/listarguiadesppage', 'DteNCFacturaController@listarguiadesppage')->name('dtencfactura_listarguiadesppage');
Route::post('dtencfactura/procesar', 'DteNCFacturaController@procesar')->name('dtencfactura_procesar');
Route::post('dtencfactura/consdte_dtedet', 'DteNCFacturaController@consdte_dtedet')->name('dtencfactura_consdte_dtedet');
Route::post('dtencfactura/anular', 'DteNCFacturaController@anular')->name('dtencfactura_anular');

/*RUTAS NOTA DEBITO FACTURA*/
Route::get('dtendfactura', 'DteNDFacturaController@index')->name('dtendfactura');
Route::get('dtendfacturapage', 'DteNDFacturaController@dtendfacturapage')->name('dtendfacturapage');
Route::get('dtendfactura/totalizarindex', 'DteNDFacturaController@totalizarindex')->name('dtendfactura_totalizarindex');
Route::get('dtendfactura/crear', 'DteNDFacturaController@crear')->name('crear_dtendfactura');
Route::post('dtendfactura', 'DteNDFacturaController@guardar')->name('guardar_dtendfactura');
Route::get('dtendfactura/{id}/editar', 'DteNDFacturaController@editar')->name('editar_dtendfactura');
Route::put('dtendfactura/{id}', 'DteNDFacturaController@actualizar')->name('actualizar_dtendfactura');
Route::delete('dtendfactura/{id}', 'DteNDFacturaController@eliminar')->name('eliminar_dtendfactura');
Route::get('dtendfactura/listarguiadesp', 'DteNDFacturaController@listarguiadesp')->name('dtendfactura_listarguiadesp');
Route::get('dtendfactura/listarguiadesppage', 'DteNDFacturaController@listarguiadesppage')->name('dtendfactura_listarguiadesppage');
Route::post('dtendfactura/procesar', 'DteNDFacturaController@procesar')->name('dtendfactura_procesar');
Route::post('dtendfactura/consdte_dtedet', 'DteNDFacturaController@consdte_dtedet')->name('dtendfactura_consdte_dtedet');
Route::post('dtendfactura/anular', 'DteNDFacturaController@anular')->name('dtendfactura_anular');


/*RUTAS REPORTE DTE NOTA CREDITO*/
Route::get('reportdtenc', 'ReportDTENcController@index')->name('reportdtenc');
Route::get('reportdtenc/reportdtencpage', 'ReportDTENcController@reportdtencpage')->name('reportdtencpage');
Route::get('reportdtenc/reporte', 'ReportDTENcController@reporte')->name('reportdtenc_reporte');
Route::get('reportdtenc/exportPdf', 'ReportDTENcController@exportPdf')->name('reportdtenc_exportPdf');
Route::get('reportdtenc/totalizarindex', 'ReportDTENcController@totalizarindex')->name('reportdtenc_totalizarindex');


/*RUTAS FACTURA DIRECTA*/
Route::get('dtefacturadir', 'DteFacturaDirController@index')->name('dtefacturadir');
Route::get('dtefacturadirpage', 'DteFacturaDirController@dtefacturadirpage')->name('dtefacturadirpage');
Route::get('dtefacturadir/totalizarindex', 'DteFacturaDirController@totalizarindex')->name('dtefacturadir_totalizarindex');
Route::get('dtefacturadir/crear', 'DteFacturaDirController@crear')->name('crear_dtefacturadir');
Route::post('dtefacturadir', 'DteFacturaDirController@guardar')->name('guardar_dtefacturadir');
Route::get('dtefacturadir/{id}/editar', 'DteFacturaDirController@editar')->name('editar_dtefacturadir');
Route::put('dtefacturadir/{id}', 'DteFacturaDirController@actualizar')->name('actualizar_dtefacturadir');
Route::delete('dtefacturadir/{id}', 'DteFacturaDirController@eliminar')->name('eliminar_dtefacturadir');
Route::get('dtefacturadir/listarguiadesp', 'DteFacturaDirController@listarguiadesp')->name('dtefacturadir_listarguiadesp');
Route::get('dtefacturadir/listarguiadesppage', 'DteFacturaDirController@listarguiadesppage')->name('dtefacturadir_listarguiadesppage');
Route::post('dtefacturadir/procesar', 'DteFacturaDirController@procesar')->name('dtefacturadir_procesar');
//Route::get('dtefacturadir/productobuscarpage', 'DteFacturaDirController@productobuscarpage')->name('dtefacturadir_productobuscarpage');
Route::get('dtefacturadir/clientebuscarpage', 'DteFacturaDirController@clientebuscarpage')->name('dtefacturadir_clientebuscarpage');
//Route::get('dtefacturadir/{id}/productobuscarpage', 'DteFacturaDirController@productobuscarpageid')->name('dtefacturadir_productobuscarpageid');
Route::get('dtefacturadir/{id}/clientebuscarpage', 'DteFacturaDirController@clientebuscarpageid')->name('dtefacturadir_clientebuscarpageid');
Route::post('dtefacturadir/dtedtefacturadir', 'DteFacturaDirController@dtedtefacturadir')->name('dtefacturadir_dtedtefacturadir');
Route::get('dtefacturadir/{id}/{stareport}/exportPdf', 'DteFacturaDirController@exportPdf')->name('exportPdf_dtefacturadir');
Route::post('dtefacturadir/validarupdated', 'DteFacturaDirController@validarupdated')->name('validarupdated');
Route::post('dtefacturadir/listardtedet', 'DteFacturaDirController@listardtedet')->name('dtefacturadir_listardtedet');
Route::post('dtefacturadir/buscarfactura', 'DteFacturaDirController@buscarfactura')->name('dtefacturadir_buscarfactura');

/*RUTAS FACTURA EXENTA*/
Route::get('dtefacturaexenta', 'DteFacturaExentaController@index')->name('dtefacturaexenta');
Route::get('dtefacturaexentapage', 'DteFacturaExentaController@dtefacturaexentapage')->name('dtefacturaexentapage');
Route::get('dtefacturaexenta/totalizarindex', 'DteFacturaExentaController@totalizarindex')->name('dtefacturaexenta_totalizarindex');
Route::get('dtefacturaexenta/crear', 'DteFacturaExentaController@crear')->name('crear_dtefacturaexenta');
Route::post('dtefacturaexenta', 'DteFacturaExentaController@guardar')->name('guardar_dtefacturaexenta');
Route::get('dtefacturaexenta/{id}/editar', 'DteFacturaExentaController@editar')->name('editar_dtefacturaexenta');
Route::put('dtefacturaexenta/{id}', 'DteFacturaExentaController@actualizar')->name('actualizar_dtefacturaexenta');
Route::delete('dtefacturaexenta/{id}', 'DteFacturaExentaController@eliminar')->name('eliminar_dtefacturaexenta');
Route::get('dtefacturaexenta/listarguiadesp', 'DteFacturaExentaController@listarguiadesp')->name('dtefacturaexenta_listarguiadesp');
Route::get('dtefacturaexenta/listarguiadesppage', 'DteFacturaExentaController@listarguiadesppage')->name('dtefacturaexenta_listarguiadesppage');
Route::post('dtefacturaexenta/procesar', 'DteFacturaExentaController@procesar')->name('dtefacturaexenta_procesar');
//Route::get('dtefacturaexenta/productobuscarpage', 'DteFacturaExentaController@productobuscarpage')->name('dtefacturaexenta_productobuscarpage');
Route::get('dtefacturaexenta/clientebuscarpage', 'DteFacturaExentaController@clientebuscarpage')->name('dtefacturaexenta_clientebuscarpage');
//Route::get('dtefacturaexenta/{id}/productobuscarpage', 'DteFacturaExentaController@productobuscarpageid')->name('dtefacturaexenta_productobuscarpageid');
Route::get('dtefacturaexenta/{id}/clientebuscarpage', 'DteFacturaExentaController@clientebuscarpageid')->name('dtefacturaexenta_clientebuscarpageid');
Route::post('dtefacturaexenta/dtedtefacturaexenta', 'DteFacturaExentaController@dtedtefacturaexenta')->name('dtefacturaexenta_dtedtefacturaexenta');
Route::get('dtefacturaexenta/{id}/{stareport}/exportPdf', 'DteFacturaExentaController@exportPdf')->name('exportPdf_dtefacturaexenta');
Route::post('dtefacturaexenta/validarupdated', 'DteFacturaExentaController@validarupdated')->name('validarupdated');
Route::post('dtefacturaexenta/listardtedet', 'DteFacturaExentaController@listardtedet')->name('dtefacturaexenta_listardtedet');
Route::post('dtefacturaexenta/buscarfactura', 'DteFacturaExentaController@buscarfactura')->name('dtefacturaexenta_buscarfactura');

/*RUTAS GUIA DIRECTA*/
Route::get('dteguiadespdir', 'DteGuiaDespDirController@index')->name('dteguiadespdir');
Route::get('dteguiadespdirpage', 'DteGuiaDespDirController@dteguiadespdirpage')->name('dteguiadespdirpage');
Route::get('dteguiadespdir/totalizarindex', 'DteGuiaDespDirController@totalizarindex')->name('dteguiadespdir_totalizarindex');
Route::get('dteguiadespdir/crear', 'DteGuiaDespDirController@crear')->name('crear_dteguiadespdir');
Route::post('dteguiadespdir', 'DteGuiaDespDirController@guardar')->name('guardar_dteguiadespdir');
Route::get('dteguiadespdir/{id}/editar', 'DteGuiaDespDirController@editar')->name('editar_dteguiadespdir');
Route::put('dteguiadespdir/{id}', 'DteGuiaDespDirController@actualizar')->name('actualizar_dteguiadespdir');
Route::delete('dteguiadespdir/{id}', 'DteGuiaDespDirController@eliminar')->name('eliminar_dteguiadespdir');
Route::get('dteguiadespdir/listarguiadesp', 'DteGuiaDespDirController@listarguiadesp')->name('dteguiadespdir_listarguiadesp');
Route::get('dteguiadespdir/listarguiadesppage', 'DteGuiaDespDirController@listarguiadesppage')->name('dteguiadespdir_listarguiadesppage');
Route::post('dteguiadespdir/procesar', 'DteGuiaDespDirController@procesar')->name('dteguiadespdir_procesar');
//Route::get('dteguiadespdir/productobuscarpage', 'DteGuiaDespDirController@productobuscarpage')->name('dteguiadespdir_productobuscarpage');
Route::get('dteguiadespdir/clientebuscarpage', 'DteGuiaDespDirController@clientebuscarpage')->name('dteguiadespdir_clientebuscarpage');
//Route::get('dteguiadespdir/{id}/productobuscarpage', 'DteGuiaDespDirController@productobuscarpageid')->name('dteguiadespdir_productobuscarpageid');
Route::get('dteguiadespdir/{id}/clientebuscarpage', 'DteGuiaDespDirController@clientebuscarpageid')->name('dteguiadespdir_clientebuscarpageid');
Route::post('dteguiadespdir/dtedteguiadespdir', 'DteGuiaDespDirController@dtedteguiadespdir')->name('dteguiadespdir_dtedteguiadespdir');
Route::get('dteguiadespdir/{id}/{stareport}/exportPdf', 'DteGuiaDespDirController@exportPdf')->name('exportPdf_dteguiadespdir');
Route::post('dteguiadespdir/validarupdated', 'DteGuiaDespDirController@validarupdated')->name('validarupdated');
Route::post('dteguiadespdir/listardtedet', 'DteGuiaDespDirController@listardtedet')->name('dteguiadespdir_listardtedet');
Route::post('dteguiadespdir/buscarfactura', 'DteGuiaDespDirController@buscarfactura')->name('dteguiadespdir_buscarfactura');


/*RUTAS PERMITIR VER FACTURA EN DESPACHO*/
Route::get('reportverfacdesp', 'ReportVerFacDespController@index')->name('reportverfacdesp');
Route::get('reportverfacdesppage', 'ReportVerFacDespController@reportverfacdesppage')->name('reportverfacdesppage');

/*RUTAS REPORTE ESTADO CLIENTE*/
Route::get('reportdteestadocli', 'ReportDTEEstadoCliController@index')->name('reportdteestadocli');
Route::get('reportdteestadocli/reportdteestadoclipage', 'ReportDTEEstadoCliController@reportdteestadoclipage')->name('reportdteestadoclipage');
Route::get('reportdteestadocli/reporte', 'ReportDTEEstadoCliController@reporte')->name('reportdteestadocli_reporte');
Route::get('reportdteestadocli/exportPdf', 'ReportDTEEstadoCliController@exportPdf')->name('reportdteestadocli_exportPdf');
Route::get('reportdteestadocli/totalizarindex', 'ReportDTEEstadoCliController@totalizarindex')->name('reportdteestadocli_totalizarindex');

/*RUTAS REPORTE VENTAS POR VENDEDOR*/
Route::get('reportdteventasxvend', 'ReportDTEVentasxVendController@index')->name('reportdteventasxvend');
Route::get('reportdteventasxvend/reportdteventasxvendpage', 'ReportDTEVentasxVendController@reportdteventasxvendpage')->name('reportdteventasxvendpage');
Route::get('reportdteventasxvend/reporte', 'ReportDTEVentasxVendController@reporte')->name('reportdteventasxvend_reporte');
Route::get('reportdteventasxvend/exportPdf', 'ReportDTEVentasxVendController@exportPdf')->name('reportdteventasxvend_exportPdf');
Route::get('reportdteventasxvend/totalizarindex', 'ReportDTEVentasxVendController@totalizarindex')->name('reportdteventasxvend_totalizarindex');

/*RUTAS REPORTE COMISION POR VENDEDOR*/
Route::get('reportdtecomisionxvend', 'ReportDTEComisionxVendController@index')->name('reportdtecomisionxvend');
Route::get('reportdtecomisionxvend/reportdtecomisionxvendpage', 'ReportDTEComisionxVendController@reportdtecomisionxvendpage')->name('reportdtecomisionxvendpage');
Route::get('reportdtecomisionxvend/reporte', 'ReportDTEComisionxVendController@reporte')->name('reportdtecomisionxvend_reporte');
Route::get('reportdtecomisionxvend/exportPdf', 'ReportDTEComisionxVendController@exportPdf')->name('reportdtecomisionxvend_exportPdf');
Route::get('reportdtecomisionxvend/totalizarindex', 'ReportDTEComisionxVendController@totalizarindex')->name('reportdtecomisionxvend_totalizarindex');