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
        $redis_token = Redis::get("user:regtoken:".$token);
        if(!$redis_token || empty($redis_token)) {
            return ['code' => 202 , 'msg' => 'token值非法'];
        }
        
        //解析json获取用户详情信息
        $json_info = json_decode($redis_token , true);
        
        //根据手机号获取用户详情
        $user_info = User::where("phone" , $json_info['phone'])->first();
        if(!$user_info || empty($user_info)){
            return ['code' => 204 , 'msg' => '此用户不存在'];
        }
        
        //判断用户是否在其他设备登录
        if($user_info['token'] != $token){
            return ['code' => 206 , 'msg' => '您已在其他设备上登录'];
        }
        return $next($request);
    }
}
