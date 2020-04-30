<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Adminuser;
use App\Models\Roleauth;
use App\Models\Authrules;
use Illuminate\Support\Facades\Redis;
use Validator;

class RoleController extends Controller {
  
    /*
     * @param  getUserList   获取角色列表
     * @param  search  搜索条件
     * @param  page    当前页
     * @param  limit   显示条数
     * @param  return  array  
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
    public function upRoleStatus($id){
 
        $role = Roleauth::findOrfail($id);
dd($role);
        $role->is_del = 1;
        if($role->save()){
            return response()->json(['code'=>200,'msg'=>'更改成功']);
        }else{
            return response()->json(['code'=>201,'msg'=>'更改失败']);
        }
    }   
    /*
     * @param  upRoleStatus   添加角色
     * @param  $data=[
                'r_name'=> 角色名称
                'auth_id'=> 权限串
                'auth_desc'=> 角色描述
                'admin_id'=> 添加人
                'school_id'=> 所属学校id  
        ]                 添加数组
     * @param  author    lys
     * @param  ctime     2020-04-30
     */
    public function doRoleInsert(Request $request){
        $validator = Validator::make($request->all(), [
            'r_name'=> 'required',
            'auth_id'=> 'required',
            'auth_desc'=> 'required',
            'admin_id'=> 'required',
            'school_id'=> 'required',
        ]);
        if(Roleauth::create($request->all())){
            return response()->json(['code'=>200,'msg'=>'添加成功']);
        }else{
            return response()->json(['code'=>200,'msg'=>'添加失败']);
        }
        
    } 
    /*
     * @param  descriptsion   获取角色信息（编辑）
     * @param  $data=[
                'id'=> 角色id
        ]                 查询条件
     * @param  author    lys
     * @param  ctime     2020-04-30
     */
    public function getRoleAuthUpdate(Request $request){
        $data = $request->post();
        $where = [];
        $updateArr = [];
        if( !isset($data['id']) ||  empty($data['id'])){
            return response()->json(['code'=>201,'msg'=>'参数为空或缺少参数']);
        }
        $roleAuthArr = Roleauth::getRoleOne(['id'=>$data['id']],['id','r_name','auth_desc','auth_id','school_id']);
        if($roleAuthArr['code'] != 200){
            return response()->json(['code'=>$roleAuthArr['code'],'msg'=>$roleAuthArr['msg']]); 
        }
        // $roleAuthData = Roleauth::getRoleAuthAlls(['school_id'=>$roleAuthArr['data']['school_id'],'is_del'=>1]],['id','r_name','auth_desc','auth_id']);
   
        // $result = Roleauth::doRoleInsert($data);
        // if($result){
        //     return response()->json(['code'=>200,'msg'=>'Success']);    
        // }else{
        //     return response()->json(['code'=>204,'msg'=>'网络超时，请重试']);    
        // }
    }   




   
    

   
}
