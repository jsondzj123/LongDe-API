<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        $order = self::select('ld_order.*','ld_student.phone','ld_student.real_name')
            ->leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
            ->where(function($query) use ($data) {
                if($data['school_id'] != ''){
                    $query->where('ld_student.school_id',$data['school_id']);
                }
                if($data['status'] != ''){
                    $query->where('ld_order.status',$data['status']);
                }
                if($data['order_number'] != ''){
                    $query->where('ld_order.order_number',$data['order_number']);
                }
                if($data['state_time'] != ''){
                    $query->where('ld_order.create_at',$data['state_time']);
                }
                if($data['end_time'] != ''){
                    $query->where('ld_order.create_at',$data['end_time']);
                }
            })
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
            $data['student_id'] = $arr['user_id'];
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
                }else{
                    $return = app('ali')->createAppPay($data['order_number'],'商品简介',$data['price']);
                }
                return ['code' => 200 , 'msg' => '生成预订单成功','data'=>$return];
            }else{
                return ['code' => 202 , 'msg' => '生成预订单失败'];
            }
        } catch (Exception $ex) {
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
        $list = self::select('ld_order.*','ld_student.real_name','ld_student.phone','ld_school.name')
            ->leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
            ->leftJoin('ld_school','ld_school.id','=','ld_student.school_id')
            ->leftJoin()
            ->where(['ld_order.id'=>$data['order_id']])
            ->field();
        if($list){
            return ['code' => 200 , 'msg' => '查询成功','data'=>$list];
        }else{
            return ['code' => 201 , 'msg' => '查询失败'];
        }
    }
}
