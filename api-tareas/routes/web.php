<?php

//use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Controllers\Auth\GoogleSocialiteController;

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

//RUTAS CONTROLADOR DE USUARIO

Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::post('/api/googlelogin', 'UserController@googlelogin');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload');
Route::put('/api/user/upload', 'UserController@upload'); //->middleware(ApiAuthMiddleware::class);

Route::get('{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');

Route::put('/api/user/password', 'UserController@password');

Route::get('auth/google', [GoogleSocialiteController::class, 'redirectToGoogle']);
Route::get('callback/google', [GoogleSocialiteController::class, 'handleCallback']);

//RUTAS CONTROLADOR DE MAIL
Route::post('/api/mail/contact', 'MailController@contact');
Route::post('/api/mail/passwordrecoveri', 'MailController@passwordrecoveri');

//RUTAS CONTROLADOR LIST
Route::post('/api/list/create', 'ListController@create');
Route::post('/api/list/list', 'ListController@reader');
Route::post('/api/list/update', 'ListController@update');
Route::post('/api/list/delete', 'ListController@delete');

//RUTAS CONTROLADOR TASK
Route::post('/api/task/create', 'TaskController@create');
Route::post('/api/task/list', 'TaskController@reader');
Route::post('/api/task/update', 'TaskController@update');
Route::post('/api/task/delete', 'TaskController@delete');
