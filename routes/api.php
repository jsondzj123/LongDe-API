<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


//客户端路由接口
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('user/login','Api\UserController@login');
    $router->group(['prefix' => 'user' , 'middleware'=>'auth:api'], function () use ($router) {
        $router->get('logReg', 'Api\UserController@loginAndRegister');
        $router->get('getUserInfoById', 'Api\UserController@getUserInfoById');
        $router->get('userLogin', 'Api\UserController@userLogin');
        $router->post('logout','Api\UserController@logout');
        $router->post('refreshToken','Api\UserController@refreshToken');
    });
});



