<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Order;


class OrderController extends Controller {

    /*
         * @param  客户端生成预订单
         * @param  type 1安卓2苹果3h5
         * @param  class_id 课程id
         * @param  author  苏振文
         * @param  ctime   2020/5/25 11:20
         * return  array
         */
    public function createOrder() {
        $data['order_number'] = date('YmdHis', time()) . rand(1111, 9999);
        $data['admin_id'] = 0;  //操作员id
        $data['order_type'] = 2;        //1线下支付 2 线上支付
        $data['student_id'] = 1;
        $data['price'] = 1;
        $data['lession_price'] = 1;
        $data['pay_status'] = 4;
        $data['pay_type'] = 0;
        $data['status'] = 0;
        $data['oa_status'] = 0;              //OA状态
        $data['class_id'] = 2;
        $add = Order::insert($data);
        print_r($add);





//        $data = self::$accept_data;
//        $data['student_id'] = 1;
//        $orderid = Order::orderPayList($data);
//        return response()->json($orderid);
    }
}
