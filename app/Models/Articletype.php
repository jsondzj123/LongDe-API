<?php
namespace App\Models;

use App\Tools\CurrentAdmin;
use Illuminate\Database\Eloquent\Model;

class Articletype extends Model {
    //指定别的表名
    public $table = 'ld_article_type';
    //时间戳设置
    public $timestamps = false;
    /*
         * @param  获取分类列表
         * @param  school_id   分校id
         * @param  title   名称
         * @param  author  苏振文
         * @param  ctime   2020/4/27 9:48
         * return  array
         */
    public static function getArticleList($data){
        $where['ld_article_type.is_del'] = 1;
       if($data['school_id'] != ''){
           $where['ld_article_type.school_id'] = $data['school_id'];
       }
       $page = (!empty($data['page']))?$data['page']:20;
       $typelist = self::select('ld_article_type.id','ld_article_type.typename','ld_article_type.status','ld_school.name','ld_admin_user.account')
           ->leftJoin('ld_school','ld_school.id','=','ld_article_type.school_id')
           ->leftJoin('ld_admin_user','ld_admin_user.id','=','ld_article_type.user_id')
           ->where($where)
           ->orderBy('ld_article_type.id','desc')
           ->paginate($page);
       return ['code' => 200 , 'msg' => '获取成功','data'=>$typelist];
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
    /*
         * @param  修改状态
         * @param  $id 分类id
         * @param  $type 1启用2禁用
         * @param  author  苏振文
         * @param  ctime   2020/4/30 14:22
         * return  array
         */
    public static function editStatusToId($data){
        if($data['id'] == ''){
            return false;
        }
        $find = self::where(['id'=>$data['id'],'is_del'=>1])->first();
        if(!$find){
            return ['code' => 201 , 'msg' => '参数错误'];
        }
        //启用
        if($data['type'] == 1){
            if($find['status'] ==1){
              return ['code' => 200 , 'msg' => '修改成功'];
            }
            $up = self::where(['id'=>$data['id']])->update(['status'=>1]);
            if($up){
                //加日志
                return ['code' => 200 , 'msg' => '修改成功'];
            }else{
                return ['code' => 202 , 'msg' => '修改失败'];
            }
        }else{
            if($find['status'] ==0){
                return ['code' => 200 , 'msg' => '修改成功'];
            }
            $up = self::where(['id'=>$data['id']])->update(['status'=>0]);
            if($up){
                //加日志
                return ['code' => 200 , 'msg' => '修改成功'];
            }else{
                return ['code' => 202 , 'msg' => '修改失败'];
            }
        }
    }
    /*
         * @param  软删除
         * @param  $id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/30 15:38
         * return  array
         */
    public static function editDelToId($data){
        $articleOnes = self::where(['id'=>$data['id']])->first();
        if(!$articleOnes){
            return ['code' => 201 , 'msg' => '参数错误'];
        }
        if($articleOnes['is_del'] == 0){
            return ['code' => 200 , 'msg' => '删除成功'];
        }
        $update = self::where(['id'=>$data['id']])->update(['is_del'=>0]);
        if($update){
            //加操作日志
            return ['code' => 200 , 'msg' => '删除成功'];
        }else{
            return ['code' => 202 , 'msg' => '删除失败'];
        }
    }
    /*
         * @param  添加分类
         * @param  $typename  类型名称
         * @param  $description  类型简介
         * @param  author  苏振文
         * @param  ctime   2020/4/30 14:44
         * return  array
         */
    public static function addType($data){
        //获取用户信息
        $admin = CurrentAdmin::user();
        $data['school_id'] = $admin['school_id'];
        $data['user_id'] = $admin['id'];
        $data['update_at'] = date('Y-m-d H:i:s');
        if($data['typename'] == '' || $data['description']==''){
            return ['code' => 201 , 'msg' => '参数不能为空'];
        }
        $ones = self::where($data)->first();
        if($ones){
            return ['code' => 202 , 'msg' => '参数已存在'];
        }else {
            $add = self::insert($data);
            if($add){
                //加日志
                return ['code' => 200 , 'msg' => '添加成功'];
            }else{
                return ['code' => 203 , 'msg' => '添加失败'];
            }
        }
    }
    /*
         * @param  修改信息
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/30 15:15
         * return  array
         */
    public static function editForId($data){
        $id = $data['id'];
        if($data['typename'] =='' || $data['description']==''){
            return ['code' => 201 , 'msg' => '参数不能为空'];
        }
        unset($data['id']);
        $update = self::where(['id'=>$id])->update($data);
        if($update){
            //加日志
            return ['code' => 200 , 'msg' => '修改成功'];
        }else{
            return ['code' => 202 , 'msg' => '修改失败'];
        }
    }
    /*
         * @param  单条查询
         * @param  $id
         * @param  author  苏振文
         * @param  ctime   2020/5/4 10:02
         * return  array
         */
    public static function oneFind($data){
        $find = self::select('ld_article_type.id','ld_article_type.typename','ld_article_type.description','ld_school.name')
            ->leftJoin('ld_school','ld_school.id','=','ld_article_type.school_id')
            ->where(['ld_article_type.id'=>$data['id'],'ld_article_type.is_del'=>1])
            ->first();
        return ['code' => 200 , 'msg' => '获取成功','data'=>$find];
    }
}
