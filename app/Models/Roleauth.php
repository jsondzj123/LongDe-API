<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roleauth extends Model {
    //指定别的表名
    public $table = 'ld_role_auth';
    //时间戳设置
    public $timestamps = false;

/*
     * @param  descriptsion 角色查询
     * @param  $id     参数
     * @param  author  苏振文
     * @param  ctime   2020/4/25 15:52
     * return  array
     */
    public static function getRoleOne($id){
        if(empty($id) || !intval($id)){
            return ['code'=>202,'msg'=>'参数为空或类型不正确'];
        }
        $return = self::where(['id'=>$id])->select('id','auth_id','is_del')->first()->toArray();
         if($return){
            return ['code'=>200,'msg'=>'获取角色信息成功','data'=>$return];
        }else{
            return ['code'=>201,'msg'=>'角色信息不存在'];
        }
    }

    public static function getRoleAuthAll($where=[],$page =1,$limit = 10){
        $return = self::where(function($query) use ($where){
                if($where['search'] != ''){
                    $query->where('r_name','like','%'.$where['search'].'%');
                    $query->where('school_id','=',$where['school_id']);
                }
            })->forPage($page,$limit)->get()->toArray();
        return $return;
    }


    public static function upRoleStatus($where,$update){
        $result = self::where($where)->update($update);
        return $result;
    }





}
