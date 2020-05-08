<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Roleauth;
use App\Models\Authrules;
use App\Models\School;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use App\Tools\CurrentAdmin;
class SchoolController extends Controller {
  
     /*
     * @param  description 获取分校列表  
     * @param  参数说明       body包含以下参数[
     *     name       搜索条件
     *     dns        分校域名
     *     page         当前页码  必填项
     *     limit        每页显示条数
     * ]
     * @param author    lys
     * @param ctime     2020-05-05
     */
    public function getSchoolList(Request $request){
            $limit = isset($body['limit']) && $body['limit'] > 0 ? $body['limit'] : 15;
            $page     = isset($body['page']) && $body['page'] > 0 ? $body['page'] : 1;
            $offset   = ($page - 1) * $pagesize;
            $data = $request->all();
            !isset($page) || !empty($page) >0   
            $validator = Validator::make($data, 
                ['page' => 'required|integer',
                'limit' => 'required|integer'],
                School::message());
            if ($validator->fails()) {
                return response()->json(json_decode($validator->errors()->first(),1));
            }
            //$where['name'] = empty($data['school_name']) || !isset($data['school_name']) ?'':$data['school_name'];
            //$where['dns'] = empty($data['school_dns']) || !isset($data['school_dns']) ?'':$data['school_dns'];
            //$where['is_del'] = 1;
            $offset  = ($data['page']-1)*$data['limit'];
             
            $school_count = School::where('is_del','=',1)->count();
            if($school_count > 0){
                $schoolArr = School::where(function($query) use ($request){
                    if($request['name'] != ''){
                        $query->where('name','like','%'.$request['name'].'%');
                    }
                    if($request['dns'] != ''){
                        $query->where('dns','like','%'.$request['dns'].'%');
                    }
                    $query->where('is_del','=',1);
                })->select('id','name','logo_url','dns','is_forbid','logo_url')->offset($offset)->limit($data['limit'])->get();
                return response()->json(['code'=>200,'msg'=>'Success','data'=>['school_list' => $schoolArr , 'total' => $school_count , 'pagesize' => $request['limit'] , 'page' => $request['page']]]);           
            }
            return response()->json(['code'=>200,'msg'=>'Success','data'=>['school_list' => [] , 'total' => 0 , 'pagesize' => $request['limit'] , 'page' => $request['page']]]);           
    }
    /*
     * @param  description 修改分校状态 
     * @param  参数说明       body包含以下参数[
     *     id      分校id
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function doUpdateSchoolStatus(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, 
                ['type' => 'required|integer',
                'school_id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        try{
            $school = School::find($data['school_id']);
            if($data['type'] == 1){
                //删除
                $school->is_del = 0; 
                if( $school->save() &&Adminuser::upUserStatus(['school_id'=>$school['id']],['is_forbid'=>0])){
                    return response()->json(['code' => 200 , 'msg' => '更新成功']);
                } else {
                    return response()->json(['code' => 201 , 'msg' => '更新失败']);
                }
            }else if($data['type']  == 2){
                //禁用启用
                if($school['is_forbid'] == 1){
                    //禁用
                    $school->is_forbid = 0; 
                    if($school->save() && Adminuser::upUserStatus(['school_id'=>$school['id']],['is_forbid'=>0])){
                        return response()->json(['code' => 200 , 'msg' => '禁用成功']);
                    } else {
                        return response()->json(['code' => 201 , 'msg' => '禁用失败']);
                    }
                }else{
                    //启用
                     $school->is_forbid = 1; 
                     if($school->save() && Adminuser::upUserStatus(['school_id'=>$school['id']],['is_forbid'=>1])){
                        return response()->json(['code' => 200 , 'msg' => '启用成功']);
                    } else {
                        return response()->json(['code' => 201 , 'msg' => '启用失败']);
                    }
                }
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
        return response()->json(['code' => 200 , 'msg' => 'Success']);
    }
      /*
     * @param  description 学校添加 
     * @param  参数说明       body包含以下参数[
     *  'name' =>分校名称
        'dns' =>分校域名
        'logo_url' =>分校logo
        'introduce' =>分校简介
        'username' =>登录账号
        'password' =>登录密码
        'pwd' =>确认密码
        'realname' =>联系人
        'mobile' =>联系方式
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function doInsertSchool(Request $request){
        $data = $request->all();
        $validator = Validator::make(
                $data, 
                ['name' => 'required|unique:ld_school',
                 'dns' => 'required',
                 'logo_url'=>'required',
                 'introduce'=>'required',
                 'username'=>'required|unique:ld_admin',
                 'password'=>'required',
                 'pwd' =>'required',
                 'realname'=>'required',
                 'mobile'=>'required|regex:/^1[3456789][0-9]{9}$/',
                ],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        if($data['password'] != $data['pwd']){
            return response()->json(['code'=>2001,'msg'=>'两次密码不一致']);
        }
        try{
          
            $school = [
                'name' =>$data['name'],
                'dns'  =>$data['dns'],
                'logo_url'  =>$data['logo_url'],
                'introduce'  =>$data['introduce'],
                'admin_id'  => 12,
            ];
            $school_id = School::insertGetId($school);
            if($school_id <0){
                return response()->json(['code'=>2002,'msg'=>'创建学校未成功']);  
            }
            $admin =[
                'username' =>$data['username'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'realname' =>$data['realname'],
                'mobile' =>  $data['mobile'], 
                'role_id' => 0,  
                'admin_id'  => 12,
                'school_id' =>$school_id,
                'school_status' => 0,
            ];
            if(Admin::insertGetId($admin)>0){
                return response()->json(['code' => 200 , 'msg' => '创建账号成功']);
            } else {
                return response()->json(['code' => 201 , 'msg' => '创建账号未成功']);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
        return response()->json(['code' => 200 , 'msg' => 'Success']);
    }
    /*
     * @param  description 获取学校信息 
     * @param  参数说明       body包含以下参数[
     *  'school_id' =>学校id
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function getSchoolUpdate(Request $request){
        $data = $request->all();
        $validator = Validator::make(
                $data, 
                ['school_id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $school = School::select('id','name','dns','logo_url','introduce')->find($data['school_id']);
        return response()->json(['code' => 200 , 'msg' => 'Success','data'=>$school]);
    }
    /*
     * @param  description 修改分校信息 
     * @param  参数说明       body包含以下参数[
     *  'id'=>分校id
        'name' =>分校名称
        'dns' =>分校域名
        'logo_url' =>分校logo
        'introduce' =>分校简介
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function doSchoolUpdate(Request $request){
        $data = $request->all();
        $validator = Validator::make(
                $data, 
                ['id' => 'required|integer',
                'name' => 'required|unique:ld_school',
                'dns' => 'required',
                'logo_url' => 'required',
                'introduce' => 'required'],
                School::message());
        if($validator->fails()) {
          
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        
        if(School::where('id',$data['id'])->update($data)){
            return response()->json(['code' => 200 , 'msg' => '更新成功']);
        }else{
             return response()->json(['code' => 500 , 'msg' => '更新失败']);
        }
    }
    /*
     * @param  description 修改分校信息---权限管理
     * @param  参数说明       body包含以下参数[
     *      'id'=>分校id
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function getSchoolById(Request $request){
        $data = $request->all();
        $validator = Validator::make(
                $data, 
                ['id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $adminUser['school_name'] = School::select(['name'])->find($data['id']);
        $roleAuthId = Roleauth::where(['school_id'=>$data['id'],'is_super'=>1])->select('id','auth_id')->first()->toArray();
        $adminUser = Admin::where(['school_id'=>$data['id'],'role_id'=>$roleAuthId['id'],'is_del'=>1])->select('id','username','realname','mobile')->first()->toArray();
        $adminUser['role_id'] = $roleAuthId['id'];
        $adminUser['auth_id'] = $roleAuthId['auth_id'];
        $authRules = Authrules::getAuthAlls([],['id','name','title','parent_id']);
        $authRules = getParentsList($authRules);
        $arr = [
            'admin' =>$adminUser,
            'auth_rules'=>$authRules,
        ];
        return response()->json(['code'=>200,'msg'=>'success','data'=>$arr]);
    }
    /*
     * @param  description 修改分校信息---权限管理-账号编辑（获取）
     * @param  参数说明       body包含以下参数[
     *      'id'=>用户id
     * ]
     * @param author    lys
     * @param ctime     2020-05-07
     */
    public function getAdminById(Request $request){
        $id = $request->input('id');
        $admin = Admin::select('id','realname','mobile')->find($id);
        return response()->json(['code'=>200,'msg'=>'success','data'=>$admin]);
    }
    /*
     * @param  description 修改分校信息---权限管理-账号编辑
     * @param  参数说明       body包含以下参数[
     *      'id'=>用户id
     * ]
     * @param author    lys
     * @param ctime     2020-05-07
     */
    public function doAdminUpdate(){
        
        $data = $request->all();
        $validator = Validator::make(
            $data, 
                [
                'user_id' => 'required|integer',
                'mobile' => 'regex:/^1[3456789][0-9]{9}$/',
               
                ],School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $result = School::doAdminUpdate($data);
        return response()->json(['code'=>$result['code'],'msg'=>$result['msg']]);
    }
    /*
     * @param  description 获取分校讲师列表
     * @param  参数说明       body包含以下参数[
     *      'school_id'=>学校id
     * ]
     * @param author    lys
     * @param ctime     2020-05-07
     */
    public function getSchoolTeacherList(){
            $validator = Validator::make(self::$accept_data, 
                ['school_id' => 'required|integer'],
                School::message());
            if ($validator->fails()) {
                return response()->json(json_decode($validator->errors()->first(),1));
            }
            $result = School::getSchoolTeacherList(self::$accept_data);
            return response()->json($result);
    }

}
