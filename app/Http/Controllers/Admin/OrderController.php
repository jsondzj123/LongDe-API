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
        $list = Order::getList($data);
        return response()->json(['code' => 200 , 'msg' => '获取成功','data'=>$list]);
    }
    /*
         * @param  微信支付
         * @param  $price  支付的钱
         * @param  author  苏振文
         * @param  ctime   2020/5/4 16:14
         * return  array
         */
    public function Wxpay(){
        //生成预订单
        $price = 0.01;
        $ordernumber = "202005041720111478";
        $return = app('wx')->getPrePayOrder($ordernumber,$price);
        print_r($return);
    }
    public function Alipay(){
        //生成预订单
        $price = 0.01;
        $ordernumber = "202005041720111478";
        $return = app('ali')->createAppPay($ordernumber,'商品简介',$price);
        print_r($return);
    }
}
