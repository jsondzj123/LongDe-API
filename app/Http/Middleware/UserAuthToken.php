<?php

namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Redis;

class UserAuthToken {
    public function handle($request, Closure $next){
        //获取用户token值
        $token = $request->input('user_token');
        
        //判断用户token是否为空
        if(!$token || empty($token)){
            return ['code' => 201 , 'msg' => 'token值为空'];
        }
        
        //判断token值是否合法
        $redis_token = Redis::hLen("user:regtoken:".$token);
        if(!$redis_token || $redis_token <= 0) {
            return ['code' => 202 , 'msg' => 'token值非法'];
        }
        
        //解析json获取用户详情信息
        $json_info = Redis::hGetAll("user:regtoken:".$token);
        
        //根据手机号获取用户详情
        if($json_info['phone'] && !empty($json_info['phone'])){
            $user_info = User::where("phone" , $json_info['phone'])->first();
            if(!$user_info || empty($user_info)){
                return ['code' => 204 , 'msg' => '此用户不存在'];
            }

            //判断用户是否在其他设备登录
            if($user_info['token'] != $token){
                return ['code' => 206 , 'msg' => '您已在其他设备上登录'];
            }
        }
        
        //从redis中获取token相关信息
        //第一种方法赋值:$request->offsetSet('user_token_info' , json_decode(Redis::get("user:regtoken:".$token) , true));  //添加参数
        /****
         * 例如:public function doUpdateUser(Request $request) {}
         */
        //第二种方法赋值:$_REQUEST['user_info'] = json_decode(Redis::get("user:regtoken:".$token) , true);
        /****
         * 例如:public function doUpdateUser() {
         *     self::$accept_data
         * }
         */
        $_REQUEST['user_info'] = $json_info;
        return $next($request);//进行下一步(即传递给控制器)
    }
}
