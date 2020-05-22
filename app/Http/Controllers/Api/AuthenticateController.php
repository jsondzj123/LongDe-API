<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Log;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use Lysice\Sms\Facade\SmsFacade;

class AuthenticateController extends Controller {
    /*
     * @param  description   注册方法
     * @param  参数说明       body包含以下参数[
     *     phone             手机号
     *     password          密码
     *     repassword        确认密码
     *     verifycode        验证码
     * ]
     * @param author    dzj
     * @param ctime     2020-05-22
     * return string
     */
    public function doUserRegister() {
        $body = self::$accept_data;
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断手机号是否为空
        if(!isset($body['phone']) || empty($body['phone'])){
            return ['code' => 201 , 'msg' => '请输入手机号'];
        } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
            return ['code' => 202 , 'msg' => '手机号不合法'];
        }
        
        //判断密码是否为空
        if(!isset($body['password']) || empty($body['password'])){
            return ['code' => 201 , 'msg' => '请输入密码'];
        }
        
        //判断确认密码是否为空
        if(!isset($body['repassword']) || empty($body['repassword'])){
            return ['code' => 201 , 'msg' => '请输入确认密码'];
        }
        
        //判断两次输入的密码是否一致
        if($body['password'] != $body['repassword']){
            return ['code' => 202 , 'msg' => '两次密码输入不一致'];
        }
        
        //判断验证码是否为空
        if(!isset($body['verifycode']) || empty($body['verifycode'])){
            return ['code' => 201 , 'msg' => '请输入验证码'];
        }
        
        //验证码合法验证
        $verify_code = Redis::get('user:verifycode:'.$body['phone']);
        if(!$verify_code || empty($verify_code)){
            return ['code' => 201 , 'msg' => '请先获取验证码'];
        }

        //判断验证码是否一致
        if($verify_code != $body['verifycode']){
            return ['code' => 202 , 'msg' => '验证码错误'];
        }

        //key赋值
        $key = 'user:register:'.$body['phone'];
        
        //判断此学员是否被请求过一次(防止重复请求,且数据信息存在)
        if(Redis::get($key)){
            return ['code' => 205 , 'msg' => '此手机号已被注册'];
        } else {
            //判断用户手机号是否注册过
            $student_count = User::where("is_forbid" , 1)->where("phone" , $body['phone'])->count();
            if($student_count > 0){
                //存储学员的手机号值并且保存60s
                Redis::setex($key , 60 , $body['phone']);
                return ['code' => 205 , 'msg' => '此手机号已被注册'];
            }
        }
       
        //开启事务
        DB::beginTransaction();
        
        //将数据插入到表中
        if(false !== User::insertGetId(['phone' => $body['phone'] , 'password' => password_hash($body['password'], PASSWORD_DEFAULT) , 'create_at' => date('Y-m-d H:i:s')])){
            //事务提交
            DB::commit();
            return ['code' => 200 , 'msg' => '注册成功'];
        } else {
            //事务回滚
            DB::rollBack();
            return ['code' => 203 , 'msg' => '注册失败'];
        }
    }
    
    /*
     * @param  description   获取验证码方法
     * @param  参数说明       body包含以下参数[
     *     verify_type     验证码类型(1代表注册,2代表找回密码)
     * ]
     * @param author    dzj
     * @param ctime     2020-05-22
     * return string
     */
    public static function doSendSms(){
        $body = self::$accept_data;
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断验证码类型是否合法
        if(!isset($body['verify_type']) || !in_array($body['verify_type'] , [1,2])){
            return ['code' => 202 , 'msg' => '验证码类型不合法'];
        }
        
        //判断手机号是否为空
        if(!isset($body['phone']) || empty($body['phone'])){
            return ['code' => 201 , 'msg' => '请输入手机号'];
        } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
            return ['code' => 202 , 'msg' => '手机号不合法'];
        }
        
        //判断是注册还是忘记密码
        if($body['verify_type'] == 1){
            //设置key值
            $key = 'user:register:'.$body['phone'];
            //保存时间(5分钟)
            $time= 300;
            //短信模板code码
            $template_code = 'SMS_180053367';
        } else {
            //设置key值
            $key = 'user:forget:'.$body['phone'];
            //保存时间(30分钟)
            $time= 1800;
            //短信模板code码
            $template_code = 'SMS_190727799';
        }
        
        //判断验证码是否过期
        $phone_code = Redis::get($key);
        if($phone_code && !empty($phone_code)){
            return ['code' => 200 , 'msg' => '发送短信成功' , 'data'=>[]];
        }
        
        //随机生成验证码数字,默认为6位数字
        $code = rand(100000,999999);
        
        //发送验证信息流
        $data = ['mobile' => $body['phone'] , 'TemplateParam' => ['code' => $code] , 'template_code' => $template_code];
        $send_data = SmsFacade::send($data);
        
        //判断发送验证码是否成功
        if($send_data->Code == 'OK'){
            //存储学员的id值
            Redis::setex($key , $time , $body['phone']);
            return ['code' => 200 , 'msg' => '发送短信成功'];
        } else {
            return ['code' => 203 , 'msg' => '发送短信失败' , 'data' => $send_data->Message];
        }
    }
}
