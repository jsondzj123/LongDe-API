<?php

namespace App\Http\Middleware;
use App\Models\Adminuser;
use App\Models\Authrules;
use App\Models\Roleauth;
use App\Providers\Rsa\RsaFactory;
use Closure;

class ApidateToken {
    public function handle($request, Closure $next){
        //解密 获得参数
        $data = $request->post();
        $rsa =  new RsaFactory();
        $user = $rsa->Servicersadecrypt($data);
        $url = ltrim(parse_url($request->url(),PHP_URL_PATH),'/'); //获取路由连接
        $userlist = Adminuser::GetUserOne($user['id']); //获取用户信息
        $authid = Authrules::getAuthOne($url);//获取权限id
        $role = Roleauth::getRoleOne($userlist['role_id']);//角色权限
        if(!strpos($role['auth_id'],(string)$authid['id'])){
            return "此用户没有权限";
        }else{
            return  "此用户权限很大";
        }
    }
}
