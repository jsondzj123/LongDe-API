<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller {
    /*
         * @param  新增文章
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/28 17:35
         * return  array
         */
    public function addArticle(Request $request){
        $data = $request->post();
        $list = Article::addArticle($data);
        if($list){
            rDate($list,'成功');
        }else{
            rDate($list,'失败');
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
    public function getArticleList(Request $request){
        $data = $request->post();
        $list = Article::getArticleList($data);
        rDate('1','成功',$list);
    }
    /*
         * @param  文章表禁用或启用
         * @param  $id    文章id
         * @param  $type   1启用0禁用
         * @param  author  苏振文
         * @param  ctime   2020/4/28 15:4  1
         * return  array
         */
    public function editStatusToId(){
        $id = $_POST['id'];
        $type = $_POST['type'];
        //后台操作员   id
        $adminid = 1;
        $update = Article::editStatus($id,$adminid,$type);
        if($update==200){
            rDate($update,'操作成功');
        }else{
            rDate($update,'操作失败');
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
        $id = $_POST['id'];
        //后台操作员   id
        $adminid = 1;
        $update = Article::editDelToId($id,$adminid);
        if($update==200){
            rDate($update,'操作成功');
        }else{
            rDate($update,'操作失败');
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
        $id = $_POST['id'];
        $find = Article::findOne($id);
        if($find){
            rDate('200','获取成功',$find);
        }else{
            rDate($find,'获取失败');
        }
    }
    /*
         * @param  文章修改
         * @param  $data  数组参数
         * @param  author  苏振文
         * @param  ctime   2020/4/28 19:42
         * return  array
         */
    public function exitForId(Request $request){
        $data = $request->post();
        $find = Article::exitForId($data);
        if ($find){
            rDate('200','修改成功');
        }else{
            rDate('300','修改失败');
        }
    }
}
