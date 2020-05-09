<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;
use App\Models\ExamOption;

class Exam extends Model {
    //指定别的表名
    public $table      = 'ld_question_exam';
    //时间戳设置
    public $timestamps = false;
    
    
    /*
     * @param  description   增加试题的方法
     * @param  参数说明       body包含以下参数[
     *     type            试题类型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)
     *     subject_id      科目id
     *     bank_id         题库id
     *     exam_id         试题id
     *     exam_content    题目内容
     *     option_list     [
     *         option_no     选项字母
     *         option_name   选项内容
     *         correct_flag  是否为正确选项(true代表是,false代表否)
     *     ]
     *     answer          题目答案
     *     text_analysis   文字解析
     *     audio_analysis  音频解析
     *     video_analysis  视频解析
     *     chapter_id      章id
     *     joint_id        节id
     *     point_id        考点id
     *     item_diffculty  试题难度(1代表简单,2代表一般,3代表困难)
     * ]
     * @param author    dzj
     * @param ctime     2020-05-08
     * return string
     */
    public static function doInsertExam($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断是否为材料题添加子类
        if(!isset($body['exam_id']) || empty($body['exam_id']) || $body['exam_id'] <= 0){
            //判断试题类型是否合法
            if(!isset($body['type']) || empty($body['type']) || !in_array($body['type'] , [1,2,3,4,5,6,7])){
                return ['code' => 202 , 'msg' => '试题类型不合法'];
            }

            //判断科目id是否合法
            if(!isset($body['subject_id']) || empty($body['subject_id']) || $body['subject_id'] <= 0){
                return ['code' => 202 , 'msg' => '科目id不合法'];
            }

            //判断题库id是否合法
            if(!isset($body['bank_id']) || empty($body['bank_id']) || $body['bank_id'] <= 0){
                return ['code' => 202 , 'msg' => '题库id不合法'];
            }
        } else {
            //判断试题类型是否合法
            if(!isset($body['type']) || empty($body['type']) || !in_array($body['type'] , [1,2,3,4,5,6])){
                return ['code' => 202 , 'msg' => '试题类型不合法'];
            }
        }
        
        //判断是否试题内容是否为空
        if(!isset($body['exam_content']) || empty($body['exam_content'])){
            return ['code' => 202 , 'msg' => '试题内容不合法'];
        }

        //判断添加的是否为材料题
        if($body['type'] < 7){
            //判断是否为(1单选题2多选题3不定项)
            if(in_array($body['type'] , [1,2,3]) && (!isset($body['option_list']) || empty($body['option_list']))){
                return ['code' => 201 , 'msg' => '试题选项为空'];
            }
            
            //判断单选题和多选题和不定项和判断题和简答题
            if(!isset($body['answer']) || empty($body['answer'])){
                return ['code' => 201 , 'msg' => '答案不能为空'];
            }

            //判断文字解析是否为空
            if(!isset($body['text_analysis']) || empty($body['text_analysis'])){
                return ['code' => 202 , 'msg' => '文字解析不合法'];
            }

            //判断是音频解析是否为空
            if(!isset($body['audio_analysis']) || empty($body['audio_analysis'])){
                return ['code' => 202 , 'msg' => '音频解析不合法'];
            }

            //判断视频解析是否为空
            if(!isset($body['video_analysis']) || empty($body['video_analysis'])){
                return ['code' => 202 , 'msg' => '视频解析不合法'];
            }

            //判断章节考点id是否合法
            if((!isset($body['chapter_id']) || empty($body['chapter_id']) || $body['chapter_id'] <= 0) || (!isset($body['joint_id']) || empty($body['joint_id']) || $body['joint_id'] <= 0) || (!isset($body['point_id']) || empty($body['point_id']) || $body['point_id'] <= 0)){
                return ['code' => 202 , 'msg' => '章节考点id不合法'];
            }

            //判断试题难度是否合法
            if(!isset($body['item_diffculty']) || empty($body['item_diffculty']) || !in_array($body['item_diffculty'] , [1,2,3])){
                return ['code' => 202 , 'msg' => '试题难度不合法'];
            }
        }
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
        
        //判断是否传递试题父级id
        if(isset($body['exam_id']) &&  $body['exam_id'] > 0){
            //试题数据组合
            $exam_arr = [
                'parent_id'     =>  $body['exam_id'] ,
                'exam_content'  =>  $body['exam_content'] ,
                'answer'        =>  $body['type'] < 7 ? $body['answer'] : '' ,
                'text_analysis' =>  $body['type'] < 7 ? $body['text_analysis'] : '' ,
                'audio_analysis'=>  $body['type'] < 7 ? $body['audio_analysis'] : '',
                'video_analysis'=>  $body['type'] < 7 ? $body['video_analysis'] : '',
                'chapter_id'    =>  $body['type'] < 7 ? $body['chapter_id'] : 0,
                'joint_id'      =>  $body['type'] < 7 ? $body['joint_id'] : 0 ,
                'point_id'      =>  $body['type'] < 7 ? $body['point_id'] : 0 ,
                'type'          =>  $body['type'] ,
                'item_diffculty'=>  $body['type'] < 7 ? $body['item_diffculty'] : 0,
                'create_at'     =>  date('Y-m-d H:i:s')
            ];
        } else {
            //试题数据组合
            $exam_arr = [
                'admin_id'      =>  $admin_id ,
                'subject_id'    =>  $body['subject_id'] ,
                'bank_id'       =>  $body['bank_id'] ,
                'exam_content'  =>  $body['exam_content'] ,
                'answer'        =>  $body['type'] < 7 ? $body['answer'] : '' ,
                'text_analysis' =>  $body['type'] < 7 ? $body['text_analysis'] : '' ,
                'audio_analysis'=>  $body['type'] < 7 ? $body['audio_analysis'] : '',
                'video_analysis'=>  $body['type'] < 7 ? $body['video_analysis'] : '',
                'chapter_id'    =>  $body['type'] < 7 ? $body['chapter_id'] : 0,
                'joint_id'      =>  $body['type'] < 7 ? $body['joint_id'] : 0 ,
                'point_id'      =>  $body['type'] < 7 ? $body['point_id'] : 0 ,
                'type'          =>  $body['type'] ,
                'item_diffculty'=>  $body['type'] < 7 ? $body['item_diffculty'] : 0,
                'create_at'     =>  date('Y-m-d H:i:s')
            ];
        }
        
        
        //将数据插入到表中
        $exam_id = self::insertGetId($exam_arr);
        if($exam_id && $exam_id > 0){
            //判断是否为(1单选题2多选题3不定项)
            if(in_array($body['type'] , [1,2,3]) && !empty($body['option_list'])){
                foreach($body['option_list'] as $k=>$v){
                    //添加试题选项
                    ExamOption::insertGetId([
                        'admin_id'    =>   $admin_id ,
                        'exam_id'     =>   $exam_id ,
                        'option_name' =>   $v['option_name'],
                        'option_no'   =>   $v['option_no'],
                        'correct_flag'=>   $v['correct_flag'],
                        'create_at'   =>   date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            
            
            
            //添加日志操作
            /*AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doInsertExam' , 
                'operate_method' =>  'insert' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);*/
            return ['code' => 200 , 'msg' => '添加成功'];
        } else {
            return ['code' => 203 , 'msg' => '添加失败'];
        }
    }
}
