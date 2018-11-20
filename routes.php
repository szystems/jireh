<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('home');
});



Route::post('/show', 'VistavehiculoController@contactoEnviarVehi');
Route::post('/show', 'VistainmuebleController@contactoEnviarInmu');
route::resource('vistavehiculos','VistavehiculoController');
route::resource('vistainmuebles','VistainmuebleController');
route::resource('usuarios','UsuarioController');
route::resource('imginmuebles','ImginmuebleController');
route::resource('imgvehiculos','ImgvehiculoController');
route::resource('vehiculos','VehiculoController');
route::resource('inmuebles','InmuebleController');




Route::auth();
Route::get('/serviciosautomotriz', 'HomeController@serviciosautomotriz');
Route::get('/serviciosinmuebles', 'HomeController@serviciosinmuebles');
Route::post('/contacto', 'HomeController@contactoEnviar');
Route::get('/contacto', 'HomeController@contacto');
Route::get('/home', 'HomeController@index');
Route::get('/{slug?}', 'HomeController@index');

