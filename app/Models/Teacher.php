<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;

class Teacher extends Model {
    //指定别的表名
    public $table      = 'ld_lecturer_educationa';
    //时间戳设置
    public $timestamps = false;

    protected $fillable = [
        'teacher_name',
        'teacher_introduce',
        'teacher_header_pic',
        'subject_id'
    ];
    /*
     * @param  description   添加教师/教务方法
     * @param  data          数组数据
     * @param  author        dzj
     * @param  ctime         2020-04-25
     * return  int
     */
    public static function insertTeacher($data) {
        return self::insertGetId($data);
    }

    /*
     * @param  descriptsion    根据讲师或教务id获取详细信息
     * @param  参数说明         body包含以下参数[
     *     teacher_id   讲师或教务id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function getTeacherInfoById($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断讲师或教务id是否合法
        if(!isset($body['teacher_id']) || empty($body['teacher_id']) || $body['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }

        //根据id获取讲师或教务详细信息
        $teacher_info = self::where('id',$body['teacher_id'])->select('head_icon','school_id','phone','real_name','sex','qq','wechat','parent_id','child_id','describe','content')->first()->toArray();
        return ['code' => 200 , 'msg' => '获取老师信息成功' , 'data' => $teacher_info];
    }

    /*
     * @param  descriptsion    根据讲师或教务id获取详细信息
     * @param  参数说明         body包含以下参数[
     *     teacher_id   讲师或教务id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function getTeacherList($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断讲师或教务类型是否合法
        if(!isset($body['type']) || empty($body['type']) || $body['type'] <= 0 || !in_array($body['type'] , [1,2])){
            return ['code' => 202 , 'msg' => '老师类型不合法'];
        }
        
        //每页显示的条数
        $pagesize = isset($body['pagesize']) && $body['pagesize'] > 0 ? $body['pagesize'] : 15;
        $page     = isset($body['page']) && $body['page'] > 0 ? $body['page'] : 1;
        $offset   = ($page - 1) * $pagesize;

        //获取讲师或教务列表
        $teacher_list = self::where(function($query) use ($body){
            $query->where('is_del' , '=' , 0);
            //获取老师类型(讲师还是教务)
            $query->where('type' , '=' , $body['type']);
            
            //判断搜索内容是否为空
            if(isset($body['search']) && !empty($body['search'])){
                $query->where('id','=',$body['search'])->orWhere('real_name','like','%'.$body['search'].'%');
            }
        })->select('id as teacher_id','real_name','phone','create_at','number','is_recommend')->orderByDesc('create_at')->offset($offset)->limit($pagesize)->get();
        return ['code' => 200 , 'msg' => '获取老师列表成功' , 'data' => $teacher_list];
    }
    
    /*
     * @param  description   讲师或教务搜索列表
     * @param  参数说明       body包含以下参数[
     *     parent_id     学科分类id
     *     real_name     老师姓名
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
     */
    public static function getTeacherSearchList($body=[]) {
        //获取讲师或教务列表
        $teacher_list = self::where(function($query) use ($body){
            $query->where('is_del' , '=' , 0);
            //判断学科分类是否选择
            if(isset($body['parent_id']) && !empty($body['parent_id']) && $body['parent_id'] > 0){
                $query->where('parent_id','=',$body['parent_id']);
            }
            
            //判断姓名是否为空
            if(isset($body['real_name']) && !empty($body['real_name'])){
                $query->where('real_name','like','%'.$body['real_name'].'%');
            }
        })->select('id as teacher_id','real_name','type')->orderByDesc('create_at')->get()->toArray();
        
        //判断获取列表是否为空
        if($teacher_list && !empty($teacher_list)){
            $arr = [];
            foreach($teacher_list as $k => $v){
                //教务
                if($v['type'] == 1){
                    $arr['jiaowu'][] = [
                        'teacher_id' =>  $v['teacher_id'] ,
                        'real_name'  =>  $v['real_name']
                    ];
                } else {
                    $arr['jiangshi'][] = [
                        'teacher_id' =>  $v['teacher_id'] ,
                        'real_name'  =>  $v['real_name']
                    ];
                }
            }
            $teacher_list = $arr;
        }
        return ['code' => 200 , 'msg' => '获取老师列表成功' , 'data' => $teacher_list];
    }

    /*
     * @param  descriptsion    更改讲师教务的方法
     * @param  参数说明         body包含以下参数[
     *     teacher_id   讲师或教务id
     *     head_icon    头像
     *     phone        手机号
     *     real_name    讲师姓名/教务姓名
     *     sex          性别
     *     qq           QQ号码
     *     wechat       微信号
     *     parent_id    学科一级分类id
     *     child_id     学科二级分类id
     *     describe     讲师描述/教务描述
     *     content      讲师详情
     *     type         老师类型(1代表教务,2代表讲师)
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function doUpdateTeacher($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断讲师或教务id是否合法
        if(!isset($body['teacher_id']) || empty($body['teacher_id']) || $body['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }

        //判断头像是否上传
        if(!isset($body['head_icon']) || empty($body['head_icon'])){
            return ['code' => 201 , 'msg' => '请上传头像'];
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
        if(!isset($body['sex']) || empty($body['sex'])){
            return ['code' => 201 , 'msg' => '请选择性别'];
        } else if(!in_array($body['sex'] , [1,2])) {
            return ['code' => 202 , 'msg' => '性别不合法'];
        }

        //判断描述是否为空
        if(!isset($body['describe']) || empty($body['describe'])){
            return ['code' => 201 , 'msg' => '请输入描述'];
        }

        //如果是讲师
        if($body['type'] > 1){
            //判断学科是否选择
            if((!isset($body['parent_id']) || empty($body['parent_id'])) || (!isset($body['child_id']) || empty($body['child_id'])) || ($body['child_id'] <= 0 || $body['child_id'] <= 0)){
                return ['code' => 201 , 'msg' => '请选择关联学科'];
            }

            //判断详情是否为空
            if(!isset($body['content']) || empty($body['content'])){
                return ['code' => 201 , 'msg' => '请输入详情'];
            }
        }

        //获取老师id
        $teacher_id = $body['teacher_id'];
        
        //将更新时间追加
        $body['update_at'] = date('Y-m-d H:i:s');
        unset($body['teacher_id']);

        //根据讲师或教务id更新信息
        if(false !== self::where('id',$teacher_id)->update($body)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   AdminLog::getAdminInfo()->id  ,
                'module_name'    =>  'Teacher' ,
                'route_url'      =>  'admin/teacher/doUpdateTeacher' , 
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
     * @param  description   增加讲师教务的方法
     * @param  参数说明       body包含以下参数[
     *     head_icon    头像
     *     phone        手机号
     *     real_name    讲师姓名/教务姓名
     *     sex          性别(1男,2女)
     *     qq           QQ号码
     *     wechat       微信号
     *     parent_id    学科一级分类id
     *     child_id     学科二级分类id
     *     describe     讲师描述/教务描述
     *     content      讲师详情
     *     type         老师类型(1代表教务,2代表讲师)
     * ]
     * @param author    dzj
     * @param ctime     2020-04-25
     * return string
     */
    public static function doInsertTeacher($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断是教师还是教务
        if(!isset($body['type']) || empty($body['type']) || !in_array($body['type'] , [1,2])){
            return ['code' => 202 , 'msg' => '老师类型不合法'];
        } else {
            //判断头像是否上传
            if(!isset($body['head_icon']) || empty($body['head_icon'])){
                return ['code' => 201 , 'msg' => '请上传头像'];
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
            if(!isset($body['sex']) || empty($body['sex'])){
                return ['code' => 201 , 'msg' => '请选择性别'];
            } else if(!in_array($body['sex'] , [1,2])) {
                return ['code' => 202 , 'msg' => '性别不合法'];
            }

            //判断描述是否为空
            if(!isset($body['describe']) || empty($body['describe'])){
                return ['code' => 201 , 'msg' => '请输入描述'];
            }

            //如果是讲师
            if($body['type'] > 1){
                //判断学科是否选择
                if((!isset($body['parent_id']) || empty($body['parent_id'])) || (!isset($body['child_id']) || empty($body['child_id'])) || ($body['child_id'] <= 0 || $body['child_id'] <= 0)){
                    return ['code' => 201 , 'msg' => '请选择关联学科'];
                }

                //判断详情是否为空
                if(!isset($body['content']) || empty($body['content'])){
                    return ['code' => 201 , 'msg' => '请输入详情'];
                }
            }
        }

        //将所属网校id和后台人员id追加
        $body['admin_id']   = AdminLog::getAdminInfo()->id;
        $body['school_id']  = AdminLog::getAdminInfo()->school_id;
        $body['create_at']  = date('Y-m-d H:i:s');

        //将数据插入到表中
        if(false !== self::insertTeacher($body)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   AdminLog::getAdminInfo()->id  ,
                'module_name'    =>  'Teacher' ,
                'route_url'      =>  'admin/teacher/doInsertTeacher' , 
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
     * @param  descriptsion    删除老师的方法
     * @param  参数说明         body包含以下参数[
     *      teacher_id   讲师或教务id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function doDeleteTeacher($body=[]) {

        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断讲师或教务id是否合法
        if(!isset($body['teacher_id']) || empty($body['teacher_id']) || $body['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }

        //追加更新时间
        $data = [
            'is_del'     => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];

        //根据讲师或教务id更新删除状态
        if(false !== self::where('id',$body['teacher_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   AdminLog::getAdminInfo()->id  ,
                'module_name'    =>  'Teacher' ,
                'route_url'      =>  'admin/teacher/doDeleteTeacher' , 
                'operate_method' =>  'delete' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '删除成功'];
        } else {
            return ['code' => 203 , 'msg' => '删除失败'];
        }
    }

    /*
     * @param  descriptsion    推荐老师的方法
     * @param  参数说明         body包含以下参数[
     *     is_recommend   是否推荐(1代表推荐,2代表不推荐)
     *     teacher_id     讲师或教务id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function doRecommendTeacher($body=[]) {

        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断讲师或教务id是否合法
        if(!isset($body['teacher_id']) || empty($body['teacher_id']) || $body['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }

        //追加更新时间
        $data = [
            'is_recommend' => $body['is_recommend'] == 1 ? 1 : 0 ,
            'update_at'    => date('Y-m-d H:i:s')
        ];

        //根据讲师或教务id更新推荐状态
        if(false !== self::where('id',$body['teacher_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   AdminLog::getAdminInfo()->id  ,
                'module_name'    =>  'Teacher' ,
                'route_url'      =>  'admin/teacher/doRecommendTeacher' , 
                'operate_method' =>  'delete' ,
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
