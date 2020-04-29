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
     * @param  getUserList   获取用户列表
     * @param  return  array   返回用户信息
     * @param  author    lys
     * @param  ctime     2020-04-28 13:27
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
    

    public function upUserStatus(Request $request){
    	$data =  $request->post(); 
    	
    	$where = [];
    	$updateArr = [];
    	if( !isset($data['id']) || !isset($data['type']) ){
    		return response()->json(['code'=>201,'msg'=>'缺少参数']);
    	}
      	$userInfo = Adminuser::getUserOne($data['id']);
    	if(!$userInfo){
    			return response()->json(['code'=>$userInfo['code'],'msg'=>$userInfo['msg']]); 
    	}	
    	$where['id'] = $data['id'];
    	if($data['type'] == 1){
    		if($userInfo['data']['is_del'] == 1){
    			$updateArr['is_del'] = 0;
    		}else{ 
    			$updateArr['is_del'] = 1;
    		}	
    	}else if($data['type'] == 2){
    		if($userInfo['data']['is_forbid'] == 1){
    			$updateArr['is_forbid'] = 0;
    		}else{ 
    		    $updateArr['is_forbid'] = 1;
    		}	
    	}
    	$result = Adminuser::upUserStatus($where,$updateArr);
    	if($result){
    		return response()->json(['code'=>200,'msg'=>'Success']);    
    	}else{
    		return response()->json(['code'=>203,'msg'=>'网络超时，请重试']);    
    	}
    }
     
}
