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
//        $data = self::$accept_data;
//        $data['student_id'] = 1;
//        $orderid = Order::orderPayList($data);
        $lesson = Lesson::get()->toArray();
        print_r($lesson);die;
        return response()->json($orderid);
    }
}
