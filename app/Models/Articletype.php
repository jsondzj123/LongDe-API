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
         * @param  title   名称
         * @param  author  苏振文
         * @param  ctime   2020/4/27 9:48
         * return  array
         */
    public static function getArticleList($school_id,$page){
        $where['ld_article_type.is_del'] = 1;
       if($school_id != ''){
           $where['ld_article_type.school_id'] = $school_id;
       }
       $typelist = self::select('ld_article_type.*','ld_school.name','ld_admin_user.account')
           ->leftJoin('ld_school','ld_school.id','=','ld_article_type.school_id')
           ->leftJoin('ld_admin_user','ld_admin_user.id','=','ld_article_type.user_id')
           ->where($where)
           ->orderBy('ld_article_type.id','desc')
           ->paginate($page);
       return $typelist;
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
    public static function editStatusToId($type='',$id=''){
        if($id == ''){
            return false;
        }
        $find = self::where(['id'=>$id,'is_del'=>1])->first();
        if(!$find){
            return 500;
        }
        //启用
        if($type == 1){
            if($find['status'] ==1){
                return 200;
            }
            $up = self::where(['id'=>$id])->update(['status'=>1]);
            if($up){
                //加日志
                return 200;
            }else{
                return 500;
            }
        }else{
            if($find['status'] ==0){
                return 200;
            }
            $up = self::where(['id'=>$id])->update(['status'=>0]);
            if($up){
                //加日志
                return 200;
            }else{
                return 500;
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
    public static function editDelToId($id){
        $articleOnes = self::where(['id'=>$id])->field();
        if(!$articleOnes){
            return 404;
        }
        if($articleOnes['is_del'] == 0){
            return 200;
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
         * @param  添加分类
         * @param  $typename  类型名称
         * @param  $description  类型简介
         * @param  author  苏振文
         * @param  ctime   2020/4/30 14:44
         * return  array
         */
    public static function addType($data){
        //获取用户信息
        $data['school_id'] = 1;
        $data['user_id'] = 1;
        $data['update_at'] = date('Y-m-d H:i:s');
        if($data['typename'] == '' || $data['description']==''){
            return 300;
        }
        $ones = self::where($data)->first();
        if($ones){
            return 400;
        }else {
            $add = self::insert($data);
            if($add){
                //加日志
                return 200;
            }else{
                return 500;
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
            return 300;
        }
        unset($data['id']);
        $update = self::where(['id'=>$id])->update($data);
        if($update){
            //加日志
            return 200;
        }else{
            return 500;
        }
    }
}
