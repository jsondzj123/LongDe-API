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
        $return = self::where(['id'=>$id])->select('auth_id')->first();
         if($return){
            return ['code'=>200,'msg'=>'获取角色信息成功','data'=>$return];
        }else{
            return ['code'=>201,'msg'=>'角色信息不存在'];
        }
    }





}
