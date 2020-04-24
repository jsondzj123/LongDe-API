<?php

namespace App\Http\Middleware;
use App\Providers\Rsa\RsaFactory;
use Closure;

class ApidateToken {
    public function handle($request, Closure $next){
        $url = $request->url(); //获取路由
        //解密 获得参数
        $data = $request->post();
        $rsa =  new RsaFactory();
        $a = $rsa->Servicersadecrypt($data);
        //缓存查询权限
//        $redisrole = \Redis::
        //判断权限
//        if($role){
//
//        }else{
//            return $next($request);
//        }
    }
}
