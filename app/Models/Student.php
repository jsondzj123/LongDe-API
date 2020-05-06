<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;

class Student extends Model {
    //指定别的表名
    public $table      = 'ld_student';
    //时间戳设置
    public $timestamps = false;

    /*
     * @param  description   添加学员方法
     * @param  data          数组数据
     * @param  author        dzj
     * @param  ctime         2020-04-27
     * return  int
     */
    public static function insertStudent($data) {
        return self::insertGetId($data);
    }

    /*
     * @param  descriptsion    根据学员id获取详细信息
     * @param  参数说明         body包含以下参数[
     *     student_id   学员id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-27
     * return  array
     */
    public static function getStudentInfoById($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断学员id是否合法
        if(!isset($body['student_id']) || empty($body['student_id']) || $body['student_id'] <= 0){
            return ['code' => 202 , 'msg' => '学员id不合法'];
        }

        //根据id获取学员详细信息
        $student_info = self::where('id',$body['student_id'])->select('school_id','phone','real_name','sex','papers_type','papers_num','birthday','address_locus','age','educational','family_phone','office_phone','contact_people','contact_phone','email','qq','wechat','address','remark')->first()->toArray();
        return ['code' => 200 , 'msg' => '获取学员信息成功' , 'data' => $student_info];
    }

    /*
     * @param  descriptsion    获取学员列表
     * @param  参数说明         body包含以下参数[
     *     student_id   学员id
     *     is_forbid    账号状态
     *     state_status 开课状态
     *     real_name    姓名
     *     pagesize     每页显示条数
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-27
     * return  array
     */
    public static function getStudentList($body=[]) {
        //每页显示的条数
        $pagesize = isset($body['pagesize']) && $body['pagesize'] > 0 ? $body['pagesize'] : 15;
        $page     = isset($body['page']) && $body['page'] > 0 ? $body['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        
        //获取学员的总数量
        $student_count = self::count();
        if($student_count > 0){
            //学员列表
            $student_list = self::where(function($query) use ($body){
                //判断学科id是否选择
                if(isset($body['subject_id']) && !empty($body['subject_id']) && $body['subject_id'] > 0){
                    $query->where('subject_id' , '=' , $body['subject_id']);
                }

                //判断账号状态是否选择
                if(isset($body['is_forbid']) && !empty($body['is_forbid']) && in_array($body['is_forbid'] , [1,2])){
                    $query->where('is_forbid' , '=' , $body['is_forbid']);
                }

                //判断开课状态是否选择
                if(isset($body['state_status']) && !empty($body['state_status'])){
                    $query->where('state_status' , '=' , $body['state_status']);
                }

                //判断搜索内容是否为空
                if(isset($body['search']) && !empty($body['search'])){
                    $query->where('real_name','like','%'.$body['search'].'%')->orWhere('phone','like','%'.$body['search'].'%');
                }
            })->select('id as student_id','real_name','phone','create_at','enroll_status','state_status','is_forbid')->orderByDesc('create_at')->offset($offset)->limit($pagesize)->get();
            return ['code' => 200 , 'msg' => '获取学员列表成功' , 'data' => ['student_list' => $student_list , 'total' => $student_count , 'pagesize' => $pagesize , 'page' => $page]];
        }
        return ['code' => 200 , 'msg' => '获取学员列表成功' , 'data' => ['student_list' => [] , 'total' => 0 , 'pagesize' => $pagesize , 'page' => $page]];
    }

    /*
     * @param  description   修改学员的方法
     * @param  参数说明       body包含以下参数[
     *     student_id   学员id
     *     phone        手机号
     *     real_name    学员姓名
     *     sex          性别(1男,2女)
     *     papers_type  证件类型(1代表身份证,2代表护照,3代表港澳通行证,4代表台胞证,5代表军官证,6代表士官证,7代表其他)
     *     papers_num   证件号码
     *     birthday     出生日期
     *     address_locus户口所在地
     *     age          年龄
     *     educational  学历(1代表小学,2代表初中,3代表高中,4代表大专,5代表大本,6代表研究生,7代表博士生,8代表博士后及以上)
     *     family_phone 家庭电话号
     *     office_phone 办公电话
     *     contact_people  紧急联系人
     *     contact_phone   紧急联系电话
     *     email           邮箱
     *     qq              QQ号码
     *     wechat          微信
     *     address         地址
     *     remark          备注
     * ]
     * @param author    dzj
     * @param ctime     2020-04-27
     * return string
     */
    public static function doUpdateStudent($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断学员id是否合法
        if(!isset($body['student_id']) || empty($body['student_id']) || $body['student_id'] <= 0){
            return ['code' => 202 , 'msg' => '学员id不合法'];
        }

        //判断手机号是否为空
        if(!isset($body['phone']) || empty($body['phone'])){
            return ['code' => 201 , 'msg' => '请输入手机号'];
        } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
            return ['code' => 202 , 'msg' => '手机号不合法'];
        }

        //判断姓名是否为空
        if(!isset($body['real_name']) || empty($body['real_name'])){
            return ['code' => 201 , 'msg' => '请输入姓名'];
        }

        //判断性别是否选择
        if(isset($body['sex']) && !empty($body['sex']) && !in_array($body['sex'] , [1,2])){
            return ['code' => 202 , 'msg' => '性别不合法'];
        }

        //判断证件类型是否合法
        if(isset($body['papers_type']) && !empty($body['papers_type']) && !in_array($body['papers_type'] , [1,2,3,4,5,6,7])){
            return ['code' => 202 , 'msg' => '证件类型不合法'];
        }
        
        //判断年龄是否为空
        if(isset($body['age']) && !empty($body['age']) && $body['age'] < 0){
            return ['code' => 201 , 'msg' => '请输入年龄'];
        }
        
        //判断最高学历是否合法
        if(isset($body['educational']) && !empty($body['educational']) && !in_array($body['educational'] , [1,2,3,4,5,6,7,8])){
            return ['code' => 202 , 'msg' => '最高学历类型不合法'];
        }
        
        //获取学员id
        $student_id = $body['student_id'];
        
        //将更新时间追加
        $body['update_at'] = date('Y-m-d H:i:s');
        unset($body['student_id']);
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

        //根据学员id更新信息
        if(false !== self::where('id',$student_id)->update($body)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Student' ,
                'route_url'      =>  'admin/student/doUpdateStudent' , 
                'operate_method' =>  'update' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '更新成功'];
        } else {
            return ['code' => 203 , 'msg' => '更新失败'];
        }
    }


    /*
     * @param  description   添加学员的方法
     * @param  参数说明       body包含以下参数[
     *     phone        手机号
     *     real_name    学员姓名
     *     sex          性别(1男,2女)
     *     papers_type  证件类型(1代表身份证,2代表护照,3代表港澳通行证,4代表台胞证,5代表军官证,6代表士官证,7代表其他)
     *     papers_num   证件号码
     *     birthday     出生日期
     *     address_locus户口所在地
     *     age          年龄
     *     educational  学历(1代表小学,2代表初中,3代表高中,4代表大专,5代表大本,6代表研究生,7代表博士生,8代表博士后及以上)
     *     family_phone 家庭电话号
     *     office_phone 办公电话
     *     contact_people  紧急联系人
     *     contact_phone   紧急联系电话
     *     email           邮箱
     *     qq              QQ号码
     *     wechat          微信
     *     address         地址
     *     remark          备注
     * ]
     * @param author    dzj
     * @param ctime     2020-04-27
     * return string
     */
    public static function doInsertStudent($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断手机号是否为空
        if(!isset($body['phone']) || empty($body['phone'])){
            return ['code' => 201 , 'msg' => '请输入手机号'];
        } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['phone'])) {
            return ['code' => 202 , 'msg' => '手机号不合法'];
        }

        //判断姓名是否为空
        if(!isset($body['real_name']) || empty($body['real_name'])){
            return ['code' => 201 , 'msg' => '请输入姓名'];
        }

        //判断性别是否选择
        if(isset($body['sex']) && !empty($body['sex']) && !in_array($body['sex'] , [1,2])){
            return ['code' => 202 , 'msg' => '性别不合法'];
        }

        //判断证件类型是否合法
        if(isset($body['papers_type']) && !empty($body['papers_type']) && !in_array($body['papers_type'] , [1,2,3,4,5,6,7])){
            return ['code' => 202 , 'msg' => '证件类型不合法'];
        }
        
        //判断年龄是否为空
        if(isset($body['age']) && !empty($body['age']) && $body['age'] < 0){
            return ['code' => 201 , 'msg' => '请输入年龄'];
        }
        
        //判断最高学历是否合法
        if(isset($body['educational']) && !empty($body['educational']) && !in_array($body['educational'] , [1,2,3,4,5,6,7,8])){
            return ['code' => 202 , 'msg' => '最高学历类型不合法'];
        }
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
        $school_id= isset(AdminLog::getAdminInfo()->admin_user->school_id) ? AdminLog::getAdminInfo()->admin_user->school_id : 0;

        //将所属网校id和后台人员id追加
        $body['admin_id']   = $admin_id;
        $body['school_id']  = $school_id;
        $body['reg_source'] = 2;
        $body['create_at']  = date('Y-m-d H:i:s');

        //将数据插入到表中
        if(false !== self::insertStudent($body)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Student' ,
                'route_url'      =>  'admin/student/doInsertStudent' , 
                'operate_method' =>  'insert' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '添加成功'];
        } else {
            return ['code' => 203 , 'msg' => '添加失败'];
        }
    }
    
    /*
     * @param  descriptsion    启用/禁用的方法
     * @param  参数说明         body包含以下参数[
     *     is_forbid      是否启用(1代表启用,2代表禁用)
     *     student_id     学员id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-28
     * return  array
     */
    public static function doForbidStudent($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断学员id是否合法
        if(!isset($body['student_id']) || empty($body['student_id']) || $body['student_id'] <= 0){
            return ['code' => 202 , 'msg' => '学员id不合法'];
        }
        
        //根据学员的id获取学员的状态
        $is_forbid = self::where('id',$body['student_id'])->pluck('is_forbid');

        //追加更新时间
        $data = [
            'is_forbid'    => $is_forbid[0] > 1 ? 1 : 2 ,
            'update_at'    => date('Y-m-d H:i:s')
        ];
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

        //根据学员id更新账号状态
        if(false !== self::where('id',$body['student_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Student' ,
                'route_url'      =>  'admin/student/doForbidStudent' , 
                'operate_method' =>  'update' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '操作成功'];
        } else {
            return ['code' => 203 , 'msg' => '操作失败'];
        }
    }
}
