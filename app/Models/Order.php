<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    //指定别的表名
    public $table = 'ld_order';
    //时间戳设置
    public $timestamps = false;

    /*
         * @param  订单列表
         * @param  $school_id  分校id
         * @param  $status  状态
         * @param  $state_time 开始时间
         * @param  $end_time 结束时间
         * @param  $order_sn 订单号
         * @param  author  苏振文
         * @param  ctime   2020/5/4 14:41
         * return  array
         */
//    public static function getList($data){
//        $order = self::select('ld_order.*')
//            ->leftjoin('')
//
//    }
}
