<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Adminuser;
use App\Models\Roleauth;
use App\Models\Authrules;
use Illuminate\Support\Facades\Redis;


class AdminUserController extends Controller {
  
     /*
     * @param  description   获取用户列表
     * @param  参数说明       body包含以下参数[
     *     search       搜索条件
     *     page         当前页码
     *     limit        每页显示条件
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */
    public function getUserList(Request $request){
    	$data =  $request->post();
    	if(!isset($data['search']) || !isset($data['page']) || !isset($data['limit']) ){
    		return response()->json(['code'=>201,'msg'=>'缺少参数']);
    	}
    	if( empty($data['page']) || $data['page']<=1 ){
    		$data['page'] =1;
    	}
    	if( isset($data['limit']) || empty($data['limit']) || $data['page']<1 ) {
    		$data['limit'] = 10;
    	}
        $adminUserArr = Adminuser::getUserAll($data['search'],$data['page'],$data['limit']);
       	$arr = [
       		'data' => $adminUserArr,
       		'page' => $data['page'],
       		'limit' => $data['limit'],
       	];
        return response()->json(['code'=>200,'msg'=>'Success','data'=>$arr]);    
    }
    
    /*
     * @param  description  更改用户状态（删除/启用、禁用）
     * @param  参数说明       body包含以下参数[
     *     type         类型(1 删除 2 启用\禁用)
     *     id           用户id
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */
    public function upUserStatus(Request $request){
    	$data =  $request->post(); 
    	$where = [];
    	$updateArr = [];
    	if( !isset($data['id']) || !isset($data['type']) ){
    		return response()->json(['code'=>201,'msg'=>'缺少参数']);
    	}
      	$userInfo = Adminuser::getUserOne(['id'=>$data['id']]);
    	if(!$userInfo){
    			return response()->json(['code'=>$userInfo['code'],'msg'=>$userInfo['msg']]); 
    	}	
    	$where['id'] = $data['id'];
    	if($data['type'] == 1){
    			$updateArr['is_del'] = 1;	
    	}else if($data['type'] == 2){
    		if($userInfo['data']['is_forbid'] == 1)  $updateArr['is_forbid'] = 0;  else  $updateArr['is_forbid'] = 1;	
    	}
    	$result = Adminuser::upUserStatus($where,$updateArr);
    	if($result){
    		return response()->json(['code'=>200,'msg'=>'Success']);    
    	}else{
    		return response()->json(['code'=>500,'msg'=>'网络超时，请重试']);    
    	}
    }

    public function getAuthList(Request $request){
        $data = $request->post();
        if( !isset($data['school_id']) ){
            return response()->json(['code'=>201,'msg'=>'缺少参数']);
        }
        $roleAuthData = Roleauth::getRoleAuthAlls(['school_id'=>$data['school_id']],['id','r_name']);
        return response()->json(['code'=>200,'msg'=>'Success','data'=>$roleAuthData]);    
    }
    /*
     * @param  description   添加后台账号
     * @param  参数说明       body包含以下参数[
     *     school_id       所属学校id
     *     account         账号
     *     real_name       姓名
     *     phone           手机号
     *     sex             性别
     *     password        密码
     *     pwd             确认密码
     *     role_id         角色id
     *     teacher_id      关联讲师id串
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */

    public function doInsertAdminUser(Request $request){
        $data = $request->post();
        if( !isset($data['school_id']) || !isset($data['account']) || !isset($data['real_name']) || !isset($data['phone'])  || !isset($data['sex']) || !isset($data['password']) || !isset($data['pwd'])  || !isset($data['role_id']) || !isset($data['teacher_id']) ){
            return response()->json(['code'=>201,'msg'=>'缺少参数']);
        }
        if($data['password'] != $data['pwd']){
            return response()->json(['code'=>202,'msg'=>'登录密码不一致']);
        }
        $where['school_id'] = $data['school_id'];
        $where['account']   = $data['account'];
        $adminUserArr = Adminuser::getUserOne($where);
        if($adminUserArr['code'] == 200){
             return response()->json(['code'=>203,'msg'=>'用户名已存在']);    
        }
        unset($data['pwd']);
        $result = Adminuser::insertAdminUser($data);
        if($result){
          return   response()->json(['code'=>200,'msg'=>'Success']); 
        }else{
           return  response()->json(['code'=>500,'msg'=>'网络超时，请重试']); 
        }
    }
    /*
     * @param  description   获取账号信息（编辑）
     * @param  参数说明       body包含以下参数[
     *      id => 账号id  
     * ]
     * @param author    lys
     * @param ctime     2020-05-04
     */

    public function getAdminUserUpdate(Request $request){
        $data = $request->post();
        if( !isset($data['id']) || empty($data['id']) ){
            return response()->json(['code'=>201,'msg'=>'缺少参数，参数为空']);
        }
         $where['id']   = $data['id'];
        $adminUserArr = Adminuser::getUserOne($where);
        if($adminUserArr['code'] != 200){
            return response()->json(['code'=>202,'msg'=>'用户不存在']);    
        }
        $roleAuthArr = Roleauth::getRoleAuthAlls(['school_id'=>$adminUserArr['data']['school_id'],'is_del'=>1],['id','r_name']);
        $teacher_id_arr = explode(',', $adminUserArr['data']['teacher_id']);
        print_r($teacher_id_arr);die;
       
    
    }


     
}
