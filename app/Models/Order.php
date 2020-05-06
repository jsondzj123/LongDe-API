<?php
namespace App\Models;

use App\Providers\aop\AopClient\AopClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Order extends Model {
    //指定别的表名
    public $table = 'ld_order';
    //时间戳设置
    public $timestamps = false;

    /*
         * @param  订单列表
         * @param  $school_id  分校id
         * @param  $status  状态
         * @param  $state_time 开始时间
         * @param  $end_time 结束时间
         * @param  $order_sn 订单号
         * @param  author  苏振文
         * @param  ctime   2020/5/4 14:41
         * return  array
         */
    public static function getList($data){
        if(empty($data['num'])){
            $data['num'] = 20;
        }
        if(empty($data['state_time'])){
            $data['state_time'] = "1999-01-01 12:12:12";
        }
        if(empty($data['end_time'])){
            $data['end_time'] = "2999-01-01 12:12:12";
        }
        $order = self::select('ld_order.*','ld_student.phone','ld_student.real_name')
            ->leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
            ->where(function($query) use ($data) {
                if(isset($data['school_id'])){
                    $query->where('ld_student.school_id',$data['school_id']);
                }
                if(isset($data['status'])){
                    $query->where('ld_order.status',$data['status']);
                }
                if(isset($data['order_number'])){
                    $query->where('ld_order.order_number',$data['order_number']);
                }
            })
            ->whereBetween('ld_order.create_at', [$data['state_time'], $data['end_time']])
            ->orderBy('ld_order.id','desc')
            ->paginate($data['num']);
        return $order;
    }
    /*
       * @param  线下学生报名 添加订单
       * @param  $student_id  用户id
       * @param  $lession_id 学科id
       * @param  $lession_price 原价
       * @param  $student_price 现价
       * @param  $payment_type 1定金2尾款3最后一笔尾款4全款
       * @param  $payment_method 1微信2支付宝3银行转账
       * @param  $payment_time 支付时间
       * @param  author  苏振文
       * @param  ctime   2020/5/6 9:57
       * return  array
       */
    public static function offlineStudentSignup($arr){
        //判断传过来的数组数据是否为空
        if(!$arr || !is_array($arr)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        //判断数据是否为空
        if(empty($arr['student_id']) || empty($arr['class_id'])|| empty($arr['lession_price'])|| empty($arr['student_price'])|| empty($arr['payment_type'])|| empty($arr['payment_method'])|| empty($arr['payment_time'])){
            return ['code' => 201 , 'msg' => '数据不能为空'];
        }
        $data['admin_id'] = 1;  //操作员id
        $data['order_number'] = date('YmdHis', time()) . rand(1111, 9999); //订单号  随机生成
        $data['order_type'] = 1;        //1线下支付 2 线上支付
        $data['student_id'] = $arr['student_id'];
        $data['price'] = $arr['student_price'];
        $data['lession_price'] = $arr['lession_price'];
        $data['pay_status'] = $arr['payment_type'];
        $data['pay_type'] = $arr['payment_method'];
        $data['status'] = 1;                  //支付状态
        $data['pay_time'] = $arr['payment_time'];
        $data['oa_status'] = 0;              //OA状态
        $data['class_id'] = $arr['lession_id'];
        $add = self::insert($data);
        if($add){
            return ['code' => 200 , 'msg' => '订单生成成功','data'=>$add];
        }else{
            return ['code' => 203 , 'msg' => '订单生成失败'];
        }
    }
    /*
         * @param  线上支付 生成预订单
         * @param  $student_id  学生id
         * @param  $price  支付金额
         * @param  $lession_price  原价
         * @param  $pay_type  支付方式
         * @param  $class_id  课程id
         * @param  author  苏振文
         * @param  ctime   2020/5/6 14:53
         * return  array
         */
    public static function orderPayList($arr){
        try{
            DB::beginTransaction();
            if(!$arr || empty($arr)){
                return ['code' => 201 , 'msg' => '参数错误'];
            }
            if(empty($arr['student_id']) || empty($arr['price']) || empty($arr['lession_price']) || empty($arr['pay_type']) || empty($arr['class_id'])){
                return ['code' => 202 , 'msg' => '参数不能为空'];
            }
            //数据入库，生成订单
            $data['order_number'] = date('YmdHis', time()) . rand(1111, 9999);
            $data['admin_id'] = 0;  //操作员id
            $data['order_type'] = 2;        //1线下支付 2 线上支付
            $data['student_id'] = $arr['student_id'];
            $data['price'] = $arr['price'];
            $data['lession_price'] = $arr['lession_price'];
            $data['pay_status'] = 4;
            $data['pay_type'] = $arr['pay_type'];
            $data['status'] = 0;
            $data['oa_status'] = 0;              //OA状态
            $data['class_id'] = $arr['class_id'];
            $add = self::insert($data);
            if($add){
                if($arr['pay_type'] == 1){
                    $return = app('wx')->getPrePayOrder($data['order_number'],$data['price']);
                    if($return['code'] == 200){
                        return ['code' => 200 , 'msg' => '生成预订单成功','data'=>$return['list']];
                    }else{
                        throw new Exception($return['list']);
                    }
                }else{
                    $return = app('ali')->createAppPay($data['order_number'],'商品简介',$data['price']);
                    return ['code' => 200 , 'msg' => '生成预订单成功','data'=>$return];
                }
            }else{
                throw new Exception('生成订单失败');
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            return ['code' => 201 , 'msg' => $ex->getMessage()];
        }

    }
    /*
         * @param  修改审核状态
         * @param  $order_id 订单id
         * @param  $status 1审核通过 status修改成2    2退回审核  status修改成4
         * @param  author  苏振文
         * @param  ctime   2020/5/6 10:56
         * return  array
         */
    public static function exitForIdStatus($data){
        if(!$data || !is_array($data)){
            return ['code' => 201 , 'msg' => '数据不合法'];
        }
        $find = self::where(['id'=>$data['order_id']])->first();
        if(!$find){
            return ['code' => 202 , 'msg' => '数据无效'];
        }
        if($data['status'] == 1){
            if($find['status'] == 2){
                return ['code' => 200 , 'msg' => '审核已通过'];
            }else if($find['status'] == 1){
                $update = self::where(['id'=>$data['order_id']])->save(['status'=>2]);
                if($update){
                    //加日志
                    return ['code' => 200 , 'msg' => '审核通过'];
                }else{
                    return ['code' => 203 , 'msg' => '操作失败'];
                }
            }else{
                return ['code' => 204 , 'msg' => '此订单无法进行此操作'];
            }
        }else{
            if($find['status'] == 4){
                return ['code' => 200 , 'msg' => '回审已通过'];
            }else if($find['status'] == 1 || $find['status'] == 2){
                $update = self::where(['id'=>$data['order_id']])->save(['status'=>4]);
                if($update){
                    //加日志
                    return ['code' => 200 , 'msg' => '回审通过'];
                }else{
                    return ['code' => 203 , 'msg' => '操作失败'];
                }
            }else{
                return ['code' => 204 , 'msg' => '此订单无法进行此操作'];
            }
        }
    }
    /*
         * @param  单条查看详情
         * @param  order_id   订单id
         * @param  author  苏振文
         * @param  ctime   2020/5/6 11:15
         * return  array
         */
    public static function findOrderForId($data){
        $list = self::select('ld_order.*','ld_student.real_name','ld_student.phone','ld_school.name','lessons.title')
            ->leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
            ->leftJoin('ld_school','ld_school.id','=','ld_student.school_id')
            ->leftJoin('lessons','lessons.id','=','ld_order.class_id')
            ->where(['ld_order.id'=>$data['order_id']])
            ->field();
        if($list){
            return ['code' => 200 , 'msg' => '查询成功','data'=>$list];
        }else{
            return ['code' => 201 , 'msg' => '查询失败'];
        }
    }
    /*
         * @param  订单修改oa状态
         * @param  $order_id
         * @param  $status
         * @param  author  苏振文
         * @param  ctime   2020/5/6 16:33
         * return  array
         */
    public static function orderUpOaForId($data){
        if(!$data || empty($data)){
            return ['code' => 201 , 'msg' => '参数错误'];
        }
        $up = self::where(['id'=>$data['order_id']])->save(['oa_status'=>$data['status']]);
        if($up){
            return ['code' => 200 , 'msg' => '修改成功'];
        }else{
            return ['code' => 202 , 'msg' => '修改失败'];
        }
    }
    /*
         * @param  微信支付 订单回调，逻辑处理  修改订单状态  添加课程有效期
         * @param  author  苏振文
         * @param  ctime   2020/5/6 17:09
         * return  array
         */
    public static function wxnotify_url($xml){
        if(!$xml) {
            return ['code' => 201 , 'msg' => '参数错误'];
        }
        $data =  self::xmlToArray($xml);
        Storage ::disk('logs')->append('wxpaynotify.txt', 'time:'.date('Y-m-d H:i:s')."\nresponse:".$data);
        if($data && $data['result_code']=='SUCCESS') {
                $where = array(
                    'order_number'=>$data['attach'],
                );
                $orderinfo = self::where($where)->first();
                if (!$orderinfo) {
                    return ['code' => 202 , 'msg' => '订单不存在'];
                }
                //完成支付
                if ($orderinfo->status > 0 ) {
                    return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                }
            try{
                DB::beginTransaction();
                //修改订单状态
                $arr = array(
                    'third_party_number'=>$data['transaction_id'],
                    'status'=>1,
                    'pay_time'=>date('Y-m-d H:i:s'),
                    'update_at'=>date('Y-m-d H:i:s')
                );
                $res = self::where($where)->update($arr);
                if (!$res) {
                    throw new Exception('回调失败');
                }
                return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
                return "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[error]]></return_msg></xml>";
            }
        } else {
            return "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[error]]></return_msg></xml>";
        }

    }

    /*
         * @param  支付宝支付 订单回调 逻辑处理
         * @param  author  苏振文
         * @param  ctime   2020/5/6 17:50
         * return  array
         */
    public static function alinotify_url($arr){
        require_once './App/Providers/Ali/aop/AopClient.php';
        require_once('./App/Providers/Ali/aop/request/AlipayTradeAppPayRequest.php');
        $aop = new AopClient();
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAh8I+MABQoa5Lr0hnb9+UeAgHCtZlwJ84+c18Kh/JWO+CAbKqGkmZ6GxrWo2X/vnY2Qf6172drEThHwafNrUqdl/zMMpg16IlwZqDeQuCgSM/4b/0909K+RRtUq48/vRM6denyhvR44fs+d4jZ+4a0v0m0Kk5maMCv2/duWejrEkU7+BG1V+YXKOb0++n8We/ZIrG/OiiXedViwSW3il9/Q5xa21KlcDPjykWyoPolR2MIFqu8PLh2z8uufCPSlFuABMyL+djo8y9RMzTWH+jN2WxcqMSDMIcwGFk3emZKzoy06a5k4Ea8/l3uHq8sbbepvpmC/dZZ0+CZdXgPnVRywIDAQAB';
        $flag = $aop->rsaCheckV1($arr, NULL, "RSA2");
        Storage ::disk('logs')->append('alipaynotify.txt', 'time:'.date('Y-m-d H:i:s')."\nresponse:".$arr);
        if($arr['trade_status'] == 'TRADE_SUCCESS' ){
            $orders = self::where(['order_number'=>$arr['out_trade_no']])->first();
            if ($orders['status'] > 0) {
                //已经支付完成
                return 'success';
            }else {
                try{
                    DB::beginTransaction();
                    //修改订单状态
                    $arr = array(
                        'third_party_number'=>$arr['transaction_id'],
                        'status'=>1,
                        'pay_time'=>date('Y-m-d H:i:s'),
                        'update_at'=>date('Y-m-d H:i:s')
                    );
                    $res = self::where(['order_number'=>$arr['out_trade_no']])->update($arr);
                    if (!$res) {
                        throw new Exception('回调失败');
                    }
                    DB::commit();
                } catch (Exception $ex) {
                    DB::rollback();
                    return 'fail';
                }
            }
        }else{
            return 'fail';
        }
    }
    /*
        * xml转换数组
        */
    public static function xmlToArray($xml) {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
}
