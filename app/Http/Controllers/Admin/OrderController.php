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
//        $list = Order::getList(self::$accept_data);
        $aa = $request->post();
        $list = Order::getList($aa);
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
    public function ExcelExport(){
        $list = Order::getList(self::$accept_data);
    }
    /*
         * @param  OA修改状态
         * @param  $order_id  订单id
         * @param  $status   状态码1成功2失败
         * @param  author  苏振文
         * @param  ctime   2020/5/6 16:30
         * return  array
         */
    public function orderUpOaForId(){
        //获取提交的参数
        try{
            $data = Order::orderUpOaForId(self::$accept_data);
            return response()->json($data);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
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
//            $arr=[
//                'student_id'=>1,
//                'price'=>0.01,
//                'lession_price'=>100,
//                'pay_type'=>1,
//                'class_id'=>1
//            ];
//            $orderlist = Order::orderPayList($arr);
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
    /*
         * @param  微信回调地址
         * @param  author  苏振文
         * @param  ctime   2020/5/6 17:08
         * return  array
         */
    public function wxnotify_url(){
        $xml = file_get_contents("php://input");
        $notify = Order::wxnotify_url($xml);
        return response()->json($notify);
    }
    /*
         * @param 支付宝回调地址
         * @param  author  苏振文
         * @param  ctime   2020/5/6 17:09
         * return  array
         */
    public function alinotify_url(){
        $notify = Order::alinotify_url($_POST);
        return response()->json($notify);
    }
}
