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
    public static function getArticleList($data){
        //获取用户网校id
        $data['num'] = isset($data['num'])?$data['num']:20;
        $list = self::select('ld_article.*','ld_school.name','ld_article_type.typename','ld_admin_user.account')
            ->leftJoin('ld_school','ld_school.id','=','ld_article.school_id')
            ->leftJoin('ld_article_type','ld_article_type.id','=','ld_article.article_type_id')
            ->leftJoin('ld_admin_user','ld_admin_user.id','=','ld_article.user_id')
            ->where(function($query) use ($data) {
                 if($data['school_id'] != ''){
                     $query->where('ld_article.school_id',$data['school_id']);
                 }
                 if($data['type_id'] != ''){
                     $query->where('ld_article.article_type_id',$data['type_id']);
                 }
                 if($data['title'] != ''){
                     $query->where('ld_article.title','like','%'.$data['title'].'%')
                         ->orwhere('ld_article.id',$data['title']);
                 }
            })
            ->orderBy('id','desc')
            ->simplePaginate($data['num']);
        //分类列表
        $typelist = Articletype::Typelist();
        //分校列表
        $school = School::SchoolAll();
        $newlist['list'] = $list; //数据
        $newlist['condition'] = $data; //条件
        $newlist['typelist'] = $typelist; //类型
        $newlist['school'] = $school; //学校
        return $newlist;
    }
    /*
         * @param 修改文章状态
         * @param  $id 文章id
         * @param  $adminid 操作员id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 15:43
         * return  array
         */
    public static function editStatus($id,$adminid){
        $articleOnes = self::where(['id'=>$id,'is_del'=>1])->field();
        if(!$articleOnes){
            return 404;
        }
        if($articleOnes['status'] == 0){
            return 300;
        }
         $update = self::where(['id'=>$id])->update(['status'=>0]);
        if($update){
            //加操作日志
            return 200;
        }else{
            return 500;
        }
    }
    /*
         * @param  软删除
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/28 17:33
         * return  array
         */
    public static function editDelToId($id,$adminid){
        $articleOnes = self::where(['id'=>$id,'is_del'=>0])->field();
        if(!$articleOnes){
            return 404;
        }
        if($articleOnes['is_del'] == 0){
            return 300;
        }
        $update = self::where(['id'=>$id])->update(['is_del'=>0]);
        if($update){
            //加操作日志
            return 200;
        }else{
            return 500;
        }
    }
    /*
         * @param  新增
         * @param  school_id   分校id
         * @param  article_type_id   分类id
         * @param  title   标题
         * @param  image   封面
         * @param  key_word   关键词
         * @param  sources  来源
         * @param  accessory   附件
         * @param  description  摘要
         * @param  text   正文
         * @param  author  苏振文
         * @param  ctime   2020/4/28 17:45
         * return  array
         */
    public static function addArticle($data){
        if(empty($data['article_type_id']) ||empty($data['title'])||empty($data['image'])||empty($data['key_word'])||empty($data['sources'])||empty($data['accessory'])||empty($data['description'])||empty($data['text'])){
            //内容为空
            return 400;
        }
        //图片上传
        //附件上传
        //缓存查出用户id和分校id
        $adminid = '';
        $data['school_id'] = 1;
        $data['user_id'] = 1;
        $add = self::insert($data);
        if($add){
            return 200;
        }else{
            return 500;
        }
    }
    /*
         * @param  单条查询
         * @param  $id    文章id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 19:30
         * return  array
         */
    public static function findOne($id){
        if(empty($id)){
            return 400;//参数为空
        }
        $find = self::select('ld_article.*,ld_school.name,ld_article_type.type_name')
                 ->leftJoin('ld_school','ld_school.id','=','ld_article.school_id')
                 ->leftJoin('ld_article_type','ld_article_type.id','=','ld_article.article_type_id')
                 ->where(['ld_article.id'=>$id,'ld_article.is_del'=>1])->first();
        if($find){
            $type = Articletype::getArticleList($find['school_id']);
            $arr = [
                'list'=>$find,
                'typelist'=>$type
            ];
            return $arr;
        }else{
            return 500;//条件不对
        }
    }
    /*
         * @param  单条修改
         * @param  id   文章id
         * @param  article_type_id   分类id
         * @param  title   标题
         * @param  image   封面
         * @param  key_word   关键词
         * @param  sources  来源
         * @param  accessory   附件
         * @param  description  摘要
         * @param  text   正文
         * @param  author  苏振文
         * @param  ctime   2020/4/28 19:43
         * return  array
         */
    public static function exitForId($data){
        //判断封面是否有更新
        if(!empty($data['image'])){
            //进行封面上传
            $data['image'] = '';
        }
        //判断封面是否有更新
        if(!empty($data['accessory'])){
            //进行封面上传
            $data['accessory'] = '';
        }
        $res = self::where(['id'=>$data['id']])->update($data);
        return $res;
    }
}
