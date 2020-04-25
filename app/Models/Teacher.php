<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model {
    //指定别的表名
    public $table      = 'ld_lecturer_educationa';
    //时间戳设置
    public $timestamps = false;
    
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
        if(!$body['condition'] || !is_array($body['condition'])){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断讲师或教务id是否合法
        if(!isset($body['condition']['teacher_id']) || empty($body['condition']['teacher_id']) || $body['condition']['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }

        //根据id获取讲师或教务详细信息
        $teacher_info = self::where('id',$body['condition']['teacher_id'])->first()->toArray();
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
        if(!$body['condition'] || !is_array($body['condition'])){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断讲师或教务类型是否合法
        if(!isset($body['condition']['type']) || empty($body['condition']['type']) || $body['condition']['type'] <= 0 || !in_array($body['condition']['type'] , [1,2])){
            return ['code' => 202 , 'msg' => '老师类型不合法'];
        }
        
        //条件组合
        $condtion[]    =  ['type' , '=' ,$body['condition']['type']];
        
        //判断讲师或教务姓名是否为空
        if(isset($body['condition']['real_name']) && !empty($body['condition']['real_name'])){
            $condtion[]  =  ['real_name' , 'like' , '%'.$body['condition']['real_name'].'%'];
        }
        
        //根据id获取讲师或教务列表
        $teacher_list = self::where($condtion)->paginate($body['condition']['paginate']);
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
        if(!$body['data'] || !is_array($body['data'])){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断讲师或教务id是否合法
        if(!isset($body['condition']['teacher_id']) || empty($body['condition']['teacher_id']) || $body['condition']['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }
        
        //判断头像是否上传
        if(!isset($body['data']['head_icon']) || empty($body['data']['head_icon'])){
            return ['code' => 201 , 'msg' => '请上传头像'];
        }

        //判断手机号是否为空
        if(!isset($body['data']['phone']) || empty($body['data']['phone'])){
            return ['code' => 201 , 'msg' => '请输入手机号'];
        } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['data']['phone'])) {
            return ['code' => 202 , 'msg' => '手机号不合法'];
        }

        //判断姓名是否为空
        if(!isset($body['data']['real_name']) || empty($body['data']['real_name'])){
            return ['code' => 201 , 'msg' => '请输入姓名'];
        }

        //判断性别是否选择
        if(!isset($body['data']['sex']) || empty($body['data']['sex'])){
            return ['code' => 201 , 'msg' => '请选择性别'];
        } else if(!in_array($body['data']['sex'] , [1,2])) {
            return ['code' => 202 , 'msg' => '性别不合法'];
        }

        //判断描述是否为空
        if(!isset($body['data']['describe']) || empty($body['data']['describe'])){
            return ['code' => 201 , 'msg' => '请输入描述'];
        }

        //如果是讲师
        if($body['data']['type'] > 1){
            //判断学科是否选择
            if((!isset($body['data']['parent_id']) || empty($body['data']['parent_id'])) || (!isset($body['data']['child_id']) || empty($body['data']['child_id'])) || ($body['data']['child_id'] <= 0 || $body['data']['child_id'] <= 0)){
                return ['code' => 201 , 'msg' => '请选择关联学科'];
            }

            //判断详情是否为空
            if(!isset($body['data']['content']) || empty($body['data']['content'])){
                return ['code' => 201 , 'msg' => '请输入详情'];
            }
        }
        
        //将更新时间追加
        $body['data']['update_at'] = date('Y-m-d H:i:s');

        //根据讲师或教务id更新信息
        if(false !== self::where('id',$body['condition']['teacher_id'])->update($body['data'])){
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
        if(!$body['data'] || !is_array($body['data'])){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断是教师还是教务
        if(!isset($body['data']['type']) || empty($body['data']['type']) || !in_array($body['data']['type'] , [1,2])){
            return ['code' => 202 , 'msg' => '老师类型不合法'];
        } else {
            //判断头像是否上传
            if(!isset($body['data']['head_icon']) || empty($body['data']['head_icon'])){
                return ['code' => 201 , 'msg' => '请上传头像'];
            }

            //判断手机号是否为空
            if(!isset($body['data']['phone']) || empty($body['data']['phone'])){
                return ['code' => 201 , 'msg' => '请输入手机号'];
            } else if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}|^16[\d]{9}$#', $body['data']['phone'])) {
                return ['code' => 202 , 'msg' => '手机号不合法'];
            }

            //判断姓名是否为空
            if(!isset($body['data']['real_name']) || empty($body['data']['real_name'])){
                return ['code' => 201 , 'msg' => '请输入姓名'];
            }

            //判断性别是否选择
            if(!isset($body['data']['sex']) || empty($body['data']['sex'])){
                return ['code' => 201 , 'msg' => '请选择性别'];
            } else if(!in_array($body['data']['sex'] , [1,2])) {
                return ['code' => 202 , 'msg' => '性别不合法'];
            }
            
            //判断描述是否为空
            if(!isset($body['data']['describe']) || empty($body['data']['describe'])){
                return ['code' => 201 , 'msg' => '请输入描述'];
            }
            
            //如果是讲师
            if($body['data']['type'] > 1){
                //判断学科是否选择
                if((!isset($body['data']['parent_id']) || empty($body['data']['parent_id'])) || (!isset($body['data']['child_id']) || empty($body['data']['child_id'])) || ($body['data']['child_id'] <= 0 || $body['data']['child_id'] <= 0)){
                    return ['code' => 201 , 'msg' => '请选择关联学科'];
                }

                //判断详情是否为空
                if(!isset($body['data']['content']) || empty($body['data']['content'])){
                    return ['code' => 201 , 'msg' => '请输入详情'];
                }
            }
        }
        
        //将所属网校id和后台人员id追加
        $body['data']['admin_id']   = 1;
        $body['data']['school_id']  = 1;
        $body['data']['create_at']  = date('Y-m-d H:i:s');
        
        //将数据插入到表中
        if(false !== self::insertTeacher($body['data'])){
            return ['code' => 200 , 'msg' => '添加成功'];
        } else {
            return ['code' => 203 , 'msg' => '添加失败'];
        }
    }
    
    /*
     * @param  descriptsion    删除老师的方法
     * @param  参数说明         body包含以下参数[
     *     data      => [] ,
     *     condition => [
     *         teacher_id   讲师或教务id
     *     ]
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function doDeleteTeacher($body=[]) {

        //判断传过来的数组数据是否为空
        if(!$body['condition'] || !is_array($body['condition'])){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断讲师或教务id是否合法
        if(!isset($body['condition']['teacher_id']) || empty($body['condition']['teacher_id']) || $body['condition']['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }
        
        //追加更新时间
        $data = [
            'is_del'     => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];  

        //根据讲师或教务id更新删除状态
        if(false !== self::where('id',$body['condition']['teacher_id'])->update($data)){
            return ['code' => 200 , 'msg' => '删除成功'];
        } else {
            return ['code' => 203 , 'msg' => '删除失败'];
        }
    }
    
    /*
     * @param  descriptsion    推荐老师的方法
     * @param  参数说明         body包含以下参数[
     *     data      => [
     *         is_recommend   是否推荐(1代表推荐,2代表不推荐)
     *     ] ,
     *     condition => [
     *         teacher_id   讲师或教务id
     *     ]
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function doRecommendTeacher($body=[]) {

        //判断传过来的数组数据是否为空
        if((!$body['condition'] || !is_array($body['condition'])) || (!$body['data'] || !is_array($body['data']))){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断讲师或教务id是否合法
        if(!isset($body['condition']['teacher_id']) || empty($body['condition']['teacher_id']) || $body['condition']['teacher_id'] <= 0){
            return ['code' => 202 , 'msg' => '老师id不合法'];
        }
        
        //追加更新时间
        $data = [
            'is_recommend' => $body['data']['is_recommend'] == 1 ? 1 : 0 ,
            'update_at'    => date('Y-m-d H:i:s')
        ];  

        //根据讲师或教务id更新推荐状态
        if(false !== self::where('id',$body['condition']['teacher_id'])->update($data)){
            return ['code' => 200 , 'msg' => '操作成功'];
        } else {
            return ['code' => 203 , 'msg' => '操作失败'];
        }
    }
}
