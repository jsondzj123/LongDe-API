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
        $return = self::where(['id'=>$id])->first();
        return $return;
    }
}
