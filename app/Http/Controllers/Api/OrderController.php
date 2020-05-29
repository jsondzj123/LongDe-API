<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonSchool;
use App\Models\Order;
use App\Models\Student;
use App\Models\StudentAccounts;
use App\Models\StudentAccountlog;
use App\Tools\AlipayFactory;
use App\Tools\WxpayFactory;


class OrderController extends Controller
{

    /*
         * @param  我的订单
         * @param  $type    参数
         * @param  author  苏振文
         * @param  ctime   2020/5/28 10:32
         * return  array
         */
    public function myOrderlist(){
        $data = self::$accept_data;
        //每页显示的条数
        $pagesize = (int)isset($data['pageSize']) && $data['pageSize'] > 0 ? $data['pageSize'] : 10;
        $page     = isset($data['page']) && $data['page'] > 0 ? $data['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        switch($data['type']){
            //0全部
            case "0":
            //1已完成
            case "1":
            //未完成
            case "2":

        }
        $count = Order::where(['student_id'=>$data['user_info']['user_id']])->count(); //全部条数
        $success = Order::where(['student_id'=>$data['user_info']['user_id'],'status'=>2])->count(); //完成
        $fily = Order::where(['student_id'=>$data['user_info']['user_id'],'status'=>'< 2'])->count(); //未完成
        $orderlist = [];
        if($count >0){
            $orderlist =Order::select('ld_order.id','ld_order.order_number','ld_order.create_at','ld_order.price','ld_order.status','ld_order.pay_time','ld_lessons.title')
                ->leftJoin('ld_lessons','ld_order.class_id','=','ld_lessons.id')
                ->where(['ld_order.student_id'=>$data['user_info']['user_id']])
                ->orderByDesc('ld_order.id')
                ->offset($offset)->limit($pagesize)
                ->get()->toArray();
        }
        $page=[
            'pageSize'=>$pagesize,
            'page' =>$page,
            'total'=>$count
        ];
        $arrcount =[
            'count'=>$count,
            'success'=>$success,
            'fily'=>$fily
        ];
        return ['code' => 200 , 'msg' => '获取成功','data'=>$orderlist,'arrcount'=>$arrcount,'page'=>$page];
    }
    /*
         * @param  descriptsion 作用
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/5/28 15:07
         * return  array
         */
    public function myPricelist(){
        $data = self::$accept_data;
        //每页显示的条数
        $pagesize = (int)isset($data['pageSize']) && $data['pageSize'] > 0 ? $data['pageSize'] : 10;
        $page     = isset($data['page']) && $data['page'] > 0 ? $data['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        $count = StudentAccountlog::where(['user_id'=>$data['user_info']['user_id']])->count();
        $pricelog = [];
        if($count > 0){
            $pricelog = StudentAccountlog::select('price','status','create_at')->where(['user_id'=>$data['user_info']['user_id']])
                ->orderByDesc('id')
                ->offset($offset)->limit($pagesize)
                ->get()->toArray();
        }
        $page=[
            'pageSize'=>$pagesize,
            'page' =>$page,
            'total'=>$count
        ];
        return ['code' => 200 , 'msg' => '获取成功','data'=>$pricelog,'page'=>$page];
    }
    /*
         * @param  客户端生成预订单
         * @param  type 1安卓2苹果3h5
         * @param  class_id 课程id
         * @param  author  苏振文
         * @param  ctime   2020/5/25 11:20
         * return  array
         */
    public function createOrder(){
        $data = self::$accept_data;
        if($data['user_info']['user_type'] ==1){
            return ['code' => 204 , 'msg' => '请先登录'];
        }
        $data['student_id'] = $data['user_info']['user_id'];
        $orderid = Order::orderPayList($data);
        return response()->json($orderid);
    }

    /*
         * @param  支付
         * type=1购买 传参
         *    user_id
         *    order_id
         *    pay_type(1微信2支付宝3汇聚微信4汇聚支付宝5余额支付
         * type=2充值 传参
         *    user_id
         *    price
         *    pay_type(1微信2支付宝3汇聚微信4汇聚支付宝5余额支付)
         * @param  author  苏振文
         * @param  ctime   2020/5/25 17:34
         * return  array
         */
    public function orderPay(){
        $data = self::$accept_data;
        //获取用户信息
        $user_id = $data['user_info']['user_id'];
        $user_balance = $data['user_info']['balance'];
        $user_school_id = $data['user_info']['school_id'];
        //判断支付类型
        if (empty($data['type']) || !isset($data['type']) || !in_array($data['type'], [1, 2])) {
            return ['code' => 201, 'msg' => '请选择类型'];
        }
        //判断支付方式
        if (empty($data['pay_type']) || !isset($data['pay_type']) || !in_array($data['pay_type'], [1, 2, 3, 4, 5])) {
            return ['code' => 201, 'msg' => '请选择支付方式'];
        }
        if ($data['type'] == 1) {
            //判断订单id
            if (empty($data['order_id']) || !isset($data['order_id'])) {
                return ['code' => 201, 'msg' => '请选择订单'];
            }
            //获取订单信息
            $order = Order::where(['id' => $data['order_id'], 'student_id' => $user_id])->first()->toArray();
            if(!$order){
                return ['code' => 201, 'msg' => '订单数据有误'];
            }
            if ($order['status'] > 0) {
                return ['code' => 202, 'msg' => '此订单已支付'];
            }
            //判断用户网校，根据网校查询课程信息
            if ($user_school_id == 1) {
                //根据课程id 查询价格
                $lesson = Lesson::select('id', 'title', 'cover', 'price', 'favorable_price')->where(['id' => $order['class_id'], 'is_del' => 0, 'is_forbid' => 0, 'status' => 2, 'is_public' => 0])->first()->toArray();
                if (!$lesson) {
                    return ['code' => 204, 'msg' => '此课程选择无效'];
                }
            } else {
                //根据课程id 网校id 查询网校课程详情
                $lesson = LessonSchool::select('id', 'title', 'cover', 'price', 'favorable_price')->where(['lesson_id' => $order['class_id'], 'school_id' => $user_school_id, 'is_del' => 0, 'is_forbid' => 0, 'status' => 1, 'is_public' => 0])->first()->toArray();
                if (!$lesson) {
                    return ['code' => 204, 'msg' => '此课程选择无效'];
                }
            }
            if ($data['pay_type'] == 5) {
                if ($lesson['favorable_price'] > $user_balance) {
                    return ['code' => 202, 'msg' => '余额不足，请充值！！！！！'];
                } else {
                    //扣除用户余额 修改订单信息 加入用户消费记录日志  商品增加购买基数  用户关联的课程加上起始时间
                    $end_balance = $user_balance - $lesson['favorable_price'];
                    Student::where(['id' => $user_id])->update(['balance' => $end_balance]);
                    Order::where(['id' => $data['order_id']])->update(['pay_type' => 5, 'status' => 1, 'pay_time' => date('Y-m-d H:i:s'), 'update_at' => date('Y-m-d H:i:s')]);
                    StudentAccountlog::insert(['user_id' => $user_id, 'price' => $lesson['favorable_price'], 'end_price' => $end_balance, 'status' => 2, 'class_id' => $order['class_id']]);
                    if ($user_school_id == 1) {
                        Lesson::where(['id' => $lesson['id']])->update(['buy_num' => $lesson['buy_num'] + 1]);
                    } else {
                        LessonSchool::where(['id' => $lesson['id']])->update(['buy_num' => $lesson['buy_num'] + 1]);
                    }
                    return response()->json(['code' => 200, 'msg' => '购买成功']);
                }
            } else {
                $sutdent_price = [
                    'user_id' => $user_id,
                    'order_number' => $order['order_number'],
                    'price' => $lesson['favorable_price'],
                    'pay_type' => $data['pay_type'],
                    'order_type' => 2,
                    'status' => 0
                ];
                StudentAccounts::insert($sutdent_price);
                $return = $this->payStatus($order['order_number'], $data['pay_type'], $lesson['favorable_price'],$user_school_id,1);
                return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $return]);
            }
        } else {
            $sutdent_price = [
                'user_id' => $user_id,
                'order_number' => date('YmdHis', time()) . rand(1111, 9999),
                'price' => $data['price'],
                'pay_type' => $data['pay_type'],
                'order_type' => 1,
                'status' => 0
            ];
            $add = StudentAccounts::insert($sutdent_price);
            if ($add) {
                $return = self::payStatus($sutdent_price['order_number'], $data['type'], $data['price'],$user_school_id,2);
                return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $return]);
            }
        }
    }
    //$type  1微信2支付宝3汇聚微信4汇聚支付宝
    //pay_type   1购买2充值
    public function payStatus($order_number, $type, $price,$school_id,$pay_type){
        switch($type){
            case "1":
                //根据分校查询对应信息
                $wxpay = new WxpayFactory();
                return $return = $wxpay->getPrePayOrder($order_number, $price,$pay_type);
            case "2":
                $alipay = new AlipayFactory();
                $return = $alipay->createAppPay($order_number, '龙德产品', 0.01,$pay_type);
                $alipay = [
                    'alipay' => $return
                ];
                return $alipay;
            case "3":
                //根据分校查询对应信息
                if($pay_type == 1){
                    $notify = "http://".$_SERVER['HTTP_HOST']."/Api/notify/hjWxnotify";
                }else{
                    $notify = "http://".$_SERVER['HTTP_HOST']."/Api/notify/hjWxTopnotify";
                }
                $arr=[
                    'p0_Version'=>'1.0',
                    'p1_MerchantNo'=>'888108900009969',
                    'p2_OrderNo'=>$order_number,
                    'p3_Amount'=>$price,
                    'p4_Cur'=>1,
                    'p5_ProductName'=>"龙德产品",
                    'p9_NotifyUrl'=>$notify,
                    'q1_FrpCode'=>'WEIXIN_APP',
                    'q7_AppId'=>'',
                    'qa_TradeMerchantNo'=>'777170100269422'
                ];
                $str = "15f8014fee1642fbb123fb5684cda48b";
                $token = $this->hjHmac($arr,$str);
                $arr['hmac'] = $token;
                if(strlen($token) ==32){
                    $aaa = $this->hjpost($arr);
                    print_r($aaa);die;
                }
            case "4":
                //根据分校查询对应信息
                if($pay_type == 1){
                    $notify = "http://".$_SERVER['HTTP_HOST']."/Api/notify/hjAlinotify";
                }else{
                    $notify = "http://".$_SERVER['HTTP_HOST']."/Api/notify/hjAliTopnotify";
                }
                $arr=[
                    'p0_Version'=>'1.0',
                    'p1_MerchantNo'=>'888108900009969',
                    'p2_OrderNo'=>$order_number,
                    'p3_Amount'=>$price,
                    'p4_Cur'=>1,
                    'p5_ProductName'=>"龙德产品",
                    'p9_NotifyUrl'=>$notify,
                    'q1_FrpCode'=>'ALIPAY_APP',
                    'qa_TradeMerchantNo'=>'777170100269422'
                ];
                $str = "15f8014fee1642fbb123fb5684cda48b";
                $token = $this->hjHmac($arr,$str);
                $arr['hmac'] = $token;
                if(strlen($token) ==32){
                    $aaa = $this->hjpost($arr);
                    print_r($aaa);die;
                }
        }
    }

    //  苹果内购 充值余额 生成预订单
    public function iphonePayCreateOrder(){
        $data = self::$accept_data;
        $user_id = $data['user_info']['user_id'];
        //生成预订单
        $sutdent_price = [
            'user_id' => $user_id,
            'order_number' => date('YmdHis', time()) . rand(1111, 9999),
            'price' => $data['price'],
            'pay_type' => 5,
            'order_type' => 1,
            'status' => 0
        ];
        StudentAccounts::insert($sutdent_price);
        return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $sutdent_price]);
    }
    // ios轮询查看订单是否成功
    public function iosPolling(){
        $data = self::$accept_data;
        $user_id = $data['user_info']['user_id'];
        $list = StudentAccounts::where(['user_id'=>$user_id,'order_number'=>$data['order_number']])->first()->toArray();
        if($list['status'] == 0){
            return response()->json(['code' => 202, 'msg' => '暂未支付']);
        }
        if($list['status'] == 1){
            return response()->json(['code' => 200, 'msg' => '支付成功']);
        }
        if($list['status'] == 2){
            return response()->json(['code' => 201, 'msg' => '支付失败']);
        }
    }

    //汇聚签名
    public function hjHmac($arr,$str){
        $newarr = '';
        foreach ($arr as $k=>$v){
            $newarr =$newarr.$v;
        }
        return md5($newarr.$str);
    }
    public function hjpost($data){
        //简单的curl
        $ch = curl_init("https://www.joinpay.com/trade/uniPayApi.action");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
