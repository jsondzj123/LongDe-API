<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;

class TeacherController extends Controller {
    /*
     * @param  description   添加讲师教务的方法
     * @param  参数说明       body包含以下参数[
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
     * @param author    dzj
     * @param ctime     2020-04-25
     */
    public function doInsertTeacher() {
        //获取提交的参数
        try{
            $data = Teacher::doInsertTeacher(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '添加成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   更改讲师教务的方法
     * @param  参数说明       body包含以下参数[
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
     *     teacher_id   老师id
     * ]
     * @param author    dzj
     * @param ctime     2020-04-25
     */
    public function doUpdateTeacher() {
        //获取提交的参数
        try{
            $data = Teacher::doUpdateTeacher(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '更改成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  descriptsion    删除老师的方法
     * @param  参数说明         body包含以下参数[
     *      teacher_id   讲师或教务id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     */
    public function doDeleteTeacher(){
        //获取提交的参数
        try{
            $data = Teacher::doDeleteTeacher(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '删除成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  descriptsion    推荐老师的方法
     * @param  参数说明         body包含以下参数[
     *      is_recommend   是否推荐(1代表推荐,2代表不推荐)
     *      teacher_id   讲师或教务id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     */
    public function doRecommendTeacher(){
        //获取提交的参数
        try{
            $data = Teacher::doRecommendTeacher(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '操作成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   根据讲师或教务id获取详细信息
     * @param  参数说明       body包含以下参数[
     *     teacher_id   讲师或教务id
     * ]
     * @param author    dzj
     * @param ctime     2020-04-25
     */
    public function getTeacherInfoById(){
        //获取提交的参数
        try{
            $data = Teacher::getTeacherInfoById(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取老师信息成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   讲师或教务列表
     * @param  参数说明       body包含以下参数[
     *     real_name   讲师或教务姓名
     *     type        老师类型(1代表教务,2代表讲师)
     * ]
     * @param author    dzj
     * @param ctime     2020-04-25
     */
    public function getTeacherList(){
        //获取提交的参数
        try{
            $data = Teacher::getTeacherList(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取老师列表成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}