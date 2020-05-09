<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;

class ExamController extends Controller {
    /*
     * @param  description   增加试题的方法
     * @param  参数说明       body包含以下参数[
     *     subject_id      科目id
     *     bank_id         题库id
     *     papers_name     试卷名称
     *     diffculty       试题类型(1代表真题,2代表模拟题,3代表其他)
     *     papers_time     答题时间
     *     area            所属区域
     *     cover_img       封面图片
     *     content         试卷描述
     *     type            选择题型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)
     * ]
     * @param author    dzj
     * @param ctime     2020-05-08
     * return string
     */
    public function doInsertExam() {
        //获取提交的参数
        try{
            $data = Exam::doInsertExam(self::$accept_data);
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
     * @param  descriptsion    获取试题列表
     * @param  参数说明         body包含以下参数[
     *     bank_id         题库id(必传)
     *     subject_id      科目id(必传)
     *     type            试题类型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)(必传)
     *     is_publish      审核状态(非必传)
     *     chapter_id      章id(非必传)
     *     joint_id        节id(非必传)
     *     point_id        考点id(非必传)
     *     item_diffculty  试题难度(1代表简单,2代表一般,3代表困难)(非必传)
     *     exam_name       试题名称(非必传)
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-09
     * return  array
     */
    public function getExamList(){
        //获取提交的参数
        try{
            $data = Exam::getExamList(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取试题列表成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   试题公共参数列表
     * @param  author        dzj
     * @param  ctime         2020-05-09
     */
    public function getExamCommonList(){
        //试题类型
        $exam_array = [
            [
                'id'  =>  1 ,
                'name'=> '单选题'
            ] ,
            [
                'id'  =>  2 ,
                'name'=> '多选题'
            ] ,
            [
                'id'  =>  3 ,
                'name'=> '不定项'
            ],
            [
                'id'  =>  4 ,
                'name'=> '判断题'
            ] ,
            [
                'id'  =>  5 ,
                'name'=> '填空题'
            ] ,
            [
                'id'  =>  6 ,
                'name'=> '简答题'
            ],
            [
                'id'  =>  7 ,
                'name'=> '材料题'
            ]
        ];
        
        //试题难度
        $diffculty_array = [
            [
                'id'  =>  1 ,
                'name'=> '简单'
            ] ,
            [
                'id'  =>  2 ,
                'name'=> '一般'
            ] ,
            [
                'id'  =>  3 ,
                'name'=> '困难'
            ] 
        ];
        return response()->json(['code' => 200 , 'msg' => '返回数据成功' , 'data' => ['diffculty_array' => $diffculty_array , 'exam_array' => $exam_array]]);
    }
}
