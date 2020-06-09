<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Article;

class ArticleController extends Controller {
    //获取分类和学校
    public function schoolList(){
        $role_id = isset(AdminLog::getAdminInfo()->admin_user->role_id) ? AdminLog::getAdminInfo()->admin_user->role_id : 0;
        $data = Article::schoolANDtype($role_id);
        return response()->json(['code' => 200 , 'msg' =>'成功','school'=>$data[0],'type'=>$data[1],]);
    }
    /*
         * @param  新增文章
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/28 17:35
         * return  array
         */
    public function addArticle(){
        //获取提交的参数
        try{
            $data = Article::addArticle(self::$accept_data);
            return response()->json($data);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  获取文章列表
         * @param  school_id   分校id
         * @param  type_id   分类id1
         * @param  title   名称
         * @param  author  苏振文
         * @param  ctime   2020/4/27 9:48
         * return  array
         */
    public function getArticleList(){
        try{
            print_r(self::$accept_data);die;
            $list = Article::getArticleList(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  文章表禁用或启用
         * @param  $id    文章id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 15:4  1
         * return  array
         */
    public function editStatusToId(){
        try{
            $list = Article::editStatus(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }

    }
    /*
         * @param  文章表删除
         * @param  $id    文章id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 16:35
         * return  array
         */
    public function editDelToId(){
        try{
            $list = Article::editDelToId(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  单条查询
         * @param  $id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 17:36
         * return  array
         */
    public function findToId(){
        try{
            $list = Article::findOne(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  文章修改
         * @param  $data  数组参数
         * @param  author  苏振文
         * @param  ctime   2020/4/28 19:42
         * return  array
         */
    public function exitForId(){
        try{
            $list = Article::exitForId(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
