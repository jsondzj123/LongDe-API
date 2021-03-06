<?php

namespace App\Http\Middleware;
use App\Models\Adminuser;
use App\Models\Authrules;
use App\Models\Roleauth;
use App\Models\Admin;
use App\Models\School;
use App\Providers\Rsa\RsaFactory;
use Closure;
use App\Tools\CurrentAdmin;

class ApiAuthToken {
    public function handle($request, Closure $next){
        $user = CurrentAdmin::user();  
        if(!isset($user['id']) || $user['id'] <=0 ){
            return response()->json(['code'=>403,'msg'=>'无此用户，请联系管理员']);
        }
        $schoolData = School::getSchoolOne(['id'=>$user['school_id'],'is_del'=>1],['id','name','is_forbid']);
        if($schoolData['code'] != 200){
            return response()->json(['code'=>403,'msg'=>'无此学校，请联系管理员']);
        }else{
            if($schoolData['data']['is_forbid'] != 1 ){
                return response()->json(['code'=>403,'msg'=>'学校已被禁用，请联系管理员']);
            }
        } 
        $url = ltrim(parse_url($request->url(),PHP_URL_PATH),'/'); //获取路由连接
        $userlist = Admin::GetUserOne(['id'=>$user['id'],'is_forbid'=>1,'is_del'=>1]); //获取用户信息 
        if($userlist['code'] != 200){
            return response()->json(['code'=>403,'msg'=>'无此用户，请联系管理员']);
        }
        $authid = Authrules::getAuthOne($url);//获取权限id
        if(!isset($authid['id'])||$authid['id'] <=0 ){
            return response()->json(['code'=>403,'msg'=>'此用户没有权限']);
        }
        $role = Roleauth::getRoleOne($userlist['data']['role_id']);//获取角色权限
        if(!strpos($role['data']['auth_id'],(string)$authid['id'])){
           return response()->json(['code'=>403,'msg'=>'此用户没有权限']);
        }else{
           return $next($request);
        }
    }
}
