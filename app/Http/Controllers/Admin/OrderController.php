<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
class OrderController extends Controller {
    /*
         * @param  订单列表
         * @param  $school_id  分校id
         * @param  $status  状态
         * @param  $state_time 开始时间
         * @param  $end_time 结束时间
         * @param  $order_number 订单号
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
            return response()->json($data);
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
            return response()->json($data);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
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
         * @param  订单导出 excel表格
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/5/6 14:12
         * return  array
         */
    public function ExcelExport(){
            return Excel::download(new \App\Exports\OrderExport(self::$accept_data), 'order.xlsx');
    }

    /*
         * @param  对接oa
         * @param  $order_id
         * @param  author  苏振文
         * @param  ctime   2020/6/1 14:46
         * return  array
         */
    public function buttOa(){
        $data = self::$accept_data;
        $order = Order::where(['id'=>$data['order_id']])->first();
        //根据订单  查询用户信息  课程信息
        $student = Student::where(['id'=>$order['student_id'],'is_forbid'=>1])->first();
        $lession = Lesson::where(['id'=>$order['class_id'],'is_del'=>0,'is_forbid'=>0])->first();
        $newarr = [
            'orderNo' => $order['order_number'],
            'mobile' => empty($student['phone'])?'17319397103':$student['phone'],
            'price' => $order['price'],
            'courseName' => $lession['title'],
            'createTime' => $order['create_time'],
            'payTime' => $order['pay_time'],
            'payStatus' => 1,
            'payType' =>'PAY_OFFLINE_INPUT'
        ];
        print_r($newarr);die;
        $res = $this->curl($newarr);
        print_r($res);die;
    }
    //curl【模拟http请求】
    public function curl($receiptData){
        //小票信息
//        $POSTFIELDS = array("receipt-data" => $receiptData);
//        $POSTFIELDS = json_encode($POSTFIELDS);
        //正式购买地址 沙盒购买地址
//        $urlBuy = "https://buy.itunes.apple.com/verifyReceipt";
//        $urlSandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
//        $url = $sandbox ? $urlSandbox : $urlBuy;//向正式环境url发送请求(默认)
        $url = "47.110.127.119:8082/front/pay/syncOrder";
        //简单的curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $receiptData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
