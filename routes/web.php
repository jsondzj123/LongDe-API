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
/*$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('login','Api\AuthenticateController@postLogin');
    $router->post('register','Api\AuthenticateController@register');

    $router->group(['middleware'=>['jwt.role:user', 'jwt.auth']], function () use ($router) {
        $router->get('user/{id}', 'Api\UserController@show');

    });
    

    $router->group(['prefix' => 'user' , 'middleware'=>'api'], function () use ($router) {

        $router->post('user/userinfo','Api\UserController@getUserinfo');
        $router->post('logReg', 'Api\UserController@loginAndRegister');
        $router->get('getUserInfoById', 'Api\UserController@getUserInfoById');
        
        $router->post('logout','Api\UserController@logout');
        $router->post('refreshToken','Api\UserController@refreshToken');
    });
});*/

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
    //后台管理系统接口(sxl)
    $router->post('register', 'Admin\AuthenticateController@register');
    $router->post('login', 'Admin\AuthenticateController@postLogin');


    $router->group(['prefix' => 'admin', 'middleware'=> 'jwt.auth'], function () use ($router) {
        //用户详情
        $router->get('{id}', 'AdminController@show');
        //获取学员列表
        //$router->get('getUserList', 'UserController@getUserList');
    });

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



    
    //讲师教务相关模块

    $router->group(['prefix' => 'teacher'], function () use ($router) {
        $router->post('doInsertTeacher', 'TeacherController@doInsertTeacher');        //添加讲师教务的方法
        $router->post('doUpdateTeacher', 'TeacherController@doUpdateTeacher');        //更改讲师教务的方法
        $router->post('doDeleteTeacher', 'TeacherController@doDeleteTeacher');        //删除讲师教务的方法
        $router->post('doRecommendTeacher', 'TeacherController@doRecommendTeacher');  //推荐讲师的方法
        $router->post('getTeacherInfoById', 'TeacherController@getTeacherInfoById');  //获取老师信息
        $router->post('getTeacherList', 'TeacherController@getTeacherList');          //获取老师列表
        $router->post('getTeacherSearchList', 'CommonController@getTeacherSearchList'); //讲师或教务搜索列表
    });
    //题库相关模块(dzj)
    $router->group(['prefix' => 'question'], function () use ($router) {
        /****************题库科目部分  start****************/
        $router->post('doInsertSubject', 'QuestionController@doInsertSubject');        //添加题库科目的方法
        $router->post('doUpdateSubject', 'QuestionController@doUpdateSubject');        //更改题库科目的方法
        $router->post('doDeleteSubject', 'QuestionController@doDeleteSubject');        //删除题库科目的方法
        $router->post('getSubjectList', 'QuestionController@getSubjectList');          //获取题库科目列表
        /****************题库科目部分  end****************/

        /****************章节考点部分  start****************/
        $router->post('doInsertChapters', 'QuestionController@doInsertChapters');        //添加题库科目的方法
        $router->post('doUpdateChapters', 'QuestionController@doUpdateChapters');        //更改题库科目的方法
        $router->post('doDeleteChapters', 'QuestionController@doDeleteChapters');        //删除题库科目的方法
        $router->post('getChaptersList', 'QuestionController@getChaptersList');          //获取章节考点列表
        /****************章节考点部分  end****************/
    });
    //运营模块   苏振文
    $router->group(['prefix' => 'article'], function () use ($router) {
        /*------------文章模块---------------------*/
        $router->post('getArticleList', 'ArticleController@getArticleList');//获取文章列表
        $router->post('addArticle', 'ArticleController@addArticle');//新增文章
        $router->post('editStatusToId', 'ArticleController@editStatusToId');//文章启用&禁用
        $router->post('editDelToId', 'ArticleController@editDelToId');//文章删除
        $router->post('findToId', 'ArticleController@findToId');//获取单条文章数据
        $router->post('exitForId', 'ArticleController@exitForId');//文章修改
        /*------------文章分类模块------------------*/
        $router->post('addType', 'ArticletypeController@addType');//文章分类添加
        $router->post('getTypeList', 'ArticletypeController@getTypeList');//获取文章分类列表
        $router->post('editStatusForId', 'ArticletypeController@editStatusForId');//文章分类禁用&启用
        $router->post('exitDelForId', 'ArticletypeController@exitDelForId');//文章分类删除
        $router->post('exitTypeForId', 'ArticletypeController@exitTypeForId');//文章分类修改
        $router->post('OnelistType', 'ArticletypeController@OnelistType');//单条查询
    });
});


