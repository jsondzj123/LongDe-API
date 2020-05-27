<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonSchool;
use App\Models\Order;
use App\Models\Student;
use App\Models\Student_price;
use App\Models\Student_pricelog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{

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
        $user_school_id = 1;
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
                    Student_pricelog::insert(['user_id' => $user_id, 'price' => $lesson['favorable_price'], 'end_price' => $end_balance, 'status' => 2, 'class_id' => $order['class_id']]);
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
                Student_price::insert($sutdent_price);
                $return = $this->payStatus($order['order_number'], $data['pay_type'], $lesson['favorable_price'],$user_school_id);
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
            $add = Student_price::insert($sutdent_price);
            if ($add) {
                $return = self::payStatus($sutdent_price['order_number'], $data['type'], $data['price'],$user_school_id);
                return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $return]);
            }
        }
    }
    //调用公共方法
    //$type  1微信2支付宝3汇聚微信4汇聚支付宝
    public function payStatus($order_number, $type, $price,$school_id){
        if ($type == 1) {
            //根据分校查询对应信息
            return $return = app('wx')->getPrePayOrder($order_number, $price);
        }
        if ($type == 2) {
            //根据分校查询对应信息
            $return = app('ali')->createAppPay($order_number, '龙德产品', 0.01);
            $alipay = [
                'alipay' => $return
            ];
            return $alipay;
        }
        if ($type == 3) {
            //根据分校查询对应信息
            $arr=[
                'p0_Version'=>'1.0',
                'p1_MerchantNo'=>'888108900009969',
                'p2_OrderNo'=>$order_number,
                'p3_Amount'=>$price,
                'p4_Cur'=>1,
                'p5_ProductName'=>"龙德产品",
                'p9_NotifyUrl'=>"http://".$_SERVER['HTTP_HOST']."/Api/order/hjWxnotify",
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
        }
        if ($type == 4) {
            //根据分校查询对应信息
            $arr=[
                'p0_Version'=>'1.0',
                'p1_MerchantNo'=>'888108900009969',
                'p2_OrderNo'=>$order_number,
                'p3_Amount'=>$price,
                'p4_Cur'=>1,
                'p5_ProductName'=>"龙德产品",
                'p9_NotifyUrl'=>"http://".$_SERVER['HTTP_HOST']."/Api/order/hjAlinotify",
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
        Student_price::insert($sutdent_price);
        return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $sutdent_price]);
    }
    // ios轮询查看订单是否成功
    public function iosPolling(){
        $data = self::$accept_data;
        $user_id = $data['user_info']['user_id'];
        $list = Student_price::where(['user_id'=>$user_id,'order_number'=>$data['order_number']])->first()->toArray();
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

    //iphone 内部支付 回调
    public function iphonePaynotify(){
        $data = self::$accept_data;
        $receiptData = $data['receiptData'];
        $order_number = $data['order_number'];
        // 验证参数
        if (strlen($receiptData) < 20) {
            return response()->json(['code' => 201 , 'msg' => '不能读取你提供的JSON对象']);
        }
        // 请求验证【默认向真实环境发请求】
        $html = $this->acurl($receiptData);
        $arr = json_decode($html, true);//接收苹果系统返回数据并转换为数组，以便后续处理
        // 如果是沙盒数据 则验证沙盒模式
        if ($arr['status'] == '21007') {
            // 请求验证  1代表向沙箱环境url发送验证请求
            $html = $this->acurl($receiptData, 1);
            $arr = json_decode($html, true);
        }
        Storage::disk('local')->append('iosnotify.txt', 'time:'.date('Y-m-d H:i:s')."\nresponse:".$html);
        // 判断是否购买成功  【状态码,0为成功（无论是沙箱环境还是正式环境只要数据正确status都会是：0）】
        if (intval($arr['status']) === 0) {
            DB::beginTransaction();
               $studentprice = Student_price::where(['order_number'=>$order_number])->first()->toArray();
               if($studentprice['status'] == 1){
                   return response()->json(['code' => 200 , 'msg' => '支付成功']);
               }
               $codearr=[
                   'tc001'=>6,
                   'tc003'=>18,
                   'tc004'=>68,
                   'tc005'=>168,
                   'tc006'=>388,
                   'tc007'=>698,
                   'tc008'=>1998,
                   'tc009'=>3998,
                   'tc0010'=>6498,
               ];
               if($studentprice['price'] != $codearr[$arr['receipt']['in_app'][0]['product_id']]){
                   return response()->json(['code' => 203 , 'msg' => '充值金额不一致，你充不上，气不气']);
               }
               //修改订单状态  更改用户余额 加入日志
              $student = Student::where(['id'=>$studentprice['user_id']])->first()->toArray();
              $endbalance = $student['balance'] + $studentprice['price'];
              Student::where(['id'=>$studentprice['user_id']])->update(['balance'=>$endbalance]);
              Student_price::where(['order_number'=>$order_number])->update(['content'=>$html,'status'=>1,'update_at'=>date('Y-m-d H:i:s')]);
              Student_pricelog::insert(['user_id'=>$studentprice['user_id'],'price'=>$studentprice['price'],'end_price'=>$endbalance,'status'=>1]);
              DB::commit();
        }else{
            if(in_array('Failed',$arr)){
                Student_price::where(['order_number'=>$order_number])->update(['content'=>$html,'status'=>2,'update_at'=>date('Y-m-d H:i:s')]);
                return response()->json(['code' => 207 , 'msg' =>'支付失败']);
            }
            if(in_array('Deferred',$arr)){
                return response()->json(['code' => 207 , 'msg' =>'等待确认，儿童模式需要询问家长同意']);
            }
            DB::rollBack();
        }
    }
    //curl【模拟http请求】
    public function acurl($receiptData, $sandbox = 0){
        //小票信息
        $POSTFIELDS = array("receipt-data" => $receiptData);
        $POSTFIELDS = json_encode($POSTFIELDS);
        //正式购买地址 沙盒购买地址
        $urlBuy = "https://buy.itunes.apple.com/verifyReceipt";
        $urlSandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
        $url = $sandbox ? $urlSandbox : $urlBuy;//向正式环境url发送请求(默认)
        //简单的curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
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
    public function hjAlinotify(){
        $json = file_get_contents("php://input");
        Storage ::disk('hjAlinotify')->append('hjAlinotify.txt', 'time:'.date('Y-m-d H:i:s')."\nresponse:".$json);
    }
    public function hjWxnotify(){
        $json = file_get_contents("php://input");
        Storage ::disk('hjAlinotify')->append('hjAlinotify.txt', 'time:'.date('Y-m-d H:i:s')."\nresponse:".$json);
    }
    public function wxnotify_url(){
        $data=[
            'sdd'=>1,
            'ssss'=>1
        ];
//        Order::wxnotify_url();
        Storage::disk('local')->append('wxpaynotify.txt', 'time:'.date('Y-m-d H:i:s')."\nresponse:".json_encode($data));
    }
}
