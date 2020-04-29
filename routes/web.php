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

$router->group(['prefix' => 'admin'], function () use ($router) {
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('login', 'Api\Admin\LoginController@login');
    });
});

$router->group(['prefix' => 'admin' , 'namespace' => 'Admin'], function () use ($router) {
     //后台登录（lys）
    $router->group(['prefix' => 'login'], function () use ($router) {
        //获取用户权限(lys)
        $router->get('getUserAuth', 'LoginController@getUserAuth');
    });
    $router->group(['prefix' => 'adminuser'], function () use ($router) {
        //获取后台用户列表(lys)
        $router->post('getUserList', 'AdminUserController@getUserList');
        $router->post('upUserStatus', 'AdminUserController@upUserStatus');
    });
    $router->group(['prefix' => 'role'], function () use ($router) {
        //获取后台用户列表(lys)
        $router->post('getAuthList', 'RoleController@getAuthList');
        $router->post('upRoleStatus', 'RoleController@upRoleStatus');
    });
    //用户学员相关模块
    $router->group(['prefix' => 'user'], function () use ($router) {
        //获取学员列表
        $router->get('getUserList', 'UserController@getUserList');
    });
    //讲师教务相关模块
    $router->group(['prefix' => 'teacher'], function () use ($router) {
        $router->post('doInsertTeacher', 'TeacherController@doInsertTeacher');        //添加讲师教务的方法
        $router->post('doUpdateTeacher', 'TeacherController@doUpdateTeacher');        //更改讲师教务的方法
        $router->post('doDeleteTeacher', 'TeacherController@doDeleteTeacher');        //删除讲师教务的方法
        $router->post('doRecommendTeacher', 'TeacherController@doRecommendTeacher');  //推荐讲师的方法
        $router->post('getTeacherInfoById', 'TeacherController@getTeacherInfoById');  //获取老师信息
        $router->post('getTeacherList', 'TeacherController@getTeacherList');          //获取老师列表
    });
});