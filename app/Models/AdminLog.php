<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model {
    //指定别的表名
    public $table      = 'ld_admin_operate_log';
    //时间戳设置
    public $timestamps = false;

    /*
     * @param  description   添加后台日志的方法
     * @param  data          数组数据
     * @param  author        dzj
     * @param  ctime         2020-04-27
     * return  int
     */
    public static function insertAdminLog($data) {
        return self::insertGetId($data);
    }
}
