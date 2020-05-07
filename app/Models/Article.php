<?php
namespace App\Models;

use App\Tools\CurrentAdmin;
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
        $data['num'] = isset($data['num'])?$data['num']:1;
        $list = self::select('ld_article.id','ld_article.title','ld_article.create_at','ld_school.name','ld_article_type.typename','ld_admin_user.account')
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
            ->paginate($data['num']);
//            ->simplePaginate($data['num']);
//        //分类列表
//        $typelist = Articletype::Typelist();
        //分校列表
//        $school = School::SchoolAll();
//        $newlist['list'] = $list; //数据
//        $newlist['condition'] = $data; //条件
//        $newlist['typelist'] = $typelist; //类型
//        $newlist['school'] = $school; //学校
        return ['code' => 200 , 'msg' => '查询成功','data'=>$list];
    }
    /*
         * @param 修改文章状态
         * @param  $id 文章id
         * @param  $type 1启用0禁用
         * @param  $adminid 操作员id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 15:43
         * return  array
         */
    public static function editStatus($data){
        $articleOnes = self::where(['id'=>$data['id'],'is_del'=>1])->first();
        if(!$articleOnes){
            return ['code' => 204 , 'msg' => '参数不对'];
        }
        //启用
        if($data['type'] == 1){
            if($articleOnes['status'] == 1){
                return ['code' => 200 , 'msg' => '修改成功'];
            }
            $update = self::where(['id'=>$data['id']])->update(['status'=>1]);
            if($update){
                //加操作日志
                return ['code' => 200 , 'msg' => '修改成功'];
            }else{
                return ['code' => 201 , 'msg' => '修改失败'];
            }
        }else{
            //禁用
            if($articleOnes['status'] == 0){
                return ['code' => 200 , 'msg' => '修改成功'];
            }
            $update = self::where(['id'=>$data['id']])->update(['status'=>0]);
            if($update){
                //加操作日志
                return ['code' => 200 , 'msg' => '修改成功'];
            }else{
                return ['code' => 201 , 'msg' => '修改失败'];
            }
        }
    }
    /*
         * @param  软删除
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/28 17:33
         * return  array
         */
    public static function editDelToId($data){
        $articleOnes = self::where(['id'=>$data['id']])->first();
        if(!$articleOnes){
            return ['code' => 204 , 'msg' => '参数不对'];
        }
        if($articleOnes['is_del'] == 0){
            return ['code' => 200 , 'msg' => '删除成功'];
        }
        $update = self::where(['id'=>$data['id']])->update(['is_del'=>0]);
        if($update){
            //加操作日志
            return ['code' => 200 , 'msg' => '删除成功'];
        }else{
            return ['code' => 201 , 'msg' => '删除失败'];
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
            return ['code' => 201 , 'msg' => '内容为空'];
        }
        //缓存查出用户id和分校id
        $admin = CurrentAdmin::user();
        $data['school_id'] = $admin['school_id'];
        $data['user_id'] = $admin['id'];
        $data['update_at'] = date('Y-m-d H:i:s');
        $add = self::insert($data);
        if($add){
            return ['code' => 200 , 'msg' => '添加成功'];
        }else{
            return ['code' => 202 , 'msg' => '添加失败'];
        }
    }
    /*
         * @param  单条查询
         * @param  $id    文章id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 19:30
         * return  array
         */
    public static function findOne($data){
        if(empty($data['id'])){
            return ['code' => 201 , 'msg' => '参数为空'];
        }
        $find = self::select('ld_article.*','ld_school.name','ld_article_type.typename')
                ->leftJoin('ld_school','ld_school.id','=','ld_article.school_id')
                ->leftJoin('ld_article_type','ld_article_type.id','=','ld_article.article_type_id')
                ->where(['ld_article.id'=>$data['id'],'ld_article.is_del'=>1])
                ->first();
        if($find){
            unset($find['user_id']);
            unset($find['share']);
            unset($find['status']);
            unset($find['is_del']);
            unset($find['create_at']);
            unset($find['update_at']);
            return ['code' => 200 , 'msg' => '获取成功','data'=>$find];
        }else{
            return ['code' => 202 , 'msg' => '获取失败'];
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
        $id = $data['id'];
        unset($data['id']);
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
        //判断是否为空
        if(empty($data['title']) || empty($data['key_word']) || empty($data['sources'])|| empty($data['accessory'])|| empty($data['description'])|| empty($data['text'])){
            return ['code' => 201 , 'msg' => '参数不能为空'];
        }
        $data['update_at'] = date('Y-m-d H:i:s');
        $res = self::where(['id'=>$id])->update($data);
        if($res){
            return ['code' => 200 , 'msg' => '更新成功'];
        }else{
            return ['code' => 202 , 'msg' => '更新失败'];
        }
    }
}
