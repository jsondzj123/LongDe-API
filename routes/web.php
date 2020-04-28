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
    $router->group(['prefix' => 'user' , 'middleware'=>'api'], function () use ($router) {
        $router->post('logReg', 'Api\UserController@loginAndRegister');
        $router->get('getUserInfoById', 'Api\UserController@getUserInfoById');
        $router->get('userLogin', 'Api\UserController@userLogin');
        $router->post('logout','Api\UserController@logout');
        $router->post('refreshToken','Api\UserController@refreshToken');
    });
});

//PC端路由接口
$router->group(['prefix' => 'web'], function () use ($router) {

});


$router->group(['prefix' => 'admin' , 'namespace' => 'Admin'], function () use ($router) {
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
    //运营模块
    $router->group(['prefix' => 'article'], function () use ($router) {
        $router->post('getArticleList', 'ArticleController@getArticleList');//获取文章列表
        $router->post('test', 'ArticleController@getArticleList');//ceshi123123777777
    });
});
