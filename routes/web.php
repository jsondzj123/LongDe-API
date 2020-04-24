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


//客户端(ios,安卓)路由接口
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('user/login','Api\UserController@login');
    $router->post('user/userinfo','Api\UserController@getUserinfo');
    $router->group(['prefix' => 'user' , 'middleware'=>'auth:api'], function () use ($router) {
        $router->get('logReg', 'Api\UserController@loginAndRegister');
        $router->get('getUserInfoById', 'Api\UserController@getUserInfoById');
        $router->get('userLogin', 'Api\UserController@userLogin');
        $router->post('logout','Api\UserController@logout');
        $router->post('refreshToken','Api\UserController@refreshToken');
    });
});

//PC端路由接口
$router->group(['prefix' => 'web'], function () use ($router) {

});

//后台端路由接口
$router->group(['prefix' => 'admin' , 'namespace' => 'Admin'], function () use ($router) {
    //用户学员相关模块
    $router->group(['prefix' => 'user'], function () use ($router) {
        //获取学员列表
        $router->get('getUserList', 'UserController@getUserList');
    });
});