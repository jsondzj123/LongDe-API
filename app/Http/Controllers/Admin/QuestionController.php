<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;

class QuestionController extends Controller {
    /*
     * @param  description   添加题库科目的方法
     * @param  参数说明       body包含以下参数[
     *     bank_id         题库id
     *     subject_name    科目名称
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
     */
    public function doInsertSubject() {
        //获取提交的参数
        try{
            $data = Subject::doInsertSubject(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '添加成功' , 'data' => ['subject_id' => $data['data']]]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   更改题库科目的方法
     * @param  参数说明       body包含以下参数[
     *     subject_id   科目id
     *     subject_name 题库科目名称
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
     */
    public function doUpdateSubject() {
        //获取提交的参数
        try{
            $data = Subject::doUpdateSubject(self::$accept_data);
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
     * @param  descriptsion    删除题库科目的方法
     * @param  参数说明         body包含以下参数[
     *      subject_id   科目id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public function doDeleteSubject(){
        //获取提交的参数
        try{
            $data = Subject::doDeleteSubject(self::$accept_data);
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
     * @param  descriptsion    获取题库科目列表
     * @param  参数说明         body包含以下参数[
     *     bank_id   题库id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public function getSubjectList(){
        //获取提交的参数
        try{
            $data = Subject::getSubjectList(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取题库科目列表成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
