<?php

use App\Http\Controllers\PostController;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $router->get('/', function(){
//     return "<h1>PHP Service App</h1><p>Running</p>";
// });

// $router->group(['middleware' => 'auth.basic.json'], function () use ($router) {
//     $router->get('/users', 'UsersController@index');
//     $router->get('/users/{userId}', 'UsersController@show');
//     $router->post('/post-user', 'UsersController@store');
//     $router->put('/put-user/{userId}/', 'UsersController@update');
//     $router->patch('/patch-user/{userId}/{resource}', 'UsersController@patch');
//     $router->delete('/delete-user/{userId}', 'UsersController@delete');
// });

//Post
Route::group(['middleware' => ['auth']], function ($router) {
    $router->get('/posts', 'PostsController@index');
    $router->post('/posts', 'PostsController@store');
    $router->get('/post/{id}', 'PostsController@show');
    $router->put('/post/{id}', 'PostsController@update');
    $router->delete('/post/{id}', 'PostsController@destroy');
});

//Barang
Route::group(['middleware' => ['auth']], function ($router) {
    $router->get('/barangs', 'BarangController@index');
    $router->post('/barangs', 'BarangController@store');
    $router->get('/barang/{id}', 'BarangController@show');
    $router->put('/barang/{id}', 'BarangController@update');
    $router->delete('/barang/{id}', 'BarangController@destroy');
});

//account
$router->get('/accounts', 'AccountController@index');
$router->post('/accounts', 'AccountController@store');
$router->get('/account/{id}', 'AccountController@show');
$router->put('/account/{id}', 'AccountController@update');
$router->delete('/account/{id}', 'AccountController@destroy');

// Users
$router->group(['prefix' => 'auth'], function () use ($router){
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

});





