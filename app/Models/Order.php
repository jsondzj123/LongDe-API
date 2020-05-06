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
    public static function getList($data){
        if(empty($data['num'])){
            $data['num'] = 20;
        }
        $order = self::select('ld_order.*','ld_student.phone','ld_student.real_name')
            ->leftJoin('ld_student','ld_student.id','=','ld_order.user_id')
            ->where(function($query) use ($data) {
                if($data['school_id'] != ''){
                    $query->where('ld_student.school_id',$data['school_id']);
                }
                if($data['status'] != ''){
                    $query->where('ld_order.status',$data['status']);
                }
                if($data['order_number'] != ''){
                    $query->where('ld_order.order_number',$data['order_number']);
                }
                if($data['state_time'] != ''){
                    $query->where('ld_order.create_at',$data['state_time']);
                }
                if($data['end_time'] != ''){
                    $query->where('ld_order.create_at',$data['end_time']);
                }
            })
            ->orderBy('ld_order.id','desc')
            ->paginate($data['num']);
        return $order;
    }
}
