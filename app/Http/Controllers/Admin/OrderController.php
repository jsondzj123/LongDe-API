<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

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
    public function orderList(){
        $list = Order::getList(self::$accept_data);
        return response()->json(['code' => 200 , 'msg' => '获取成功','data'=>$list]);
    }
    /*
         * @param  查看详情
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/5/6 9:56
         * return  array
         */
    public function findOrderForId(){
        //获取提交的参数
        try{
            $data = Order::findOrderForId(self::$accept_data);
            if($data['code'] == 200){
                return response()->json($data);
            } else {
                return response()->json($data);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  审核  通过/不通过
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/5/6 9:56
         * return  array
         */
    public function auditToId(){
        //获取提交的参数
        try{
            $data = Order::exitForIdStatus(self::$accept_data);
            if($data['code'] == 200){
                return response()->json($data);
            } else {
                return response()->json($data);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  订单导出 excel表格
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/5/6 14:12
         * return  array
         */
    public function ExxelExport(){
        $list = Order::getList(self::$accept_data);
    }




    /*
         * @param  app支付
         * @param
         * @param  author  苏振文
         * @param  ctime   2020/5/6 11:35
         * return  array
         */
    public function orderPay(){
        try{
            $orderlist = Order::orderPayList(self::$accept_data);
            return response()->json($orderlist);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }

    /*
         * @param  pc端支付
         * @param
         * @param  author  苏振文
         * @param  ctime   2020/5/6 11:42
         * return  array
         */
    public function Pcpay(){

    }
}
