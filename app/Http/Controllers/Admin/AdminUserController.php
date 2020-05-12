<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Admin as Adminuser;
use App\Models\Roleauth;
use App\Models\Authrules;
use App\Models\School;
use Illuminate\Support\Facades\Redis;
use App\Tools\CurrentAdmin;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller {

     /*
     * @param  description   获取用户列表
     * @param  参数说明       body包含以下参数[
     *     search       搜索条件 （非必填项）
     *     page         当前页码 （不是必填项）
     *     limit        每页显示条件 （不是必填项）
     *     school_id    学校id  （非必填项）
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */
    public function getAdminUserList(){
        $result     = Adminuser::getAdminUserList(self::$accept_data);
        if($result['code'] == 200){
            return response()->json($result);
        }else{
            return response()->json($result);
        }
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
    public function upUserStatus(){
    	$data =  self::$accept_data;
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
    			$updateArr['is_del'] = 0;
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

    public function getAuthList(){
         $result =  Adminuser::getAuthList(self::$accept_data);
         return response()->json($result);
    }
    /*
     * @param  description   添加后台账号
     * @param  参数说明       body包含以下参数[
     *     school_id       所属学校id
     *     account         账号
     *     real_name       姓名
     *     mobile          手机号
     *     sex             性别
     *     password        密码
     *     pwd             确认密码
     *     role_id         角色id
     *     teacher_id      关联讲师id串
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */

    public function doInsertAdminUser(){

        $data = self::$accept_data;

        $validator = Validator::make($data,
                [
                'school_id' => 'required|integer',
                'username' => 'required|unique:ld_admin',
                'realname' => 'required',
                'mobile' => 'required|regex:/^1[3456789][0-9]{9}$/',
                'sex' => 'required|integer',
                'password' => 'required',
                'pwd' => 'required',
                'role_id' => 'required|integer',
                ],
                Adminuser::message());
        if($validator->fails()) {
            return response()->json(['code'=>422,'msg'=>$validator->errors()->first()]);
        }
        if( !isset($data['teacher_id'])){
            return response()->json(['code'=>422,'msg'=>'缺少教师id']);
        }
        if($data['password'] != $data['pwd']){
            return response()->json(['code'=>422,'msg'=>'登录密码不一致']);
        }
        unset($data['pwd']);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        // $data['admin_id'] = CurrentAdmin::user()['id'];
        $data['admin_id'] = CurrentAdmin::user()['id'];
        $result = Adminuser::insertAdminUser($data);
        if($result>0){
            return   response()->json(['code'=>200,'msg'=>'添加成功']);
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

    public function getAdminUserUpdate(){
        $data = self::$accept_data;
        if( !isset($data['id']) || empty($data['id']) ){
            return response()->json(['code'=>201,'msg'=>'缺少参数，参数为空']);
        }
        $where['id']   = $data['id'];
        $adminUserArr = Adminuser::getUserOne($where);
        if($adminUserArr['code'] != 200){
            return response()->json(['code'=>202,'msg'=>'用户不存在']);
        }

        $adminUserArr['data']['school_name']  = School::getSchoolOne(['id'=>$adminUserArr['data']['school_id'],'is_forbid'=>1,'is_del'=>1],['name'])['data']['name'];

        $roleAuthArr = Roleauth::getRoleAuthAlls(['school_id'=>$adminUserArr['data']['school_id'],'is_del'=>1],['id','role_name']);

         $teacherArr = [];
        if(!empty($adminUserArr['data']['teacher_id'])){
            $teacher_id_arr = explode(',', $adminUserArr['data']['teacher_id']);
             $teacherArr= Teacher::whereIn('id',$teacher_id_arr)->where('is_del','!=',1)->where('is_forbid','!=',1)->select('id','real_name','type')->get();
        }
        $arr = [
            'admin_user'=>$adminUserArr['data'],
            'teacher' => $teacherArr,
            'role_auth' => $roleAuthArr,


        ];
        return response()->json(['code'=>200,'msg'=>'获取信息成功','data'=>$arr]);

    }
    /*
     * @param  description   账号信息（编辑）
     * @param  参数说明       body包含以下参数[
     *      id => 账号id
     * ]
     * @param author    lys
     * @param ctime     2020-05-04
     */

    public function doAdminUserUpdate(){
        $data = self::$accept_data;
        $validator = Validator::make($data,
                [
                'id' => 'required|integer',
                'school_id' => 'required|integer',
                'username' => 'required',
                'realname' => 'required',
                'mobile' => 'required|regex:/^1[3456789][0-9]{9}$/',
                'sex' => 'required|integer',
                'password' => 'required',
                'pwd' => 'required',
                'role_id' => 'required|integer',
                ],
                Adminuser::message());
        if($validator->fails()) {
            return response()->json(['code'=>422,'msg'=>$validator->errors()->first()]);
        }
        if( !isset($data['teacher_id'])){
            return response()->json(['code'=>422,'msg'=>'缺少教师id']);
        }
        if($data['password'] != $data['pwd']){
            return response()->json(['code'=>202,'msg'=>'登录密码不一致']);
        }
        $where['school_id'] = $data['school_id'];
        $where['username']   = $data['username'];
        $where['is_del'] = 1;
        $count = Adminuser::where($where)->where('id','!=',$data['id'])->count();
        if($count >=1 ){
             return response()->json(['code'=>203,'msg'=>'用户名已存在']);
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['pwd']);
        $result = Adminuser::where('id','=',$data['id'])->update($data);
        return   response()->json(['code'=>200,'msg'=>'更改成功']);
    }
    /*
     * @param  description   登录账号权限（菜单栏）
     * @param  参数说明       body包含以下参数[
     *      id => 角色id
     * ]
     * @param author    lys
     * @param ctime     2020-05-05
     */

    public function getAdminUserLoginAuth($admin_role_id){
        if(empty($admin_role_id) || !intval($admin_role_id)){
            return ['code'=>204,'msg'=>'参数值为空或参数类型错误'];
        }
        $adminRole =  Roleauth::getRoleOne(['id'=>$admin_role_id,'is_del'=>1],['id','role_name','auth_id']);
        if($adminRole['code'] != 200){
            return ['code'=>$adminRole['code'],'msg'=>$adminRole['msg']];
        }
        $adminRuths = Authrules::getAdminAuthAll($adminRole['data']['auth_id']);
        if($adminRuths['code'] != 200){
            return ['code'=>$adminRuths['code'],'msg'=>$adminRuths['msg']];
        }
        return ['code'=>200,'msg'=>'success','data'=>$adminRuths['data']];



    }



}
