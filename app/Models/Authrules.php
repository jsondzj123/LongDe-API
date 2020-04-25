<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authrules extends Model {
    //指定别的表名   权限表
    public $table = 'ld_auth_rules';
    //时间戳设置
    public $timestamps = false;
    /*
         * @param  descriptsion 权限查询
         * @param  $name    url名
         * @param  author  苏振文
         * @param  ctime   2020/4/25 15:51
         * return  array
         */
    public static function getAuthOne($name){
        $return = self::where(['name'=>$name])->first()->toArray();
        return $return;
    }
}
