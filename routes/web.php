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

$router->post('/', function () use ($router) {
    return $router->app->version();
});

//客户端(ios,安卓)路由接口
$router->group(['prefix' => 'api', 'namespace' => 'Api'], function () use ($router) {
    /*
     * 科目模块(sxl)
    */
    $router->post('subject', 'SubjectController@index');

    /*
     * 课程模块(sxl)
    */
    $router->get('lesson', 'LessonController@index');

    $router->post('doUserRegister','AuthenticateController@doUserRegister');    //APP注册接口
    $router->post('doVisitorLogin','AuthenticateController@doVisitorLogin');    //APP游客登录接口
    $router->post('doUserLogin','AuthenticateController@doUserLogin');          //APP登录接口
    $router->post('doSendSms','AuthenticateController@doSendSms');              //APP发送短信接口
    $router->post('doUserForgetPassword','AuthenticateController@doUserForgetPassword');              //APP忘记密码接口
    //用户学员相关接口
    $router->group(['prefix' => 'user' , 'middleware'=> ['user']], function () use ($router) {
        $router->post('getUserInfoById','UserController@getUserInfoById');          //APP学员详情接口
    });
    //支付
    $router->group(['prefix' => 'order'], function () use ($router) {
        $router->post('createOrder','OrderController@createOrder');
    });
});

//PC端路由接口
$router->group(['prefix' => 'web'], function () use ($router) {

});

//后台端路由接口
/*****************start**********************/
//后端登录注册接口
$router->group(['prefix' => 'admin' , 'namespace' => 'Admin'], function () use ($router) {
    $router->post('register', 'AuthenticateController@register');
    $router->post('login', 'AuthenticateController@postLogin');
});
//后端登录权限认证相关接口
$router->group(['prefix' => 'admin' , 'namespace' => 'Admin' , 'middleware'=> ['jwt.auth']], function () use ($router) {
    /*$router->group(['prefix' => 'admin', 'middleware'=> ['jwt.auth']], function () use ($router) {
        //用户详情
        $router->post('{id}', 'AdminController@show');
        $router->post('info', 'AdminController@info');

        //获取学员列表
        $router->post('postUserList', 'UserController@postUserList');
    });*/

    /*
     * 课程模块(sxl)
    */

    $router->post('lesson', 'LessonController@index');
    $router->post('lesson/add', 'LessonController@store');
    $router->post('lesson/{id}', 'LessonController@show');
    $router->post('lesson/{id}/update', 'LessonController@update');
    $router->post('lesson/{id}/edit', 'LessonController@edit');
    $router->post('lesson/{id}/delete', 'LessonController@destroy');

    /*
     * 章节模块(sxl)
    */
    $router->post('lessonChild', 'LessonChildController@index');
    $router->post('lessonChild/add', 'LessonChildController@store');
    $router->post('lessonChild/{id}', 'LessonChildController@show');
    $router->post('lessonChild/{id}/update', 'LessonChildController@update');
    $router->post('lessonChild/{id}/delete', 'LessonChildController@destroy');

    /*
     * 分校课程(sxl)
    */
    $router->post('lessonSchool', 'LessonSchoolController@index');
    $router->post('lessonSchool/add', 'LessonSchoolController@store');
    $router->post('lessonSchool/{id}', 'LessonSchoolController@show');
    $router->post('lessonSchool/{id}/update', 'LessonSchoolController@update');
    $router->post('lessonSchool/{id}/delete', 'LessonSchoolController@destroy');


    /*
     * 库存(sxl)
    */
    $router->post('lessonStock', 'LessonStockController@index');
    $router->post('lessonStock/add', 'LessonStockController@store');


    /*
     * 科目模块(sxl)
    */
    $router->post('subject', 'SubjectController@index');
    $router->post('subject/add', 'SubjectController@store');
    $router->post('subject/{id}', 'SubjectController@show');
    $router->post('subject/{id}/update', 'SubjectController@update');
    $router->post('subject/{id}/delete', 'SubjectController@destroy');

    /*
     * 录播模块(sxl)
    */
    $router->post('video', 'VideoController@index');
    $router->post('video/add', 'VideoController@store');
    $router->post('video/{id}', 'VideoController@show');
    $router->post('video/{id}/update', 'VideoController@update');
    $router->post('video/{id}/edit', 'VideoController@edit');


    /*
     * 直播模块(sxl)
    */
    $router->post('live', 'LiveController@index');
    $router->post('live/add', 'LiveController@store');
    $router->post('live/{id}/classNumberList', 'LiveController@classList');
    $router->post('live/{id}', 'LiveController@show');
    $router->post('live/{id}/update', 'LiveController@update');
    $router->post('live/{id}/delete', 'LiveController@destroy');
    $router->post('live/{id}/edit', 'LiveController@edit');
    $router->post('live/{id}/lesson', 'LiveController@lesson');

    /*
     * 直播课次模块(sxl)
    */
    $router->post('liveChild', 'LiveChildController@index');
    $router->post('liveChild/add', 'LiveChildController@store');
    $router->post('liveChild/{id}/update', 'LiveChildController@update');
    $router->post('liveChild/{id}/delete', 'LiveChildController@destroy');


    //用户学员相关模块(dzj)
    $router->group(['prefix' => 'student'], function () use ($router) {
        $router->post('doInsertStudent', 'StudentController@doInsertStudent');        //添加学员的方法
        $router->post('doUpdateStudent', 'StudentController@doUpdateStudent');        //更改学员的方法
        $router->post('doForbidStudent', 'StudentController@doForbidStudent');        //启用/禁用学员的方法
        $router->post('doStudentEnrolment', 'StudentController@doStudentEnrolment');  //学员报名的方法
        $router->post('getStudentInfoById', 'StudentController@getStudentInfoById');   //获取学员信息
        $router->post('getStudentList', 'StudentController@getStudentList');           //获取学员列表
        $router->post('getStudentCommonList', 'StudentController@getStudentCommonList');  //学员公共参数列表
    });

    //讲师教务相关模块(dzj)
    $router->group(['prefix' => 'teacher'], function () use ($router) {
        $router->post('doInsertTeacher', 'TeacherController@doInsertTeacher');        //添加讲师教务的方法
        $router->post('doUpdateTeacher', 'TeacherController@doUpdateTeacher');        //更改讲师教务的方法
        $router->post('doDeleteTeacher', 'TeacherController@doDeleteTeacher');        //删除讲师教务的方法
        $router->post('doRecommendTeacher', 'TeacherController@doRecommendTeacher');  //推荐讲师的方法
        $router->post('getTeacherInfoById', 'TeacherController@getTeacherInfoById');  //获取老师信息
        $router->post('getTeacherList', 'TeacherController@getTeacherList');          //获取老师列表
        $router->post('getTeacherSearchList', 'TeacherController@getTeacherSearchList'); //讲师或教务搜索列表
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
        $router->post('doInsertChapters', 'QuestionController@doInsertChapters');           //添加章节考点的方法
        $router->post('doUpdateChapters', 'QuestionController@doUpdateChapters');           //更改章节考点的方法
        $router->post('doDeleteChapters', 'QuestionController@doDeleteChapters');           //删除章节考点的方法
        $router->post('getChaptersList', 'QuestionController@getChaptersList');             //获取章节考点列表
        $router->post('getChaptersSelectList', 'QuestionController@getChaptersSelectList'); //获取章节考点下拉选择列表
        /****************章节考点部分  end****************/

        /****************题库部分  start****************/
        $router->post('doInsertBank', 'BankController@doInsertBank');                    //添加题库的方法
        $router->post('doUpdateBank', 'BankController@doUpdateBank');                    //更新题库的方法
        $router->post('doDeleteBank', 'BankController@doDeleteBank');                    //删除题库的方法
        $router->post('doOpenCloseBank', 'BankController@doOpenCloseBank');              //题库开启/关闭的方法
        $router->post('getBankInfoById', 'BankController@getBankInfoById');              //获取题库详情信息
        $router->post('getBankList', 'BankController@getBankList');                      //获取题库列表
        $router->post('getBankCommonList', 'BankController@getBankCommonList');          //题库公共参数列表
        /****************题库部分  end****************/


        /****************试卷部分  start****************/
        $router->post('doInsertPapers', 'PapersController@doInsertPapers');              //添加试卷的方法
        $router->post('doUpdatePapers', 'PapersController@doUpdatePapers');              //更新试卷的方法
        $router->post('doDeletePapers', 'PapersController@doDeletePapers');              //删除试卷的方法
        $router->post('doPublishPapers', 'PapersController@doPublishPapers');            //发布/取消发布试卷的方法
        $router->post('getPapersInfoById', 'PapersController@getPapersInfoById');        //获取试卷详情信息
        $router->post('getPapersList', 'PapersController@getPapersList');                //获取题库列表
        $router->post('getRegionList', 'PapersController@getRegionList');                //获取所属区域列表
        /****************试卷部分  end****************/


        //试题选择试卷（zzk）
        /****************试卷选择试题部分  start****************/
        $router->post('InsertTestPaperSelection', 'ExamController@InsertTestPaperSelection');           //添加试题到试卷
        $router->post('doTestPaperSelection', 'ExamController@doTestPaperSelection');                   //试卷已添加试题的列表
        $router->post('ListTestPaperSelection', 'ExamController@ListTestPaperSelection');               //添加试题到试卷的列表
        $router->post('RepetitionTestPaperSelection', 'ExamController@RepetitionTestPaperSelection');   //检测试卷试题
        $router->post('oneTestPaperSelection', 'ExamController@oneTestPaperSelection');                 //获取试题详情
        $router->post('deleteTestPaperSelection', 'ExamController@deleteTestPaperSelection');           //删除试题
        /****************试卷选择试题部分  end****************/


        /****************试题部分  start****************/
        $router->post('doInsertExam', 'ExamController@doInsertExam');                    //添加试题的方法
        $router->post('doUpdateExam', 'ExamController@doUpdateExam');                    //修改试题的方法
        $router->post('doDeleteExam', 'ExamController@doDeleteExam');                    //删除试题的方法
        $router->post('doPublishExam', 'ExamController@doPublishExam');                  //发布试题的方法
        $router->post('getExamInfoById', 'ExamController@getExamInfoById');              //试题详情的方法
        $router->post('getExamList', 'ExamController@getExamList');                      //试题列表的方法
        $router->post('getMaterialList', 'ExamController@getMaterialList');              //查看材料题的方法
        $router->post('getExamCommonList', 'ExamController@getExamCommonList');          //试题公共参数列表
        $router->post('importExam', 'ExamController@doImportExam');                      //导入试题excel功能
        $router->post('doExamineExcelData', 'ExamController@doExamineExcelData');        //校验excel表格接口
        /****************试题部分  end****************/

        $router->get('export', 'CommonController@doExportExamLog'); //导入导出demo
    });
    //运营模块(szw)
    $router->group(['prefix' => 'article'], function () use ($router) {
        /*------------文章模块---------------------*/
        $router->post('getArticleList', 'ArticleController@getArticleList');//获取文章列表
        $router->post('schoolList', 'ArticleController@schoolList');//学校列表
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
        $router->post('findOrderForId', 'OrderController@findOrderForId');//订单详情
        $router->post('auditToId', 'OrderController@auditToId');//订单审核通过/不通过
        $router->post('orderUpOaForId', 'OrderController@orderUpOaForId');//订单修改oa状态
        $router->post('ExcelExport', 'OrderController@ExcelExport');//订单导出
//        $router->post('orderPay', 'OrderController@orderPay');//订单在线支付
//        $router->post('wxnotify_url', 'OrderController@wxnotify_url');//微信回调
//        $router->post('alinotify_url', 'OrderController@alinotify_url');//ali回调
//        $router->post('Pcpay', 'OrderController@Pcpay');//pc支付
    });
    //数据模块（szw）
    $router->group(['prefix' => 'statistics'], function () use ($router) {
        $router->post('StudentList', 'StatisticsController@StudentList');//学员统计
        $router->post('TeacherList', 'StatisticsController@TeacherList');//教师统计
        $router->post('TeacherClasshour', 'StatisticsController@TeacherClasshour');//教师课时详情
//        $router->post('LiveList', 'StatisticsrController@LiveList');//直播统计
//        $router->post('LiveDetails', 'StatisticsrController@LiveDetails');//直播详情
    });
    /*begin 系统管理   lys   */
        //系统用户管理模块
    $router->group(['prefix' => 'adminuser'], function () use ($router) {
        $router->post('getAdminUserList', 'AdminUserController@getAdminUserList');            //获取后台用户列表方法 √ 5.8
        $router->post('upUserForbidStatus', 'AdminUserController@upUserForbidStatus');        //更改账号状态方法（启用禁用） √√√ +1
        $router->post('upUserDelStatus', 'AdminUserController@upUserDelStatus');              //更改账号状态方法 (删除)  √√√  +1
        $router->post('getInsertAdminUser', 'CommonController@getInsertAdminUser');           //获取添加账号信息（school，roleAuth）方法 √
        $router->post('doInsertAdminUser', 'AdminUserController@doInsertAdminUser');          //添加账号方法 √  +1
        $router->post('getAuthList', 'AdminUserController@getAuthList');                      //获取角色列表方法 √
        $router->post('getAdminUserUpdate', 'AdminUserController@getAdminUserUpdate');        //获取账号信息（编辑） √√√
        $router->post('doAdminUserUpdate', 'AdminUserController@doAdminUserUpdate');          //编辑账号信息  √√  5.9  +1

    });
        //系统角色管理模块
    $router->group(['prefix' => 'role'], function () use ($router) {

        $router->post('getAuthList', 'RoleController@getAuthList');                           //获取后台角色列表方法    xxx
        $router->post('doRoleDel', 'RoleController@doRoleDel');                                //修改状态码(删除) √   +1
        $router->post('getRoleAuthInsert', 'CommonController@getRoleAuth');                   //获取role_auth列表 √√
        $router->post('doRoleAuthInsert', 'RoleController@doRoleInsert');                     //添加角色方法 √√ +1
        $router->post('getRoleAuthUpdate', 'RoleController@getRoleAuthUpdate');               // 获取角色信息（编辑）√√
        $router->post('doRoleAuthUpdate', 'RoleController@doRoleAuthUpdate');                 //编辑角色信息  √√ +1
    });
    /*end 系统管理  */

    $router->group(['prefix' => 'user'], function () use ($router) { //用户学员相关模块方法
        $router->post('postUserList', 'UserController@postUserList'); //获取学员列表方法
    });

    /*begin 网校系统  lys*/

    $router->group(['prefix' => 'school'], function () use ($router) {
        $router->post('getSchoolList', 'SchoolController@getSchoolList');                    //获取网校列表方法 √√√
        $router->post('doSchoolForbid', 'SchoolController@doSchoolForbid');                  //修改学校状态 （禁启)√√
        $router->post('doSchoolDel', 'SchoolController@doSchoolDel');                         //修改学校状态 （删除) √√
        $router->post('doInsertSchool', 'SchoolController@doInsertSchool');                  //添加分校信息并创建分校管理员 √√  +1
        $router->post('getSchoolUpdate', 'SchoolController@getSchoolUpdate');                //获取分校信息（编辑）√√
        $router->post('doSchoolUpdate', 'SchoolController@doSchoolUpdate');                  //编辑分校信息  √√   +1
        $router->post('getSchoolAdminById', 'SchoolController@getSchoolAdminById');          //查看分校超级管理角色信息 √√
        $router->post('doSchoolAdminById', 'SchoolController@doSchoolAdminById');            //编辑分校超级管理角色信息（给分校超管赋权限） √√
        $router->post('getAdminById', 'SchoolController@postAdminById');                      //获取分校超级管理用户信息（编辑） √√
        $router->post('doAdminUpdate', 'SchoolController@doAdminUpdate');                    //编辑分校超级管理用户信息   √√  +1
        $router->post('getSchoolTeacherList', 'SchoolController@getSchoolTeacherList');      //获取分校讲师列表  √√√  5.11
    });
    //end 网校系统     lys
});
/*****************end**********************/

