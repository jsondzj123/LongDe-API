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
        $return = self::where(['id'=>$id])->select('auth_id')->first();
        return $return;
    }





}
