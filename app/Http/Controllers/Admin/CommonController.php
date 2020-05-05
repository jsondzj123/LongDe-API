<?php
namespace App\Http\Controllers\Admin;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CommonController extends BaseController {
   /*
     * @param  description   讲师或教务搜索列表
     * @param  参数说明       body包含以下参数[
     *     parent_id     学科分类id
     *     real_name     老师姓名
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
    */
    public function getTeacherSearchList(Request $request){
        //获取提交的参数
        try{
            //判断token或者body是否为空
            if(!empty($request->input('token')) && !empty($request->input('body'))){
                $rsa_data = app('rsa')->servicersadecrypt($request);
            } else {
                $rsa_data = [];
            }
            //获取讲师教务搜索列表
            $data = \App\Models\Teacher::getTeacherSearchList($rsa_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取老师搜索列表成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
     * @param  description   获取添加账号信息
     * @param  id            当前登录用户id
     * @param author    lys
     * @param ctime     2020-04-29
    */
    public function getAccountInfoOne(Request $request){
        try{
            $adminId = $request->input('id');
            $data =  \App\Models\Adminuser::getUserOne($adminId);
            if($data['code'] != 200){
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            } 
            $adminUserSchoolId = $data['data']['school_id'];
            $adminUserSchoolType = $data['data']['school_status']; 
            if($adminUserSchoolType >0){
                //总校
                $schoolData = \App\Models\School::getSchoolAlls(['id','name']);
            }else{
                //分校
                $schoolData = \App\Models\School::getSchoolOne($adminUserSchoolId,['id','name']);
            }
            $rolAuthArr = \App\Models\Roleauth::getRoleAuthAlls(['school_id'=>$adminUserSchoolId],['id','role_name']);
            $arr = [
                'school'=>$schoolData,
                'role_auth'=>$rolAuthArr
            ];
            return response()->json(['code' => 200 , 'msg' => '获取信息成功' , 'data' => $arr]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
     * @param  description   获取角色权限列表
     * @param author    lys
     * @param ctime     2020-04-29
    */
    public  function getRoleAuth(Request $request){
         try{
            $adminId = $request->input('id');
            $data =  \App\Models\Adminuser::getUserOne(['id'=>$adminId]);
            if($data['code'] != 200){
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            } 
            $adminUserSchoolId = $data['data']['school_id'];
            $adminUserSchoolType = $data['data']['school_status']; 
            
            if($adminUserSchoolType >0){
                //总校 Auth 
                $roleAuthArr = \App\Models\Authrules::getAuthAlls([],['id','name','title','parent_id']);
            }else{
                //分校  Auth
                $schoolData = \App\Models\Roleauth::getRoleOne(['school_id'=>$adminUserSchoolId,'is_del'=>1,'is_super'=>1],['id','role_name','auth_desc','auth_id']);
              
                if( $schoolData['code'] != 200){    
                     return response()->json(['code' => 403 , 'msg' => '请联系总校超级管理员' ]);
                }
                $auth_id_arr = explode(',',$schoolData['data']['auth_id']);
      
                if(!$auth_id_arr){
                     $auth_id_arr = [$auth_id];
                }
                $roleAuthArr = \App\Models\Authrules::getAuthAlls(['id'=>$auth_id_arr],['id','name','title','parent_id']);
            }

            $roleAuthData = \App\Models\Roleauth::getRoleAuthAlls(['school_id'=>$adminUserSchoolId,'is_del'=>1],['id','role_name','auth_desc','auth_id']);
            $roleAuthArr  = getParentsList($roleAuthArr);
            $arr = [
                'role_auth'=>$roleAuthData,
                'auth'=>$roleAuthArr,
                'school_id'=>$adminUserSchoolId,
                'admin_id' =>$adminId,
            ];
            return response()->json(['code' => 200 , 'msg' => '获取信息成功' , 'data' => $arr]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }   
    }

}
