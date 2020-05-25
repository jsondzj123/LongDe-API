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
     *     phone             手机号(必传)
     *     password          密码(必传)
     *     device            设备唯一标识(非必传)
     *     verifycode        验证码(必传)
     * ]
     * @param author    dzj
     * @param ctime     2020-05-22
     * return string
     */
    public function doUserRegister() {
        try {
            $body = self::$accept_data;
            //判断传过来的数组数据是否为空
            if(!$body || !is_array($body)){
                return response()->json(['code' => 202 , 'msg' => '传递数据不合法']);
            }

            //判断手机号是否为空
            if(!isset($body['phone']) || empty($body['phone'])){
                return response()->json(['code' => 201 , 'msg' => '请输入手机号']);
            } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
                return response()->json(['code' => 202 , 'msg' => '手机号不合法']);
            }

            //判断密码是否为空
            if(!isset($body['password']) || empty($body['password'])){
                return response()->json(['code' => 201 , 'msg' => '请输入密码']);
            }

            //判断验证码是否为空
            if(!isset($body['verifycode']) || empty($body['verifycode'])){
                return response()->json(['code' => 201 , 'msg' => '请输入验证码']);
            }

            //验证码合法验证
            $verify_code = Redis::get('user:register:'.$body['phone']);
            if(!$verify_code || empty($verify_code)){
                return ['code' => 201 , 'msg' => '请先获取验证码'];
            }

            //判断验证码是否一致
            if($verify_code != $body['verifycode']){
                return ['code' => 202 , 'msg' => '验证码错误'];
            }

            //key赋值
            $key = 'user:isregister:'.$body['phone'];

            //判断此学员是否被请求过一次(防止重复请求,且数据信息存在)
            if(Redis::get($key)){
                return response()->json(['code' => 205 , 'msg' => '此手机号已被注册']);
            } else {
                //判断用户手机号是否注册过
                $student_count = User::where("phone" , $body['phone'])->count();
                if($student_count > 0){
                    //存储学员的手机号值并且保存60s
                    Redis::setex($key , 60 , $body['phone']);
                    return response()->json(['code' => 205 , 'msg' => '此手机号已被注册']);
                }
            }
            
            //生成随机唯一的token
            $token = sha1(uniqid().$body['phone'].$body['password'].time().rand(1000,9999));

            //开启事务
            DB::beginTransaction();

            //封装成数组
            $user_data = [
                'phone'     =>    $body['phone'] ,
                'password'  =>    md5($body['password']) ,
                'token'     =>    $token ,
                'device'    =>    isset($body['device']) && !empty($body['device']) ? $body['device'] : '' ,
                'reg_source'=>    1 ,
                'create_at' =>    date('Y-m-d H:i:s')
            ];

            //将数据插入到表中
            $user_id = User::insertGetId($user_data);
            if($user_id && $user_id > 0){
                //redis存储信息
                Redis::set("user:regtoken:".$token , json_encode($user_data));
                
                //事务提交
                DB::commit();
                $user_info = ['user_id' => $user_id , 'user_token' => $token , 'user_type' => 0  , 'head_icon' => '' , 'real_name' => '' , 'phone' => $body['phone'] , 'nickname' => '' , 'sign' => '' , 'papers_type' => '' , 'papers_name' => '' , 'papers_num' => ''];
                return response()->json(['code' => 200 , 'msg' => '注册成功' , 'data' => ['user_info' => $user_info]]);
            } else {
                //事务回滚
                DB::rollBack();
                return response()->json(['code' => 203 , 'msg' => '注册失败']);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   登录方法
     * @param  参数说明       body包含以下参数[
     *     phone             手机号
     *     password          密码
     * ]
     * @param author    dzj
     * @param ctime     2020-05-23
     * return string
     */
    public static function doUserLogin() {
        try {
            $body = self::$accept_data;
            //判断传过来的数组数据是否为空
            if(!$body || !is_array($body)){
                return response()->json(['code' => 202 , 'msg' => '传递数据不合法']);
            }

            //判断手机号是否为空
            if(!isset($body['phone']) || empty($body['phone'])){
                return response()->json(['code' => 201 , 'msg' => '请输入手机号']);
            } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
                return response()->json(['code' => 202 , 'msg' => '手机号不合法']);
            }

            //判断密码是否为空
            if(!isset($body['password']) || empty($body['password'])){
                return response()->json(['code' => 201 , 'msg' => '请输入密码']);
            }

            //key赋值
            $key = 'user:login:'.$body['phone'];

            //判断此学员是否被请求过一次(防止重复请求,且数据信息存在)
            if(Redis::get($key)){
                return response()->json(['code' => 204 , 'msg' => '此手机号未注册']);
            } else {
                //判断用户手机号是否注册过
                $student_count = User::where("phone" , $body['phone'])->count();
                if($student_count <= 0){
                    //存储学员的手机号值并且保存60s
                    Redis::setex($key , 60 , $body['phone']);
                    return response()->json(['code' => 204 , 'msg' => '此手机号未注册']);
                }
            }
            
            //生成随机唯一的token
            $token = sha1(uniqid().$body['phone'].$body['password'].time().rand(1000,9999));
            
            //开启事务
            DB::beginTransaction();

            //根据手机号和密码进行登录验证
            $user_login = User::where("phone",$body['phone'])->where("password",md5($body['password']))->first();
            if($user_login && !empty($user_login)){
                //清除老的redis的key值
                Redis::del("user:regtoken:".$user_login->token);
                
                //用户详细信息赋值
                $user_info = [
                    'user_id'    => $user_login->id ,
                    'user_token' => $token , 
                    'user_type'  => 0 ,
                    'head_icon'  => $user_login->head_icon , 
                    'real_name'  => $user_login->real_name , 
                    'phone'      => $user_login->phone , 
                    'nickname'   => $user_login->nickname , 
                    'sign'       => $user_login->sign , 
                    'papers_type'=> $user_login->papers_type , 
                    'papers_name'=> $user_login->papers_type > 0 ? parent::getPapersNameByType($user_login->papers_type) : '',
                    'papers_num' => $user_login->papers_num
                ];
                
                //redis存储信息
                Redis::set("user:regtoken:".$token , json_encode($user_info));
                
                //更新token
                $rs = User::where("phone" , $body['phone'])->update(["token" => $token , "update_at" => date('Y-m-d H:i:s')]);
                if($rs && !empty($rs)){
                    //事务提交
                    DB::commit();
                } else {
                    //事务回滚
                    DB::rollBack();
                }
                return response()->json(['code' => 200 , 'msg' => '登录成功' , 'data' => ['user_info' => $user_info]]);
            } else {
                return response()->json(['code' => 203 , 'msg' => '手机号或密码错误']);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   游客登录方法
     * @param  参数说明       body包含以下参数[
     *     device            设备唯一标识(必传)
     * ]
     * @param author    dzj
     * @param ctime     2020-05-23
     * return string
     */
    public static function doVisitorLogin() {
        try {
            $body = self::$accept_data;
            //判断传过来的数组数据是否为空
            if(!$body || !is_array($body)){
                return response()->json(['code' => 202 , 'msg' => '传递数据不合法']);
            }

            //判断设备唯一标识是否为空
            if(!isset($body['device']) || empty($body['device'])){
                return response()->json(['code' => 201 , 'msg' => '设备唯一标识为空']);
            }
            
            //生成随机唯一的token
            $token = sha1(uniqid().$body['device'].time().rand(1000,9999));
            
            //通过设备唯一标识判断是否注册过
            $student_info = User::where("device" , $body['device'])->first();
            
            //开启事务
            DB::beginTransaction();
            
            //判断是否是存在用户信息
            if($student_info && !empty($student_info)){
                //清除老的redis的key值
                Redis::del("user:regtoken:".$student_info->token);
                
                //用户详细信息赋值
                $user_info = [
                    'user_id'    => $student_info->id ,
                    'user_token' => $token , 
                    'user_type'  => 0 ,
                    'head_icon'  => $student_info->head_icon , 
                    'real_name'  => $student_info->real_name , 
                    'phone'      => $student_info->phone , 
                    'nickname'   => $student_info->nickname , 
                    'sign'       => $student_info->sign , 
                    'papers_type'=> $student_info->papers_type , 
                    'papers_name'=> $student_info->papers_type > 0 ? parent::getPapersNameByType($student_info->papers_type) : '',
                    'papers_num' => $student_info->papers_num
                ];
                
                //redis存储信息
                Redis::set("user:regtoken:".$token , json_encode($user_info));
                
                //更新token
                $rs = User::where("device" , $body['device'])->update(["token" => $token , "update_at" => date('Y-m-d H:i:s')]);
                if($rs && !empty($rs)){
                    //事务提交
                    DB::commit();
                    return response()->json(['code' => 200 , 'msg' => '登录成功' , 'data' => ['user_info' => $user_info]]);
                } else {
                    //事务回滚
                    DB::rollBack();
                    return response()->json(['code' => 203 , 'msg' => '登录失败']);
                }
            } else {
                //封装成数组
                $user_data = [
                    'token'     =>    $token ,
                    'device'    =>    isset($body['device']) && !empty($body['device']) ? $body['device'] : '' ,
                    'reg_source'=>    1 ,
                    'nickname'  =>    '游客'.randstr(8) ,
                    'user_type' =>    1 ,
                    'create_at' =>    date('Y-m-d H:i:s')
                ];

                $user_id = User::insertGetId($user_data);
                if($user_id && $user_id > 0){
                    //用户详细信息赋值
                    $user_info = [
                        'user_id'    => $user_id ,
                        'user_token' => $token , 
                        'user_type'  => 1 ,
                        'head_icon'  => '' , 
                        'real_name'  => '' , 
                        'phone'      => '' , 
                        'nickname'   => '' , 
                        'sign'       => '' , 
                        'papers_type'=> '' , 
                        'papers_name'=> '' ,
                        'papers_num' => ''
                    ];
                
                    //redis存储信息
                    Redis::set("user:regtoken:".$token , json_encode($user_info));

                    //事务提交
                    DB::commit();
                    return response()->json(['code' => 200 , 'msg' => '登录成功' , 'data' => ['user_info' => $user_info]]);
                } else {
                    //事务回滚
                    DB::rollBack();
                    return response()->json(['code' => 203 , 'msg' => '登录失败']);
                }
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   找回密码方法
     * @param  参数说明       body包含以下参数[
     *     phone             手机号
     *     password          新密码
     *     verifycode        验证码
     * ]
     * @param author    dzj
     * @param ctime     2020-05-23
     * return string
     */
    public static function doUserForgetPassword() {
        try {
            $body = self::$accept_data;
            //判断传过来的数组数据是否为空
            if(!$body || !is_array($body)){
                return response()->json(['code' => 202 , 'msg' => '传递数据不合法']);
            }

            //判断手机号是否为空
            if(!isset($body['phone']) || empty($body['phone'])){
                return response()->json(['code' => 201 , 'msg' => '请输入手机号']);
            } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
                return response()->json(['code' => 202 , 'msg' => '手机号不合法']);
            }

            //判断密码是否为空
            if(!isset($body['password']) || empty($body['password'])){
                return response()->json(['code' => 201 , 'msg' => '请输入新密码']);
            }
            
            //判断验证码是否为空
            if(!isset($body['verifycode']) || empty($body['verifycode'])){
                return response()->json(['code' => 201 , 'msg' => '请输入验证码']);
            }

            //验证码合法验证
            $verify_code = Redis::get('user:forget:'.$body['phone']);
            if(!$verify_code || empty($verify_code)){
                return ['code' => 201 , 'msg' => '请先获取验证码'];
            }

            //判断验证码是否一致
            if($verify_code != $body['verifycode']){
                return ['code' => 202 , 'msg' => '验证码错误'];
            }
            
            //key赋值
            $key = 'user:login:'.$body['phone'];

            //判断此学员是否被请求过一次(防止重复请求,且数据信息存在)
            if(Redis::get($key)){
                return response()->json(['code' => 204 , 'msg' => '此手机号未注册']);
            } else {
                //判断用户手机号是否注册过
                $student_count = User::where("phone" , $body['phone'])->count();
                if($student_count <= 0){
                    //存储学员的手机号值并且保存60s
                    Redis::setex($key , 60 , $body['phone']);
                    return response()->json(['code' => 204 , 'msg' => '此手机号未注册']);
                }
            }
            
            //开启事务
            DB::beginTransaction();

            //将数据插入到表中
            $update_user_password = User::where("phone" , $body['phone'])->update(['password' => md5($body['password']) , 'update_at' => date('Y-m-d H:i:s')]);
            if($update_user_password && !empty($update_user_password)){
                //事务提交
                DB::commit();
                return response()->json(['code' => 200 , 'msg' => '更新成功']);
            } else {
                //事务回滚
                DB::rollBack();
                return response()->json(['code' => 203 , 'msg' => '更新失败']);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
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
            return response()->json(['code' => 202 , 'msg' => '传递数据不合法']);
        }
        
        //判断验证码类型是否合法
        if(!isset($body['verify_type']) || !in_array($body['verify_type'] , [1,2])){
            return response()->json(['code' => 202 , 'msg' => '验证码类型不合法']);
        }
        
        //判断手机号是否为空
        if(!isset($body['phone']) || empty($body['phone'])){
            return response()->json(['code' => 201 , 'msg' => '请输入手机号']);
        } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
            return response()->json(['code' => 202 , 'msg' => '手机号不合法']);
        }
        
        //判断是注册还是忘记密码
        if($body['verify_type'] == 1){
            //设置key值
            $key = 'user:register:'.$body['phone'];
            //保存时间(5分钟)
            $time= 300;
            //短信模板code码
            $template_code = 'SMS_180053367';
            
            //判断用户手机号是否注册过
            $student_count = User::where("phone" , $body['phone'])->count();
            if($student_count > 0){
                return response()->json(['code' => 205 , 'msg' => '此手机号已被注册']);
            }
        } else {
            //设置key值
            $key = 'user:forget:'.$body['phone'];
            //保存时间(30分钟)
            $time= 1800;
            //短信模板code码
            $template_code = 'SMS_190727799';
            
            //判断用户手机号是否注册过
            $student_count = User::where("phone" , $body['phone'])->count();
            if($student_count <= 0){
                return response()->json(['code' => 204 , 'msg' => '此手机号未注册']);
            }
        }
        
        //判断验证码是否过期
        $code = Redis::get($key);
        if(!$code || empty($code)){
            //随机生成验证码数字,默认为6位数字
            $code = rand(100000,999999);
        }
        
        //发送验证信息流
        $data = ['mobile' => $body['phone'] , 'TemplateParam' => ['code' => $code] , 'template_code' => $template_code];
        $send_data = SmsFacade::send($data);
        
        //判断发送验证码是否成功
        if($send_data->Code == 'OK'){
            //存储学员的id值
            Redis::setex($key , $time , $code);
            return response()->json(['code' => 200 , 'msg' => '发送短信成功']);
        } else {
            return response()->json(['code' => 203 , 'msg' => '发送短信失败' , 'data' => $send_data->Message]);
        }
    }
    
    //删除redis指定key的所有键值信息
    public static function doDelRedisKeys($prefix){
        //获取所有的指定的前缀的信息列表
        $key_list = Redis::keys($prefix . '*');
        Redis::del($key_list);
    }
}
