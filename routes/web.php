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

    //系统用户管理模块（lys）
    $router->group(['prefix' => 'adminuser' ], function () use ($router) {
        $router->post('getAdminUserList', 'AdminUserController@getAdminUserList'); //获取后台用户列表方法
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

    $router->group(['prefix' => 'user','middleware'=> ['jwt.auth']], function () use ($router) { //用户学员相关模块方法
        $router->get('getUserList', 'UserController@getUserList'); //获取学员列表方法
    });
    $router->group(['prefix' => 'school',], function () use ($router) { //用户学员相关模块方法
        $router->post('getSchoolList', 'SchoolController@getSchoolList'); //获取学员列表方法
        $router->post('doUpdateSchoolStatus', 'SchoolController@doUpdateSchoolStatus'); //获取学员列表方法
        $router->post('doInsertSchool', 'SchoolController@doInsertSchool'); //添加分校信息并创建分校管理员
        $router->post('getSchoolUpdate', 'SchoolController@getSchoolUpdate'); //获取分校信息（编辑）
        $router->post('doSchoolUpdate', 'SchoolController@doSchoolUpdate'); //编辑分校信息
        $router->post('getSchoolById', 'SchoolController@getSchoolById'); //查看分校超级管理角色信息
        $router->post('getAdminById', 'SchoolController@getAdminById'); //获取分校超级管理用户信息（编辑）
        $router->post('doAdminUpdate', 'SchoolController@doAdminUpdate'); //编辑分校超级管理用户信息
        $router->post('getSchoolTeacherList', 'SchoolController@getSchoolTeacherList'); //获取分校讲师列表
    });

});


$router->group(['prefix' => 'admin' , 'namespace' => 'Admin'], function () use ($router) {
    //后台管理系统接口(sxl)
    $router->post('register', 'AuthenticateController@register');
    $router->post('login', 'AuthenticateController@postLogin');


    $router->group(['prefix' => 'admin', 'middleware'=> ['jwt.auth']], function () use ($router) {
        //用户详情
        $router->get('{id}', 'AdminController@show');
        $router->post('info', 'AdminController@info');
         // $router->post('info', function() use ($router){
         //    echo 11;
         // });
        //获取学员列表
        //$router->get('getUserList', 'UserController@getUserList');
    });

    /*
     * 课程模块
    */
    $router->get('lesson', 'LessonController@index');
    $router->get('lesson/{id}', 'LessonController@show');
    $router->post('lesson', 'LessonController@store');
    $router->post('lesson/{id}/update', 'LessonController@update');
    $router->get('lesson/{id}/delete', 'LessonController@destroy');

    /*
     * 科目模块
    */
    $router->get('subject', 'SubjectController@index');
    $router->get('subject/{id}', 'SubjectController@show');
    $router->post('subject', 'SubjectController@store');
    $router->post('subject/{id}/update', 'SubjectController@update');
    $router->get('subject/{id}/delete', 'SubjectController@destroy');

    /*
     * 录播模块
    */
    $router->get('video', 'VideoController@index');
    $router->get('video/{id}', 'VideoController@show');
    $router->post('video', 'VideoController@store');
    $router->post('video/{id}/update', 'VideoController@update');

    $router->get('video/{id}/edit', 'VideoController@edit');


    /*
     * 直播模块
    */
    $router->get('live', 'LiveController@index');
    $router->get('live/{id}', 'LiveController@show');
    $router->post('live', 'LiveController@store');
    $router->post('live/{id}/update', 'LiveController@update');
    $router->get('live/{id}/delete', 'LiveController@destroy');
    $router->get('live/{id}/edit', 'LiveController@edit');

    //用户学员相关模块(dzj)
    $router->group(['prefix' => 'student'], function () use ($router) {
        $router->post('doInsertStudent', 'StudentController@doInsertStudent');        //添加学员的方法
        $router->post('doUpdateStudent', 'StudentController@doUpdateStudent');        //更改学员的方法
        $router->post('doForbidStudent', 'StudentController@doForbidStudent');        //启用/禁用学员的方法
        $router->post('doStudentEnrolment', 'StudentController@doStudentEnrolment');  //学员报名的方法
        $router->post('getStudentInfoById', 'StudentController@getStudentInfoById');  //获取学员信息
        $router->post('getStudentList', 'CommonController@getStudentList');           //获取学员列表
        $router->post('getStudentCommonList', 'CommonController@getStudentCommonList');  //学员公共参数列表
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
        $router->post('doInsertChapters', 'QuestionController@doInsertChapters');        //添加章节考点的方法
        $router->post('doUpdateChapters', 'QuestionController@doUpdateChapters');        //更改章节考点的方法
        $router->post('doDeleteChapters', 'QuestionController@doDeleteChapters');        //删除章节考点的方法
        $router->post('getChaptersList', 'QuestionController@getChaptersList');          //获取章节考点列表
        /****************章节考点部分  end****************/

        /****************题库部分  start****************/
        $router->post('doInsertBank', 'BankController@doInsertBank');                    //添加题库的方法
        $router->post('doUpdateBank', 'BankController@doUpdateBank');                    //更新题库的方法
        $router->post('doDeleteBank', 'BankController@doDeleteBank');                    //删除题库的方法
        $router->post('doOpenCloseBank', 'BankController@doOpenCloseBank');              //题库开启/关闭的方法
        $router->post('getBankInfoById', 'BankController@getBankInfoById');              //获取题库详情信息
        $router->post('getBankList', 'CommonController@getBankList');                    //获取题库列表
        /****************题库部分  end****************/


        /****************试卷部分  start****************/
        $router->post('doInsertPapers', 'PapersController@doInsertPapers');              //添加试卷的方法
        $router->post('doUpdatePapers', 'PapersController@doUpdatePapers');              //更新试卷的方法
        $router->post('doDeletePapers', 'PapersController@doDeletePapers');              //删除试卷的方法
        $router->post('doPublishPapers', 'PapersController@doPublishPapers');            //发布/取消发布试卷的方法
        $router->post('getPapersInfoById', 'PapersController@getPapersInfoById');        //获取试卷详情信息
        $router->post('getPapersList', 'PapersController@getPapersList');                //获取题库列表
        /****************试卷部分  end****************/

        $router->get('export', 'CommonController@doExportExamLog'); //导入导出demo
        $router->post('getBankCommonList', 'CommonController@getBankCommonList');        //题库公共参数列表
    });
    //运营模块(szw)
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
    //订单&支付模块(szw)
    $router->group(['prefix' => 'order'], function () use ($router) {
        $router->post('orderList', 'OrderController@orderList');//订单列表
        $router->post('orderPay', 'OrderController@orderPay');//订单在线支付
        $router->post('findOrderForId', 'OrderController@findOrderForId');//订单详情
        $router->post('auditToId', 'OrderController@auditToId');//订单审核通过/不通过
        $router->post('orderUpOaForId', 'OrderController@orderUpOaForId');//订单修改oa状态
        $router->post('wxnotify_url', 'OrderController@wxnotify_url');//微信回调
        $router->post('alinotify_url', 'OrderController@alinotify_url');//ali回调
    });
    //数据模块（szw）
    $router->group(['prefix' => 'statistics'], function () use ($router) {
        $router->post('StudentList', 'StatisticsController@StudentList');//学员统计
        $router->post('TeacherList', 'StatisticsController@TeacherList');//教师统计
        $router->post('TeacherClasshour', 'StatisticsController@TeacherClasshour');//教师课时详情
        $router->post('LiveList', 'StatisticsrController@LiveList');//直播统计
        $router->post('LiveDetails', 'StatisticsrController@LiveDetails');//直播详情
    });
});


