<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonSchool;
use App\Models\Order;
use App\Models\Student;
use App\Models\Student_price;
use App\Models\Student_pricelog;


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
        $data = self::$accept_data;
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
    public function orderPay(){
        $data = self::$accept_data;
        //获取用户信息
        $user_id = 1;
        $user_balance = 1.00;
        $user_school_id = 1;
        //判断支付类型
        if(empty($data['type']) || !isset($data['type']) || !in_array($data['type'],[1,2])){
            return ['code' => 201 , 'msg' => '请选择类型'];
        }
        //判断支付方式
        if(empty($data['pay_type']) || !isset($data['pay_type']) || !in_array($data['pay_type'],[1,2,3,4,5])){
            return ['code' => 201 , 'msg' => '请选择支付方式'];
        }
        if($data['type'] == 1){
            //判断订单id
            if(empty($data['order_id']) || !isset($data['order_id'])){
                return ['code' => 201 , 'msg' => '请选择订单'];
            }
            //获取订单信息
            $order = Order::where(['id'=>$data['order_id'],'student_id'=>$user_id])->first()->toArray();
            if($order['status'] > 0){
                return ['code' => 202 , 'msg' => '此订单已支付'];
            }
            //判断用户网校，根据网校查询课程信息
            if($user_school_id == 1){
                //根据课程id 查询价格
                $lesson = Lesson::select('id','title','cover','price','favorable_price')->where(['id'=>$order['class_id'],'is_del'=>0,'is_forbid'=>0,'status'=>2,'is_public'=>0])->first()->toArray();
                if(!$lesson){
                    return ['code' => 204 , 'msg' => '此课程选择无效'];
                }
            }else{
                //根据课程id 网校id 查询网校课程详情
                $lesson = LessonSchool::select('id','title','cover','price','favorable_price')->where(['lesson_id'=>$order['class_id'],'school_id'=>$user_school_id,'is_del'=>0,'is_forbid'=>0,'status'=>1,'is_public'=>0])->first()->toArray();
                if(!$lesson){
                    return ['code' => 204 , 'msg' => '此课程选择无效'];
                }
            }
            if($data['pay_type'] == 5){
                if($lesson['favorable_price'] > $user_balance){
                    return ['code' => 202 , 'msg' => '余额不足，请充值！！！！！'];
                }else{
                    //扣除用户余额 修改订单信息 加入用户消费记录日志  商品增加购买基数  用户关联的课程加上起始时间
                    $end_balance = $user_balance - $lesson['favorable_price'];
                    Student::where(['id'=>$user_id])->update(['balance'=>$end_balance]);
                    Order::where(['id'=>$data['order_id']])->update(['pay_type'=>5,'status'=>1,'pay_time'=>date('Y-m-d H:i:s'),'update_at'=>date('Y-m-d H:i:s')]);
                    Student_pricelog::insert(['user_id'=>$user_id,'price'=>$lesson['favorable_price'],'end_price'=>$end_balance,'status'=>2,'class_id'=>$order['class_id']]);
                    if($user_school_id == 1){
                        Lesson::where(['id'=>$lesson['id']])->update(['buy_num'=>$lesson['buy_num']+1]);
                    }else{
                        LessonSchool::where(['id'=>$lesson['id']])->update(['buy_num'=>$lesson['buy_num']+1]);
                    }
                    return response()->json(['code' => 200 , 'msg' => '购买成功']);
                }
            }else{
                $sutdent_price=[
                    'user_id'=>$user_id,
                    'order_sn'=>$order['order_number'],
                    'price'=>$lesson['favorable_price'],
                    'pay_type'=>$data['pay_type'],
                    'order_type'=>2,
                    'status'=>0
                ];
                Student_price::insert($sutdent_price);
                $return = $this->payStatus($order['order_number'],$data['pay_type'],$lesson['favorable_price']);
                return response()->json(['code' => 200 , 'msg' => '生成预订单成功','data'=>$return]);
            }
        }else{
            $sutdent_price=[
                'user_id'=>$user_id,
                'order_sn'=>date('YmdHis', time()) . rand(1111, 9999),
                'price'=>$data['price'],
                'pay_type'=>$data['pay_type'],
                'order_type'=>1,
                'status'=>0
            ];
            $add = Student_price::insert($sutdent_price);
            if($add){
                $return = self::payStatus($sutdent_price['order_sn'],$data['type'],$data['price']);
                return response()->json(['code' => 200 , 'msg' => '生成预订单成功','data'=>$return]);
            }
        }
    }
    //调用公共方法
    //$type  1微信2支付宝3汇聚微信4汇聚支付宝
    public function payStatus($order_number,$type,$price){
        if($type == 1){
            //根据分校查询对应信息
            return $return = app('wx')->getPrePayOrder($order_number,$price);
        }
        if($type == 2){
            //根据分校查询对应信息
            return $return = app('ali')->createAppPay($order_number,'龙德产品',0.01);
        }
        if($type == 3){
            //根据分校查询对应信息
        }
        if($type == 4){
            //根据分校查询对应信息
        }
    }
    //iphone 内部支付
    public function iphonePay(){
        echo "123132";
    }
}
