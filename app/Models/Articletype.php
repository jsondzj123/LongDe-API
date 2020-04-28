<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articletype extends Model {
    //指定别的表名
    public $table = 'ld_article_type';
    //时间戳设置
    public $timestamps = false;
    /*
         * @param  获取分类列表
         * @param  school_id   分校id
         * @param  type_id   分类id
         * @param  title   名称
         * @param  author  苏振文
         * @param  ctime   2020/4/27 9:48
         * return  array
         */
    public static function getArticleList($school_id=''){
        $where['is_del'] = 1;
       if($school_id != ''){
           $where['school_id'] = $school_id;
       }
       $typelist = self::leftjoin('', "order_info.goods_id", "=", "goods.id")->where($where)->orderBy('id','desc')->get()->toArray();
    }
    /*
         * @param  分类简单查询
         * @param  author  苏振文
         * @param  ctime   2020/4/27 16:48
         * return  array
         */
    public static function Typelist(){
        $typelist = self::where(['is_del'=>1])->select('id','typename')->get()->toArray();
        return $typelist;
    }
}
