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
Route::group(['middleware' => ['cors']], function () {
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

//RUTAS CONTROLADOR DEPARTAMENTO
Route::post('/api/department/create', 'DepartmentController@create');
Route::post('/api/department/list', 'DepartmentController@getDepartmentByUser');
Route::post('/api/department/update', 'DepartmentController@updateDepartment');
Route::post('/api/department/delete', 'DepartmentController@deleteDepartment');
Route::post('/api/department/detail/{id}', 'DepartmentController@detail');

//RUTAS CONTROLADOR CATEGORIA
Route::post('/api/category/create', 'CategoryController@create');
Route::post('/api/category/list', 'CategoryController@getCategoryByUser');
Route::post('/api/category/department/list', 'CategoryController@getCategoryByDepartment');
Route::post('/api/category/update', 'CategoryController@updateCategory');
Route::post('/api/category/delete', 'CategoryController@deleteCategory');
Route::post('/api/category/detail/{id}', 'CategoryController@detail');

//RUTAS CONTROLADOR ARTICULOS
Route::post('/api/article/create', 'ArticleController@create');
Route::post('/api/article/list', 'ArticleController@getArticleByUser');
Route::post('/api/article/department/list', 'ArticleController@getArticleByDepartment');
Route::post('/api/article/category/list', 'ArticleController@getArticleByCategory');
Route::post('/api/article/update', 'ArticleController@updateArticle');
Route::post('/api/article/delete', 'ArticleController@deleteArticle');
Route::post('/api/article/detail/{id}', 'ArticleController@detail');

//RUTAS CONTROLADOR ELEMENTOS
Route::post('/api/element/create', 'ElementController@create');
Route::post('/api/element/list', 'ElementController@getElementsByUser');
Route::post('/api/element/update', 'ElementController@updateElement');
Route::post('/api/element/delete', 'ElementController@deleteElement');
Route::post('/api/element/detail/{id}', 'ElementController@detail');

//RUTAS CONTROLADOR CARACTERISTICAS
Route::post('/api/feature/create', 'FeatureController@create');
Route::post('/api/feature/createfromelement', 'FeatureController@createfromelement');
Route::post('/api/feature/list', 'FeatureController@getFeaturesByUser');
Route::post('/api/feature/article/list', 'FeatureController@getFeaturesByArticle');
Route::post('/api/feature/update', 'FeatureController@updateFeature');
Route::post('/api/feature/delete', 'FeatureController@deleteFeature');

//RUTAS CONTROLADOR PRESUPUESTOS
Route::post('/api/budguet/create', 'BudguetController@create');
Route::post('/api/budguet/list', 'BudguetController@getBudguetByUser');
Route::post('/api/budguet/client/list', 'BudguetController@getBudguetByClient');
Route::post('/api/budguet/update', 'BudguetController@updateBudguet');
Route::post('/api/budguet/delete', 'BudguetController@deleteBudguet');
Route::post('/api/budguet/detail/{id}', 'BudguetController@detail');

//RUTAS CONTROLADOR CLIENTES
Route::post('/api/client/create', 'ClientController@create');
Route::post('/api/client/list', 'ClientController@getClientByUser');
Route::post('/api/client/update', 'ClientController@updateClient');
Route::post('/api/client/delete', 'ClientController@deleteClient');
Route::post('/api/client/detail/{id}', 'ClientController@detail');

//RUTAS CONTROLADOR LINEAS DE PRESUPUESTO
Route::post('/api/row/create', 'RowController@create');
Route::post('/api/row/list', 'RowController@getRowByUser');
Route::post('/api/row/budguet/list', 'RowController@getRowByBudguet');
Route::post('/api/row/budguetpdf/list', 'RowController@getRowByBudguetpdf');
Route::post('/api/row/update', 'RowController@updateRow');
Route::post('/api/row/delete', 'RowController@deleteRow');
});