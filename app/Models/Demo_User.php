<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demo_User extends Model {
    //指定别的表名
    public $table = 'demo_user';
    //时间戳设置
    public $timesstamps = false;


    public static function getInfoById($id){
       // $info = self::get($id);
        $info = self::where(['id'=>$id])->first();
        return $info;
    }


}

