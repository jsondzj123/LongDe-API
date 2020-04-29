<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Adminuser;
use App\Models\Roleauth;
use App\Models\Authrules;
use Illuminate\Support\Facades\Redis;


class RoleController extends Controller {
  
    /*
     * @param  getUserList   获取用户列表
     * @param  return  array   返回用户信息
     * @param  author    lys
     * @param  ctime     2020-04-28 13:27
     */
    public function getAuthList(Request $request){
        $data =  $request->post();
        $where= [];
        if( !isset($data['search']) || !isset($data['page']) || !isset($data['limit']) ){
            return response()->json(['code'=>202,'msg'=>'缺少参数']);
        }else{
              $where['search'] = $data['search'];
        }
        if(  empty($data['page']) || $data['page']<=1 ){
            $data['page'] =1;
        }
        if(  empty($data['limit']) || $data['page']<1 ){
            $data['limit'] = 10;
        }
        $where['school_id'] = 1;
        $authArr = Roleauth::getRoleAuthAll($where,$data['page'],$data['limit']);
        $arr = [
            'data' => $authArr,
            'page' => $data['page'],
            'limit' => $data['limit'],
        ];
        return response()->json(['code'=>200,'msg'=>'Success','data'=>$arr]);    
    }
     /*
     * @param  upRoleStatus   修改角色状态
     * @param  data
     * @param  return  array  状态信息
     * @param  author    lys
     * @param  ctime     2020-04-28 13:27
     */
    public function upRoleStatus(Request $request){
        $data = $request->post();
        $where = [];
        $updateArr = [];
        if( !isset($data['id']) ){
            return response()->json(['code'=>201,'msg'=>'缺少参数']);
        }
        $roleAuthArr = Roleauth::getRoleOne($data['id']);
        if(!$roleAuthArr){
            return response()->json(['code'=>$roleAuthArr['code'],'msg'=>$roleAuthArr['msg']]); 
        }
        $where['id']= $roleAuthArr['data']['id'];
        
        if($roleAuthArr['data']['is_del'] == 1){
            $updateArr['is_del'] = 0;
        }else{ 
            $updateArr['is_del'] = 1;
        }

        $result = Roleauth::upRoleStatus($where,$updateArr);

        if($result){
            return response()->json(['code'=>200,'msg'=>'Success']);    
        }else{
            return response()->json(['code'=>203,'msg'=>'网络超时，请重试']);    
        }
    }   




   
    

   
}
