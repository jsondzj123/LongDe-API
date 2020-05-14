<?php
namespace App\Models;

use App\Tools\CurrentAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

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
        $list = self::select('ld_article.id','ld_article.title','ld_article.create_at','ld_school.name','ld_article_type.typename','ld_admin.username')
            ->leftJoin('ld_school','ld_school.id','=','ld_article.school_id')
            ->leftJoin('ld_article_type','ld_article_type.id','=','ld_article.article_type_id')
            ->leftJoin('ld_admin','ld_admin.id','=','ld_article.user_id')
            ->where(function($query) use ($data) {
                 if(!empty($data['school_id']) && $data['school_id'] != ''){
                     $query->where('ld_article.school_id',$data['school_id']);
                 }
                 if(!empty($data['type_id']) && $data['type_id'] != '' ){
                     $query->where('ld_article.article_type_id',$data['type_id']);
                 }
                 if(!empty($data['title']) && $data['title'] != ''){
                     $query->where('ld_article.title','like','%'.$data['title'].'%')
                         ->orwhere('ld_article.id',$data['title']);
                 }
            })
            ->where(['ld_article.is_del'=>1,'ld_article_type.is_del'=>1,'ld_article_type.status'=>1,'ld_admin.is_del'=>1,'ld_admin.is_forbid'=>1,'ld_school.is_del'=>1,'ld_school.is_forbid'=>1])
            ->orderBy('ld_article.id','desc')
            ->paginate($data['num']);
        return ['code' => 200 , 'msg' => '查询成功','data'=>$list];
    }
    /*
         * @param 修改文章状态
         * @param  $id 文章id
         * @param  author  苏振文
         * @param  ctime   2020/4/28 15:43
         * return  array
         */
    public static function editStatus($data){
        if(empty($data['id']) || !isset($data['id'])){
            return ['code' => 201 , 'msg' => '参数为空或格式错误'];
        }
        $articleOnes = self::where(['id'=>$data['id']])->first();
        if(!$articleOnes){
            return ['code' => 204 , 'msg' => '参数不对'];
        }
        $status = ($articleOnes['status']==1)?0:1;
        $update = self::where(['id'=>$data['id']])->update(['status'=>$status,'update_at'=>date('Y-m-d H:i:s')]);
        if($update){
            //获取后端的操作员id
            $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Article' ,
                'route_url'      =>  'admin/Article/editStatus' ,
                'operate_method' =>  'update' ,
                'content'        =>  '操作'.json_encode($data) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '修改成功'];
        }else{
            return ['code' => 201 , 'msg' => '修改失败'];
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
        //判断分类id
        if(empty($data['id'])|| !isset($data['id'])){
            return ['code' => 201 , 'msg' => '参数为空或格式错误'];
        }
        $articleOnes = self::where(['id'=>$data['id']])->first();
        if(!$articleOnes){
            return ['code' => 204 , 'msg' => '参数不正确'];
        }
        if($articleOnes['is_del'] == 0){
            return ['code' => 200 , 'msg' => '删除成功'];
        }
        $update = self::where(['id'=>$data['id']])->update(['is_del'=>0,'update_at'=>date('Y-m-d H:i:s')]);
        if($update){
            //获取后端的操作员id
            $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Article' ,
                'route_url'      =>  'admin/Article/editDelToId' ,
                'operate_method' =>  'delete' ,
                'content'        =>  '软删除id为'.$data['id'],
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
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
        //判断分类id
        if(empty($data['article_type_id']) || !isset($data['article_type_id'])){
            return ['code' => 201 , 'msg' => '请正确选择分类'];
        }
        //判断标题
        if(empty($data['title']) || !isset($data['title'])){
            return ['code' => 201 , 'msg' => '标题不能为空'];
        }
        //判断图片
        if(empty($data['image']) || !isset($data['image'])){
            return ['code' => 201 , 'msg' => '图片不能为空'];
        }
        //判断摘要
        if(empty($data['description']) || !isset($data['description'])){
            return ['code' => 201 , 'msg' => '摘要不能为空'];
        }
        //判断正文
        if(empty($data['text']) || !isset($data['text'])){
            return ['code' => 201 , 'msg' => '正文不能为空'];
        }
        //缓存查出用户id和分校id
        $data['school_id'] = isset(AdminLog::getAdminInfo()->admin_user->school_id) ? AdminLog::getAdminInfo()->admin_user->school_id : 0;
        $data['user_id'] = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
        $data['update_at'] = date('Y-m-d H:i:s');
        $add = self::insert($data);
        if($add){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $data['user_id']  ,
                'module_name'    =>  'Article' ,
                'route_url'      =>  'admin/Article/addArticle' ,
                'operate_method' =>  'insert' ,
                'content'        =>  '新增数据'.json_encode($data) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
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
        if(empty($data['id']) || !isset($data['id'])){
            return ['code' => 201 , 'msg' => '参数为空'];
        }
        //缓存
        $key = 'article_findOne_'.$data['id'];
        if(Redis::get($key)) {
            return ['code' => 200 , 'msg' => '获取成功','data'=>json_decode(Redis::get($key),true)];
        }else{
            $find = self::select('ld_article.*','ld_school.name','ld_article_type.typename')
                ->leftJoin('ld_school','ld_school.id','=','ld_article.school_id')
                ->leftJoin('ld_article_type','ld_article_type.id','=','ld_article.article_type_id')
                ->where(['ld_article.id'=>$data['id'],'ld_article.is_del'=>1,'ld_school.is_del'=>1])
                ->first();
            if($find){
                unset($find['user_id'],$find['share'],$find['status'],$find['is_del'],$find['create_at'],$find['update_at']);
                Redis::setex($key,60,json_encode($find));
                return ['code' => 200 , 'msg' => '获取成功','data'=>$find];
            }else{
                return ['code' => 202 , 'msg' => '获取失败'];
            }
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
        if(empty($data['id'])){
            return ['code' => 201 , 'msg' => 'id为空或格式不正确'];
        }
        //判断分类id
        if(empty($data['article_type_id'])){
            return ['code' => 201 , 'msg' => '分类为空或格式不正确'];
        }
        //判断标题
        if(empty($data['title'])){
            return ['code' => 201 , 'msg' => '标题为空或格式不正确'];
        }
        //判断图片
        if(empty($data['image'])){
            return ['code' => 201 , 'msg' => '图片为空或格式不正确'];
        }
        //判断摘要
        if(empty($data['description'])){
            return ['code' => 201 , 'msg' => '摘要为空或格式不正确'];
        }
        //判断正文
        if(empty($data['text'])){
            return ['code' => 201 , 'msg' => '正文为空或格式不正确'];
        }
        $data['update_at'] = date('Y-m-d H:i:s');
        $id = $data['id'];
        unset($data['id']);
        $res = self::where(['id'=>$id])->update($data);
        if($res){
            //获取后端的操作员id
            $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Article' ,
                'route_url'      =>  'admin/Article/exitForId' ,
                'operate_method' =>  'update' ,
                'content'        =>  '修改id'.$id.'的内容,'.json_encode($data),
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '更新成功'];
        }else{
            return ['code' => 202 , 'msg' => '更新失败'];
        }
    }
}
