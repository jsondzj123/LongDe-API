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


//后台端路由接口
$router->group(['prefix' => 'admin' , 'namespace' => 'Admin'], function () use ($router) {
     //后台登录（lys）
    $router->group(['prefix' => 'login'], function () use ($router) {
        $router->get('getUserAuth', 'LoginController@getUserAuth');     //获取用户权限方法
    });
    //系统用户管理模块（lys）
    $router->group(['prefix' => 'adminuser'], function () use ($router) {
        $router->post('getUserList', 'AdminUserController@getUserList'); //获取后台用户列表方法
        $router->post('upUserStatus', 'AdminUserController@upUserStatus');//更改账号状态方法 (删除/禁用)
        $router->post('getAccount', 'CommonController@getAccountInfoOne');//获取添加账号信息（school，roleAuth）方法
        $router->post('getAuthList', 'AdminUserController@getAuthList');  //获取角色列表方法 
        $router->post('doInsertAdminUser', 'AdminUserController@doInsertAdminUser');  //添加用户方法 
        $router->post('getAdminUserUpdate', 'AdminUserController@getAdminUserUpdate');  //获取用户信息（编辑） 
        $router->post('doAdminUserUpdate', 'AdminUserController@doAdminUserUpdate');//编辑角色信息
    });
    //系统角色管理模块 （lys）
    $router->group(['prefix' => 'role'], function () use ($router) {
        $router->post('getAuthList', 'RoleController@getAuthList'); //获取后台角色列表方法
        $router->post('upRoleStatus', 'RoleController@upRoleStatus');//修改状态码
        $router->post('getRoleAuth', 'CommonController@getRoleAuth');//获取role_auth列表
        $router->post('doRoleInsert', 'RoleController@doRoleInsert');//添加角色方法
        $router->post('getRoleAuthUpdate', 'RoleController@getRoleAuthUpdate');// 获取角色信息（编辑）
        $router->post('doRoleAuthUpdate', 'RoleController@doRoleAuthUpdate');//编辑角色信息
    });
    
    $router->group(['prefix' => 'user'], function () use ($router) { //用户学员相关模块方法
        $router->get('getUserList', 'UserController@getUserList'); //获取学员列表方法
    });
    
});

$router->group(['prefix' => 'admin' , 'namespace' => 'Admin'], function () use ($router) {
    //用户学员相关模块(dzj)
    $router->group(['prefix' => 'student'], function () use ($router) {
        $router->post('doInsertStudent', 'StudentController@doInsertStudent');        //添加学员的方法
        $router->post('doUpdateStudent', 'StudentController@doUpdateStudent');        //更改学员的方法
        $router->post('doForbidStudent', 'StudentController@doForbidStudent');        //启用/禁用学员的方法
        $router->post('doStudentEnrolment', 'StudentController@doStudentEnrolment');  //学员报名的方法
        $router->post('getStudentInfoById', 'StudentController@getStudentInfoById');  //获取学员信息
        $router->post('getStudentList', 'StudentController@getStudentList');          //获取学员列表
    });
    //讲师教务相关模块(dzj)

    $router->group(['prefix' => 'teacher'], function () use ($router) {
        $router->post('doInsertTeacher', 'TeacherController@doInsertTeacher');        //添加讲师教务的方法
        $router->post('doUpdateTeacher', 'TeacherController@doUpdateTeacher');        //更改讲师教务的方法
        $router->post('doDeleteTeacher', 'TeacherController@doDeleteTeacher');        //删除讲师教务的方法
        $router->post('doRecommendTeacher', 'TeacherController@doRecommendTeacher');  //推荐讲师的方法
        $router->post('getTeacherInfoById', 'TeacherController@getTeacherInfoById');  //获取老师信息
        $router->post('getTeacherList', 'TeacherController@getTeacherList');          //获取老师列表
        $router->post('getTeacherSearchList', 'CommonController@getTeacherSearchList'); //讲师或教务搜索列表
    });
    //运营模块
    $router->group(['prefix' => 'article'], function () use ($router) {
        $router->post('getArticleList', 'ArticleController@getArticleList');//获取文章列表
    });
});
