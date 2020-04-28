<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller {

    /*
         * @param  获取文章列表
         * @param  school_id   分校id
         * @param  type_id   分类id
         * @param  title   名称
         * @param  author  苏振文
         * @param  ctime   2020/4/27 9:48
         * return  array
         */
    public function getArticleList(){
        $school_id = isset($_POST['school_id'])?$_POST['school_id']:'';
        $type_id = isset($_POST['type_id'])?$_POST['type_id']:'';
        $title = isset($_POST['title'])?$_POST['title']:'';
        $num = isset($_POST['num'])?$_POST['num']:2;
        $page = isset($_POST['page'])?$_POST['page']:0;
        $list = Article::getArticleList($school_id,$type_id,$title,$page,$num);
        rDate('1','成功',$list);
    }
}
