<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrolment;

class StudentController extends Controller {
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
     * @param ctime     2020-04-28
     * return string
     */
    public function doInsertStudent() {
        //获取提交的参数
        try{
            $data = Student::doInsertStudent(self::$accept_data);
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
     * @param  description   更新学员的方法
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
     * @param ctime     2020-04-28
     * return string
     */
    public function doUpdateStudent() {
        //获取提交的参数
        try{
            $data = Student::doUpdateStudent(self::$accept_data);
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
     * @param  descriptsion    根据学员id获取详细信息
     * @param  参数说明         body包含以下参数[
     *     student_id   学员id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-27
     * return  array
     */
    public function getStudentInfoById(){
        //获取提交的参数
        try{
            $data = Student::getStudentInfoById(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取学员信息成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  descriptsion    账号启用/禁用方法
     * @param  参数说明         body包含以下参数[
     *      student_id   学员id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-28
     */
    public function doForbidStudent(){
        //获取提交的参数
        try{
            $data = Student::doForbidStudent(self::$accept_data);
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
     * @param  description   学员报名的方法
     * @param  参数说明       body包含以下参数[
     *     student_id     学员id
     *     parent_id      学科分类id
     *     lession_id     课程id
     *     lession_price  课程原价
     *     student_price  学员价格
     *     payment_type   付款类型
     *     payment_method 付款方式
     *     payment_fee    付款金额
     * ]
     * @param author    dzj
     * @param ctime     2020-04-28
     * return string
     */
    public function doStudentEnrolment(){
        //获取提交的参数
        try{
            $data = Enrolment::doStudentEnrolment(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '报名成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
