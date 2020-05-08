<?php
namespace App\Http\Controllers\Admin;

use Laravel\Lumen\Routing\Controller as BaseController;
use Maatwebsite\Excel\Facades\Excel;
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

    
    /*
     * @param  descriptsion    获取学员列表
     * @param  参数说明         body包含以下参数[
     *     student_id   学员id
     *     is_forbid    账号状态
     *     state_status 开课状态
     *     real_name    姓名
     *     paginate     每页显示条数
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-27
     * return  array
     */
    public function getStudentList(Request $request){
        //获取提交的参数
        try{
            //判断token或者body是否为空
            if(!empty($request->input('token')) && !empty($request->input('body'))){
                $rsa_data = app('rsa')->servicersadecrypt($request);
            } else {
                $rsa_data = [];
            }
            
            //获取全部学员列表
            $data = \App\Models\Student::getStudentList($rsa_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取学员列表成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }

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


    
    /*
     * @param  description   导入功能方法
     * @param  参数说明[
     *     $imxport      导入文件名称
     *     $excelpath    excel文件路径
     *     $is_limit     是否限制最大表格数量(1代表限制,0代表不限制)
     *     $limit        限制数量
     * ]
     * @param  author        dzj
     * @param  ctime         2020-04-30
    */
    public static function doImportExcel($imxport , $excelpath , $is_limit = 0 , $limit = 0){
        //获取提交的参数
        try{
            //导入数据方法
            $exam_array = Excel::toArray($imxport , $excelpath);
            
            //判断导入的excel文件中是否有信息
            if(!$exam_array || empty($exam_array)){
                return ['code' => 204 , 'msg' => '暂无信息导入'];
            } else {
                $array = [];
                //循环excel表中数据信息
                foreach($exam_array as $v){
                    //去掉header头字段信息(不加入表中)【备注:去掉二维数组中第一个数组】
                    unset($v[0]);
                    foreach($v as $k1 => $v1){
                        //去掉二维数组中最后一个空元素
                        unset($v1[count($v1)-1]);
                        for($i=0;$i<count($v1);$i++){
                            if($v1[$i] && !empty($v1[$i])){
                                $array[$k1] = $v1;
                            }
                        }
                    }
                }
            }
            //判断excel表格中总数量是否超过最大限制
            $max_count = count($array);
            if($is_limit > 0 && $max_count > $limit){
                return ['code' => 202 , 'msg' => '超过最大导入数量'];
            }
            return ['code' => 200 , 'msg' => '获取数据成功' , 'data' => $array];
        } catch (Exception $ex) {
            return ['code' => 500 , 'msg' => $ex->getMessage()];
        }
    }
    
    /*
     * @param  description   导入试题功能方法
     * @param  author        dzj
     * @param  ctime         2020-04-30
    */
    public function doImportExam(){
        //获取提交的参数
        try{
            //获取excel表格中试题列表
            $exam_list = self::doImportExcel(new \App\Imports\UsersImport , app()->basePath().'/invoices.xlsx' , 1 , 1000);
            if($exam_list['code'] == 200){
                foreach($exam_list['data'] as $v){
                    //插入试题表中数据
                    \App\Models\Student::insertStudent(['name'=>$v[0] , 'age' => $v[1] , 'sex' => $v[2]]);
                }
                return response()->json(['code' => 200 , 'msg' => '导入试题列表成功']);
            } else {
                return response()->json(['code' => $exam_list['code'] , 'msg' => $exam_list['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   导出做题记录功能方法
     * @param  参数说明[
     *     student_id     学员id(必传)
     *     bank_id        题库id(非必传)
     *     subject_id     科目id(非必传)
     *     type           类型(非必传)
     *     exam_date      做题日期(非必传)
     * ]
     * @param  author        dzj
     * @param  ctime         2020-04-30
    */
    public function doExportExamLog(){
        //获取提交的参数
        return Excel::download(new \App\Exports\ExamExport, 'examlog.xlsx');
    }

}
