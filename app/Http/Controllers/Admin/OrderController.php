<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller {
    /*
         * @param  订单列表
         * @param  $school_id  分校id
         * @param  $status  状态
         * @param  $state_time 开始时间
         * @param  $end_time 结束时间
         * @param  $order_sn 订单号
         * @param  author  苏振文
         * @param  ctime   2020/5/4 11:29
         * return  array
         */
    public function orderList(Request $request){
        $data = $request->post();
        $list = Order::getList();
    }
}
