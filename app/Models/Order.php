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
         * @param  $order_number 订单号
         * @param  author  苏振文
         * @param  ctime   2020/5/4 14:41
         * return  array
         */
    public static function getList($data){
        unset($data['/admin/order/orderList']);
        //用户权限
        $role_id = isset(AdminLog::getAdminInfo()->admin_user->role_id) ? AdminLog::getAdminInfo()->admin_user->role_id : 0;
        //如果不是总校管理员，只能查询当前关联的网校订单
        if($role_id != 1){
            $school_id = isset(AdminLog::getAdminInfo()->admin_user->school_id) ? AdminLog::getAdminInfo()->admin_user->school_id : 0;
            $data['school_id'] = $school_id;
        }
        $begindata=date('Y-m-01', strtotime(date("Y-m-d")));
        $enddate = date('Y-m-d',strtotime("$begindata +1 month -1 day"));
        $statetime = !empty($data['state_time'])?$data['state_time']:$begindata;
        $endtime = !empty($data['end_time'])?$data['end_time']:$enddate;
        $state_time = $statetime." 00:00:00";
        $end_time = $endtime." 23:59:59";
        //每页显示的条数
        $pagesize = (int)isset($data['pageSize']) && $data['pageSize'] > 0 ? $data['pageSize'] : 20;
        $page     = isset($data['page']) && $data['page'] > 0 ? $data['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        //計算總數
        $count = self::leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
            ->where(function($query) use ($data) {
                if(isset($data['school_id']) && !empty($data['school_id'])){
                    $query->where('ld_order.school_id',$data['school_id']);
                }
                if(isset($data['status'])&& !empty($data['status'])){
                    $query->where('ld_order.status',$data['status']);
                }
                if(isset($data['order_number'])&& !empty($data['order_number'])){
                    $query->where('ld_order.order_number',$data['order_number']);
                }
            })
            ->whereBetween('ld_order.create_at', [$state_time, $end_time])
            ->count();
        $order = self::select('ld_order.id','ld_order.order_number','ld_order.order_type','ld_order.price','ld_order.pay_status','ld_order.pay_type','ld_order.status','ld_order.create_at','ld_order.oa_status','ld_order.student_id','ld_student.phone','ld_student.real_name')
            ->leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
            ->where(function($query) use ($data) {
                if(isset($data['school_id']) && !empty($data['school_id'])){
                    $query->where('ld_order.school_id',$data['school_id']);
                }
                if(isset($data['status'])&& is_numeric($data['status'])){
                    $query->where('ld_order.status',$data['status']);
                }
                if(isset($data['order_number'])&& !empty($data['order_number'])){
                    $query->where('ld_order.order_number',$data['order_number']);
                }
            })
            ->whereBetween('ld_order.create_at', [$state_time, $end_time])
            ->orderByDesc('ld_order.id')
            ->offset($offset)->limit($pagesize)->get();
        $schooltype = Article::schoolANDtype($role_id);
        $page=[
            'pageSize'=>$pagesize,
            'page' =>$page,
            'total'=>$count
        ];
        return ['code' => 200 , 'msg' => '查询成功','data'=>$order,'school'=>$schooltype[0],'where'=>$data,'page'=>$page];
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
            return ['code' => 201 , 'msg' => '传递数据不合法'];
        }
        //判断学生id
        if(!isset($arr['student_id']) || empty($arr['student_id'])){
            return ['code' => 201 , 'msg' => '报名学生为空或格式不对'];
        }
        //判断学科id
        if(!isset($arr['lession_id']) || empty($arr['lession_id'])){
            return ['code' => 201 , 'msg' => '学科为空或格式不对'];
        }
        //判断原价
        if(!isset($arr['lession_price']) || empty($arr['lession_price'])){
            return ['code' => 201 , 'msg' => '原价为空或格式不对'];
        }
        //判断付款类型
        if(!isset($arr['payment_type']) || empty($arr['payment_type']) || !in_array($arr['payment_type'],[1,2,3,4])){
            return ['code' => 201 , 'msg' => '付款类型为空或格式不对'];
        }
        //判断原价
        if(!isset($arr['payment_method']) || empty($arr['payment_method'])|| !in_array($arr['payment_method'],[1,2,3])){
            return ['code' => 201 , 'msg' => '付款方式为空或格式不对'];
        }
        //判断支付时间
        if(!isset($arr['payment_time'])|| empty($arr['payment_time'])){
            return ['code' => 201 , 'msg' => '支付时间不能为空'];
        }
        //获取后端的操作员id
        $data['admin_id'] = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;  //操作员id
        //根据用户id获得分校id
        $school = Student::select('school_id')->where('id',$arr['student_id'])->first();
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
        $data['school_id'] = $school['school_id'];
        $add = self::insert($data);
        if($add){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $data['admin_id']  ,
                'module_name'    =>  'Order' ,
                'route_url'      =>  'admin/Order/offlineStudentSignup' ,
                'operate_method' =>  'insert' ,
                'content'        =>  '添加订单的内容,'.json_encode($data),
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return true;
        }else{
            return false;
        }
    }
    /*
         * @param  线上支付 生成预订单
         * @param  $student_id  学生id
         * @param  type  1安卓2ios3h5
         * @param  $class_id  课程id
         * @param  author  苏振文
         * @param  ctime   2020/5/6 14:53
         * return  array
         */
    public static function orderPayList($arr){
            DB::beginTransaction();
            if(!$arr || empty($arr)){
                return ['code' => 201 , 'msg' => '参数错误'];
            }
            //判断学生id
            if(!isset($arr['student_id']) || empty($arr['student_id'])){
                return ['code' => 201 , 'msg' => '学生id为空或格式不对'];
            }
            //根据用户id查询信息
            $student = Student::select('school_id','balance')->where('id','=',$arr['student_id'])->first();
            //判断课程id
            if(!isset($arr['class_id']) || empty($arr['class_id'])){
                return ['code' => 201 , 'msg' => '课程id为空或格式不对'];
            }
            //判断类型
            if(!isset($arr['type']) || empty($arr['type'] || !in_array($arr['type'],[1,2,3]))){
                return ['code' => 201 , 'msg' => '机型不匹配'];
            }
            //判断用户网校，根据网校查询课程信息
           if($student['school_id'] == 1){
               //根据课程id 查询价格
               $lesson = Lesson::select('id','title','cover','price','favorable_price')->where(['id'=>$arr['class_id'],'is_del'=>0,'is_forbid'=>0,'status'=>2,'is_public'=>0])->first()->toArray();
               if(!$lesson){
                   return ['code' => 204 , 'msg' => '此课程选择无效'];
               }
           }else{
                //根据课程id 网校id 查询网校课程详情
               $lesson = LessonSchool::select('id','title','cover','price','favorable_price')->where(['lesson_id'=>$arr['class_id'],'school_id'=>$student['school_id'],'is_del'=>0,'is_forbid'=>0,'status'=>1,'is_public'=>0])->first()->toArray();
               if(!$lesson){
                   return ['code' => 204 , 'msg' => '此课程选择无效'];
               }
           }
            //查询用户有此类订单没有，有的话直接返回
            $orderfind = self::where(['student_id'=>$arr['student_id'],'class_id'=>$arr['class_id'],'status'=>0])->first();
            if($orderfind){
                $lesson['order_id'] = $orderfind['id'];
                $lesson['order_number'] = $orderfind['order_number'];
                $lesson['user_balance'] = $student['balance'];
                return ['code' => 200 , 'msg' => '生成预订单成功','data'=>$lesson];
            }
            //数据入库，生成订单
            $data['order_number'] = date('YmdHis', time()) . rand(1111, 9999);
            $data['admin_id'] = 0;  //操作员id
            $data['order_type'] = 2;        //1线下支付 2 线上支付
            $data['student_id'] = $arr['student_id'];
            $data['price'] = $lesson['favorable_price'];
            $data['lession_price'] = $lesson['price'];
            $data['pay_status'] = 4;
            $data['pay_type'] = 0;
            $data['status'] = 0;
            $data['oa_status'] = 0;              //OA状态
            $data['class_id'] = $arr['class_id'];
            $add = self::insertGetId($data);
            if($add){
                $lesson['order_id'] = $add;
                $lesson['order_number'] = $data['order_number'];
                $lesson['user_balance'] = $student['balance'];
                DB::commit();
                //添加支付方式数组
                return ['code' => 200 , 'msg' => '生成预订单成功','data'=>$lesson];
            }else{
                DB::rollback();
                return ['code' => 203 , 'msg' => '生成订单失败'];
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
            return ['code' => 201 , 'msg' => '数据无效'];
        }
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
        if($find['status'] == 1){
            if($data['status'] == 2){
                $update = self::where(['id'=>$data['order_id']])->update(['status'=>2]);
                if($update){
                    //添加日志操作
                    AdminLog::insertAdminLog([
                        'admin_id'       =>   $admin_id  ,
                        'module_name'    =>  'Order' ,
                        'route_url'      =>  'admin/Order/exitForIdStatus' ,
                        'operate_method' =>  'update' ,
                        'content'        =>  '审核通过，修改id为'.$data['order_id'].json_encode($data) ,
                        'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                        'create_at'      =>  date('Y-m-d H:i:s')
                    ]);
                    return ['code' => 200 , 'msg' => '审核通过'];
                }else{
                    return ['code' => 202 , 'msg' => '操作失败'];
                }
           }else if($data['status'] == 4){
                $update = self::where(['id'=>$data['order_id']])->update(['status'=>4]);
                if($update){
                    //添加日志操作
                    AdminLog::insertAdminLog([
                        'admin_id'       =>   $admin_id  ,
                        'module_name'    =>  'Order' ,
                        'route_url'      =>  'admin/Order/exitForIdStatus' ,
                        'operate_method' =>  'update' ,
                        'content'        =>  '退回审核，修改id为'.$data['order_id'].json_encode($data) ,
                        'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                        'create_at'      =>  date('Y-m-d H:i:s')
                    ]);
                    return ['code' => 200 , 'msg' => '回审通过'];
                }else{
                    return ['code' => 202 , 'msg' => '操作失败'];
                }
            }
        }else{
            return ['code' => 203 , 'msg' => '此订单无法进行此操作'];
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
        if(empty($data['order_id'])){
            return ['code' => 201 , 'msg' => '订单id错误'];
        }
//        $list = self::select('ld_order.order_number','ld_order.create_at','ld_order.price','ld_order.order_type','ld_order.status','ld_order.pay_time','ld_student.real_name','ld_student.phone','ld_school.name','lessons.title','lessons.price as lessprice','lesson_teachers.real_name')
//            ->leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
//            ->leftJoin('ld_school','ld_school.id','=','ld_student.school_id')
//            ->leftJoin('ld_lessons','ld_lessons.id','=','ld_order.class_id')
//            ->leftJoin('ld_lesson_teachers','ld_lesson_teachers.lesson_id','=','ld_lessons.id')
//            ->leftJoin('ld_lecturer_educationa','ld_lecturer_educationa.id','=','ld_lesson_teachers.teacher_id')
//            ->where(['ld_order.id'=>$data['order_id']])
//            ->first();
        $list = self::select('ld_order.order_number','ld_order.create_at','ld_order.price','ld_order.order_type','ld_order.status','ld_order.pay_time','ld_student.real_name','ld_student.phone','ld_school.name','ld_lessons.title','ld_lecturer_educationa.real_name')
            ->leftJoin('ld_student','ld_student.id','=','ld_order.student_id')
            ->leftJoin('ld_school','ld_school.id','=','ld_student.school_id')
            ->leftJoin('ld_lessons','ld_lessons.id','=','ld_order.class_id')
            ->leftJoin('ld_lesson_teachers','ld_lesson_teachers.lesson_id','=','ld_lessons.id')
            ->leftJoin('ld_lecturer_educationa','ld_lecturer_educationa.id','=','ld_lesson_teachers.teacher_id')
            ->where(['ld_order.id'=>$data['order_id']])
            ->first();
        if($list){
            return ['code' => 200 , 'msg' => '查询成功','data'=>$list];
        }else{
            return ['code' => 202 , 'msg' => '查询失败'];
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
            return ['code' => 201 , 'msg' => '参数为空或格式错误'];
        }
        if(empty($data['order_id'])){
            return ['code' => 201 , 'msg' => '订单id错误'];
        }
        if(!in_array($data['status'],['0,1'])){
            return ['code' => 201 , 'msg' => '状态传输错误'];
        }
        $up = self::where(['id'=>$data['order_id'],'order_type'=>1])->update(['oa_status'=>$data['status'],'update_at'=>date('Y-m-d H:i:s')]);
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
         * @param  author  苏振文
         * @param  ctime   2020/5/11 15:15
         * return  array
         */
    public static function pcpay($price){
        $arr = app('wx')->getPcPayOrder('202005111519301234',$price);
        return $arr;
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
