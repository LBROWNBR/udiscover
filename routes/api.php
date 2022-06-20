<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//http://apis.tpresto.local/api/auth/login

/*
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

});

*/

Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 });

header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

//===============================================================
//=====         http://www.udiscover.local/api/login       =======
//===============================================================

Route::get('login', 'App\Http\Controllers\AuthController@login');

//===============================================================
//=====    http://www.udiscover.local/api/auth/textowhatsapp  =====
//===============================================================

Route::group([
    'middleware' => ['jwt.verify'],
    'prefix' => 'auth'
], function() {

    //============================     PARA LOGIN DEL APP      ======================
    Route::get('Companias', 'App\Http\Controllers\CompaniasController@index');
    Route::get('Companias/{id}', 'App\Http\Controllers\CompaniasController@show');
    Route::post('Companias', 'App\Http\Controllers\CompaniasController@store');
    Route::put('Companias/{id}', 'App\Http\Controllers\CompaniasController@update');
    Route::delete('Companias/{id}', 'App\Http\Controllers\CompaniasController@delete');




    Route::get('creditoscliente', 'App\Http\Controllers\ClienteController@API_CreditosCliente');
    Route::get('vercuotascreditocliente', 'App\Http\Controllers\ClienteController@API_VerCuotasCreditoCliente');
    Route::post('registrarpagocuotascreditocliente', 'App\Http\Controllers\ClienteController@API_RegistrarPagoCuotasCreditoCliente');
    Route::post('adjuntarfotocliente', 'App\Http\Controllers\ClienteController@API_AdjuntarFotoCliente');
    Route::post('modificardatosfotocliente', 'App\Http\Controllers\ClienteController@API_ModificarDatosFotoCliente');

    Route::post('adjuntarvoucher_prueba', 'App\Http\Controllers\ClienteController@API_AdjuntarVoucher_Prueba');

    //============================     PARA CONTACTENOS APP      =====================
    Route::get('textowhatsapp', 'App\Http\Controllers\GeneralesController@API_TextoWhatsApp');
    Route::get('textoemail', 'App\Http\Controllers\GeneralesController@API_TextoEmail');

    //============================     PARA SIMULADOR DEL APP      =====================
    Route::get('tipoprestamos', 'App\Http\Controllers\GeneralesController@API_TipoPrestamos');
    Route::get('tipopagos', 'App\Http\Controllers\GeneralesController@API_TipoPagos');
    Route::get('nrocuotas', 'App\Http\Controllers\GeneralesController@API_NroCuotas');
    Route::get('calculosimulador', 'App\Http\Controllers\GeneralesController@API_CalculoSimulador');

    //============================     PARA REGISTRO DE CLIENTE DEL APP      ======================
    Route::get('tipodocumentos', 'App\Http\Controllers\GeneralesController@API_TipoDocumentos');
    Route::get('ubigeos', 'App\Http\Controllers\GeneralesController@API_Ubigeos');
    Route::get('departamentos', 'App\Http\Controllers\GeneralesController@API_Departamentos');
    Route::get('provincias/{depa}', 'App\Http\Controllers\GeneralesController@API_Provincias');
    Route::get('distritos/{depa}/{prov}', 'App\Http\Controllers\GeneralesController@API_Distritos');

    Route::post('registrarclientepotencial', 'App\Http\Controllers\ClienteController@API_RegistrarClientePotencial');
    Route::post('registrargarantiaclientepotencial', 'App\Http\Controllers\ClienteController@API_RegistrarGarantiaClientePotencial');
    Route::post('registrardocugarantiaclientepotencial', 'App\Http\Controllers\ClienteController@API_RegistrarDocuGarantiaClientePotencial');



    /*
    Route::get('articles', 'App\Http\Controllers\ArticleController@index');
    Route::get('articles/{article}', 'App\Http\Controllers\ArticleController@show');
    Route::post('articles', 'App\Http\Controllers\ArticleController@store');
    Route::put('articles/{article}', 'App\Http\Controllers\ArticleController@update');
    Route::delete('articles/{article}', 'App\Http\Controllers\ArticleController@delete');
    */

});
