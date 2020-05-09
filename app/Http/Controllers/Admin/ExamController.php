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
}
