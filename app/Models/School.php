<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model {
    //指定别的表名
    public $table = 'ld_school';
    //时间戳设置
    public $timestamps = false;

    /*
         * @param  分校列表
         * @param  author  苏振文
         * @param  ctime   2020/4/28 14:43
         * return  array
         */
    public static function SchoolAll(){
        $list = self::select('id','name')->where(['is_forbid'=>1,'is_del'=>1])->get()->toArray();
        return $list;
    }


}

