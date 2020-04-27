<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adminuser extends Model {
    //指定别的表名
    public $table = 'ld_admin_user';
    //时间戳设置
    public $timestamps = false;
    /*
         * @param  descriptsion 后台账号信息
         * @param  $user_id     用户id
         * @param  author  苏振文
         * @param  ctime   2020/4/25 15:44
         * return  array
         */
    public static function GetUserOne($id){
        if(empty($id) || !intval($id)){
            return ['code'=>202,'msg'=>'参数为空或类型不正确'];
        }
        $userInfo = self::where(['id'=>$id])->first();
        if($userInfo){
            return ['code'=>200,'msg'=>'获取后台用户信息成功','data'=>$userInfo];
        }else{
            return ['code'=>201,'msg'=>'后台用户信息不存在'];
        }
    }
}
