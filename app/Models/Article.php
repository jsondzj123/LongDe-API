<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model {
    //指定别的表名
    public $table = 'ld_article';
    //时间戳设置
    public $timestamps = false;
    /*
         * @param  获取文章列表
         * @param  school_id   分校id
         * @param  type_id   分类id
         * @param  title   名称
         * @param  author  苏振文
         * @param  ctime   2020/4/27 9:48
         * return  array
         */
    public static function getArticleList($school_id,$type_id,$title,$num){
        $where=[];
        if($school_id != ''){
            $where['school_id'] = $school_id;
        }
        if($type_id != ''){
            $where['article_type_id'] = $type_id;
        }
        $list = self::where($where)
            ->where('title','like','%'.$title.'%')
            ->orderBy('id','desc')
            ->simplePaginate($num);
        //条件数组
        $condition = [
              'school_id'=>$school_id,
              'type_id'=>$type_id,
              'title'=>$title,
        ];
        //分类列表
        $typelist = Articletype::Typelist();
        //分校列表

        $newlist['list'] = $list;
        $newlist['condition'] = $condition;
        $newlist['typelist'] = $typelist;
        return $newlist;
    }
}
