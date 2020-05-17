<?php

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
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/categories',['uses'=>'CategoryController@index']);
    $router->post('categories',['uses' => 'CategoryController@store']);
    $router->get('saving_accounts',['uses' => 'SavingAccountController@index']);
    $router->post('saving_accounts',['uses' => 'SavingAccountController@store']);
    $router->get('movements',['uses' => 'MovementController@index']);
    $router->get('movements/categories',['uses' => 'MovementController@categories']);
    $router->get('movements/summary',['uses' => 'MovementController@summary']);

    $router->get('movements/{id}',['uses' => 'MovementController@show']);
    $router->post('movements',['uses' => 'MovementController@store']);

});
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('users',['uses' => 'UserController@store']);
$router->post('login',['uses'=>'UserController@login']);
