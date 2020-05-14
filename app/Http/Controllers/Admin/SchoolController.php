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
use Illuminate\Support\Facades\Validator;
use App\Tools\CurrentAdmin;
use App\Models\AdminLog;
class SchoolController extends Controller {
  
     /*
     * @param  description 获取分校列表  
     * @param  参数说明       body包含以下参数[
     *     name       搜索条件
     *     dns        分校域名
     *     page         当前页码  
     *     limit        每页显示条数
     * ]
     * @param author    lys
     * @param ctime     2020-05-05
     */
    public function getSchoolList(){
            $data = self::$accept_data;
            $pagesize = isset($data['pagesize']) && $data['pagesize'] > 0 ? $data['pagesize'] : 15;
            $page     = isset($data['page']) && $data['page'] > 0 ? $data['page'] : 1;
            $offset   = ($page - 1) * $pagesize;

            $where['name'] = empty($data['school_name']) || !isset($data['school_name']) ?'':$data['school_name'];
            $where['dns'] = empty($data['school_dns']) || !isset($data['school_dns']) ?'':$data['school_dns'];
     
            $offset  = ($page -1)*$pagesize;
            $school_count = School::where(function($query) use ($where){
                    if($where['name'] != ''){
                        $query->where('name','like','%'.$where['name'].'%');
                    }
                    if($where['dns'] != ''){
                        $query->where('dns','like','%'.$where['dns'].'%');
                    }
                    $query->where('is_del','=',1);
                })->count();
            $sum_page = ceil($school_count/$pagesize);
            if($school_count > 0){
                $schoolArr = School::where(function($query) use ($where){
                    if($where['name'] != ''){
                        $query->where('name','like','%'.$where['name'].'%');
                    }
                    if($where['dns'] != ''){
                        $query->where('dns','like','%'.$where['dns'].'%');
                    }
                    $query->where('is_del','=',1);
                })->select('id','name','logo_url','dns','is_forbid','logo_url')->offset($offset)->limit($pagesize)->get();
                return response()->json(['code'=>200,'msg'=>'Success','data'=>['school_list' => $schoolArr , 'total' => $school_count , 'pagesize' => $pagesize , 'page' => $page,'sum_page'=>$sum_page,'name'=>$where['name'],'dns'=>$where['dns']]]);           
            }
            return response()->json(['code'=>200,'msg'=>'Success','data'=>['school_list' => [] , 'total' => 0 , 'pagesize' => $pagesize , 'page' => $page,'sum_page'=>$sum_page,'name'=>$where['name'],'dns'=>$where['dns']]]);           
    }
    /*
     * @param  description 修改分校状态 (删除)
     * @param  参数说明       body包含以下参数[
     *     id      分校id
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function doSchoolDel(){
        $data = self::$accept_data;
        $validator = Validator::make($data, 
                ['school_id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        try{
            $school = School::find($data['school_id']);
            $school->is_del = 0; 
            if( $school->save() &&Adminuser::upUserStatus(['school_id'=>$school['id']],['is_del'=>0])){
                AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'School' ,
                    'route_url'      =>  'admin/school/doSchoolForbid' , 
                    'operate_method' =>  'update' ,
                    'content'        =>  json_encode($data),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
                return response()->json(['code' => 200 , 'msg' => '删除成功']);
            } else {
                return response()->json(['code' => 201 , 'msg' => '删除失败']);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }

    /*
     * @param  description 修改分校状态 (启禁)
     * @param  参数说明       body包含以下参数[
     *     id      分校id
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function doSchoolForbid(){
        $data = self::$accept_data;
        $validator = Validator::make($data, 
                ['school_id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        try{
            $school = School::find($data['school_id']);
            if($school['is_forbid'] != 1){
                $school->is_forbid = 1; 
                $is_forbid = 1;
            }else{
                $school->is_forbid = 0; 
                $is_forbid = 0;
            }   
            if( $school->save() &&Adminuser::upUserStatus(['school_id'=>$school['id']],['is_forbid'=>$is_forbid])){
                AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'School' ,
                    'route_url'      =>  'admin/school/doSchoolDel' , 
                    'operate_method' =>  'update' ,
                    'content'        =>  json_encode($data),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
                return response()->json(['code' => 200 , 'msg' => '更新成功']);
            } else {
                return response()->json(['code' => 201 , 'msg' => '更新失败']);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }

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
        'realname' =>联系人(真实姓名)
        'mobile' =>联系方式
     * ]
     * @param author    lys
     * @param ctime     2020-05-06
     */
    public function doInsertSchool(){
        $data = self::$accept_data;
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
                'admin_id'  => CurrentAdmin::user()['id'],
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
                'admin_id'  => CurrentAdmin::user()['id'],
                'school_id' =>$school_id,
                'school_status' => 0,
            ];
            if(Adminuser::insertGetId($admin)>0){
                AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'School' ,
                    'route_url'      =>  'admin/school/doInsertSchool' , 
                    'operate_method' =>  'insert',
                    'content'        =>  json_encode($data),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
                return response()->json(['code' => 200 , 'msg' => '创建成功']);
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
    public function getSchoolUpdate(){
        $data = self::$accept_data;
        $validator = Validator::make(
                $data, 
                ['school_id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $school = School::where('id',$data['school_id'])->select('id','name','dns','logo_url','introduce')->get()->toArray();
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
    public function doSchoolUpdate(){
        $data = self::$accept_data;

        $validator = Validator::make(
                $data, 
                [
                    'id' => 'required|integer',
                    'name' => 'required',
                    'dns' => 'required',
                    'logo_url' => 'required',
                    'introduce' => 'required'
                ],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        if(School::where(['name'=>$data['name'],'is_del'=>1])->where('id','!=',$data['id'])->count()>0){
             return response()->json(['code' => 422 , 'msg' => '学校已存在']);
        }
        if(School::where('id',$data['id'])->update($data)){
                AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'School' ,
                    'route_url'      =>  'admin/school/doSchoolUpdate' , 
                    'operate_method' =>  'update',
                    'content'        =>  json_encode($data),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
            return response()->json(['code' => 200 , 'msg' => '更新成功']);
        }else{
            return response()->json(['code' => 200 , 'msg' => '更新成功']);
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
    public function getSchoolAdminById(){
        $data = self::$accept_data;
        $validator = Validator::make(
                $data, 
                ['id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $schoolData = School::select(['name'])->find($data['id']);

        $roleAuthId = Roleauth::where(['school_id'=>$data['id'],'is_super'=>1])->select('id','auth_id')->first(); //查询学校是否有超管人员信息
        if(is_null($roleAuthId)){
            return response()->json(['code'=>422,'msg'=>'请完善管理员信息']);
        }
        $adminUser = Adminuser::where(['school_id'=>$data['id'],'role_id'=>$roleAuthId['id'],'is_del'=>1])->select('id','username','realname','mobile')->first();
        $adminUser['role_id'] = $roleAuthId['id'] > 0 ? $roleAuthId['id']  : 0;
        $adminUser['auth_id'] = $roleAuthId['auth_id'] ? $roleAuthId['auth_id']  : '';
        $adminUser['school_name'] =  !empty($schoolData['name']) ? $schoolData['name']  : '';
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
     *      'user_id'=>用户id
     * ]
     * @param author    lys
     * @param ctime     2020-05-07
     */
    public function getAdminById(){
        $data = self::$accept_data;
        $validator = Validator::make(
                $data, 
                ['user_id' => 'required|integer'],
                School::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $admin = Adminuser::where('id',$data['user_id'])->select('id','realname','mobile')->get();
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
        
        $data = self::$accept_data;
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
    /*
     * @param  description 获取分校课程列表
     * @param  参数说明       body包含以下参数[
     *      'school_id'=>学校id
     * ]
     * @param author    lys
     * @param ctime     2020-05-11
     */
    public function getSchoolLessonList(){
            $validator = Validator::make(self::$accept_data, 
                ['school_id' => 'required|integer'],
                School::message());
            if ($validator->fails()) {
                return response()->json(json_decode($validator->errors()->first(),1));
            }
            $result = School::getSchoolLessonList(self::$accept_data);
            return response()->json($result);
    }

}
