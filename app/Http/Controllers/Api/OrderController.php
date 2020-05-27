<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonSchool;
use App\Models\Order;
use App\Models\Student;
use App\Models\Student_price;
use App\Models\Student_pricelog;


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
        print_r($data);die;
//        if($data['user_type'] ==1){
//            return ['code' => 204 , 'msg' => '请先登录'];
//        }
        $data['student_id'] = 1;
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
    public function orderPay()
    {
        $data = self::$accept_data;
        //获取用户信息
        $user_id = 1;
        $user_balance = 1.00;
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
                    'order_sn' => $order['order_number'],
                    'price' => $lesson['favorable_price'],
                    'pay_type' => $data['pay_type'],
                    'order_type' => 2,
                    'status' => 0
                ];
                Student_price::insert($sutdent_price);
                $return = $this->payStatus($order['order_number'], $data['pay_type'], $lesson['favorable_price']);
                return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $return]);
            }
        } else {
            $sutdent_price = [
                'user_id' => $user_id,
                'order_sn' => date('YmdHis', time()) . rand(1111, 9999),
                'price' => $data['price'],
                'pay_type' => $data['pay_type'],
                'order_type' => 1,
                'status' => 0
            ];
            $add = Student_price::insert($sutdent_price);
            if ($add) {
                $return = self::payStatus($sutdent_price['order_sn'], $data['type'], $data['price']);
                return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $return]);
            }
        }
    }
    //调用公共方法
    //$type  1微信2支付宝3汇聚微信4汇聚支付宝
    public function payStatus($order_number, $type, $price)
    {
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
        }
        if ($type == 4) {
            //根据分校查询对应信息
        }
    }




    /*
         * @param  苹果内购 充值余额 生成预订单
         * @param  price 充值金额
         * @param  author  苏振文
         * @param  ctime   2020/5/27 10:31
         * return  array
         */
    public function iphonePayCreateOrder(){
        $user_id = 1;
        $data = self::$accept_data;
        //生成预订单
        $sutdent_price = [
            'user_id' => $user_id,
            'order_sn' => date('YmdHis', time()) . rand(1111, 9999),
            'price' => $data['price'],
            'pay_type' => 5,
            'order_type' => 1,
            'status' => 0
        ];
        Student_price::insert($sutdent_price);
        return response()->json(['code' => 200, 'msg' => '生成预订单成功', 'data' => $sutdent_price]);
    }
    /*
         * @param  ios轮询查看订单是否成功
         * @param  order_number   订单号
         * @param  author  苏振文
         * @param  ctime   2020/5/27 10:54
         * return  array
         */
    public function iosPolling(){
        $user_id = 1;
        $data = self::$accept_data;
        $list = Student_price::where(['user_id'=>$user_id,'order_sn'=>$data['order_number']])->first()->toArray();
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
    public function iphonePay($receiptData, $phone, $payProject){
        // 验证参数
        if (strlen($receiptData) < 1000) {
            return;
        }
        // 请求验证【默认向真实环境发请求】
        $html = $this->acurl($receiptData);
        $data = json_decode($html, true);//接收苹果系统返回数据并转换为数组，以便后续处理
        // 如果是沙盒数据 则验证沙盒模式
        if ($data['status'] == '21007') {
            // 请求验证  1代表向沙箱环境url发送验证请求
            $html = $this->acurl($receiptData, 1);
            $data = json_decode($html, true);
        }
        if (isset($_GET['debug'])) {
            exit(json_encode($data));
        }
        // 判断是否购买成功  【状态码,0为成功（无论是沙箱环境还是正式环境只要数据正确status都会是：0）】
        if (intval($data['status']) === 0) {
            if ($phone != '') {
                $iapData = [
                    'phone' => $phone,
                    'original_purchase_date_pst' => $data['receipt']['original_purchase_date_pst'],//购买时间,太平洋标准时间
                    'purchase_date_ms' => $data['receipt']['purchase_date_ms'],//购买时间毫秒
                    'unique_identifier' => $data['receipt']['unique_identifier'],//唯一标识符
                    'original_transaction_id' => $data['receipt']['original_transaction_id'],//原始交易ID
                    'bvrs' => $data['receipt']['bvrs'],//iPhone程序的版本号
                    'transaction_id' => $data['receipt']['transaction_id'],//交易的标识
                    'quantity' => $data['receipt']['quantity'],//购买商品的数量
                    'unique_vendor_identifier' => $data['receipt']['unique_vendor_identifier'],//开发商交易ID
                    'item_id' => $data['receipt']['item_id'],//App Store用来标识程序的字符串
                    'version_external_identifier' => $data['receipt']['version_external_identifier'],//版本外部的标识，沙箱环境下其值为：0正式环境其值为一个数字，会变，原因未知。是否和修改价格有关？
                    'bid' => $data['receipt']['bid'],//iPhone程序的bundle标识
                    'is_in_intro_offer_period' => $data['receipt']['is_in_intro_offer_period'],//正式环境返回数据中能未找到?考虑删除，目前其值都是false
                    'product_id' => $data['receipt']['product_id'],//商品的标识
                    'purchase_date' => $data['receipt']['purchase_date'],//购买时间
                    'is_trial_period' => $data['receipt']['is_trial_period'],//?沙箱环境中在in_app中找到？正式环境中找得到吗？考虑删除，目前其值都是false
                    'purchase_date_pst' => $data['receipt']['purchase_date_pst'],//太平洋标准时间
                    'original_purchase_date' => $data['receipt']['original_purchase_date'],//原始购买时间
                    'original_purchase_date_ms' => $data['receipt']['original_purchase_date_ms'],//毫秒
                    'status' => $data['status'],
                    'timestamp' => date("Y-m-d H:i:s"),//北京时间（用户真实购买的时间）
                ];
                //插入iap订单表【将苹果返回的所有输数据都插入到数据库中，你可更具需要取舍，这里为了说明方便】
                $this->insert($iapData);
                $user = new User;
                //修改user表中的付款状态【2019-07-02】
                $user->where('phone', $phone)->update(['pay_project' => $payProject, 'create_time' => date('Y-m-d H:i:s', time())]);
                //返回到APP的数据
                $result = array(
                    'status' => 'true',
                    'errorCode' => '购买成功',
                    'pay_project' => $payProject
                );
//                Log::record($result, 'toAPP');// 记录到日志
                return $result;
            } else {
                throw new ParamErrorException(['errorCode' => '购买失败,status:' . $data['status'] . ',未填写游戏账户']);
            }
        } else {
            throw new ParamErrorException(['errorCode' => 'receipt参数有误']);
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
}
