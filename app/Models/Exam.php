<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;
use App\Models\ExamOption;
use App\Models\QuestionSubject;

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
     *     is_publish      是否发布(1代表发布,0代表未发布)
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
                'is_publish'    =>  isset($body['is_publish']) && $body['is_publish'] > 0 ? 1 : 0,
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
                'is_publish'    =>  isset($body['is_publish']) && $body['is_publish'] > 0 ? 1 : 0,
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
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doInsertExam' , 
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
     * @param  descriptsion    删除试题的方法
     * @param  参数说明         body包含以下参数[
     *      exam_id    试题id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-11
     * return  array
     */
    public static function doDeleteExam($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断试题id是否合法
        if(!isset($body['exam_id']) || empty($body['exam_id'])){
            return ['code' => 202 , 'msg' => '试题id不合法'];
        }

        //追加更新时间
        $data = [
            'is_del'     => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
        $exam_id  = explode(',',$body['exam_id']);

        //根据试题id更新删除状态
        if(false !== self::whereIn('id',$exam_id)->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doDeleteExam' , 
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
     * @param  descriptsion    发布试题的方法
     * @param  参数说明         body包含以下参数[
     *      exam_id    试题id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-11
     * return  array
     */
    public static function doPublishExam($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断试题id是否合法
        if(!isset($body['exam_id']) || empty($body['exam_id'])){
            return ['code' => 202 , 'msg' => '试题id不合法'];
        }

        //追加更新时间
        $data = [
            'is_publish' => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
        $exam_id  = explode(',',$body['exam_id']);

        //根据试题id更新删除状态
        if(false !== self::whereIn('id',$exam_id)->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doPublishExam' , 
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
    
    /*
     * @param  descriptsion    获取试题列表
     * @param  参数说明         body包含以下参数[
     *     bank_id         题库id(必传)
     *     subject_id      科目id(非必传)
     *     type            试题类型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)(非必传)
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
    public static function getExamList($body=[]) {
        //每页显示的条数
        $pagesize = isset($body['pagesize']) && $body['pagesize'] > 0 ? $body['pagesize'] : 20;
        $page     = isset($body['page']) && $body['page'] > 0 ? $body['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        
        //判断题库id是否为空和合法
        if(!isset($body['bank_id']) || empty($body['bank_id']) || $body['bank_id'] <= 0){
            return ['code' => 202 , 'msg' => '题库id不合法'];
        }
        
        //判断试题类型是否为空和合法
        if(!isset($body['type']) || empty($body['type']) || $body['type'] <= 0 || !in_array($body['type'] , [1,2,3,4,5,6,7])){
            $body['type']   =   1;
        }
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
        
        //判断科目id是否为空和合法
        if(!isset($body['subject_id']) || empty($body['subject_id']) || $body['subject_id'] <= 0){
            //根据题库的Id获取最新科目信息
            $subject_info = QuestionSubject::select("id")->where("admin_id" , $admin_id)->where("bank_id" , $body['bank_id'])->where("is_del" , 0)->orderByDesc('create_at')->first();
            $body['subject_id']  = $subject_info->id ? $subject_info->id : 0;
        }

        //获取试题的总数量
        $exam_count = self::where(function($query) use ($body){
            //获取后端的操作员id
            $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;
            $query->where('bank_id' , '=' , $body['bank_id'])->where("subject_id" , "=" , $body['subject_id'])->where("type" , $body['type'])->where("parent_id" , 0)->where('is_del' , '=' , 0)->where('admin_id' , '=' , $admin_id);
            
            //判断审核状态是否为空和合法
            if(isset($body['is_publish']) && in_array($body['is_publish'] , [1,0])){
                $query->where('is_publish' , '=' , $body['is_publish']);
            }
        
            //判断章id是否为空和合法
            if(isset($body['chapter_id']) && !empty($body['chapter_id']) && $body['chapter_id'] > 0){
                $query->where('chapter_id' , '=' , $body['chapter_id']);
            }
            
            //判断节id是否为空和合法
            if(isset($body['joint_id']) && !empty($body['joint_id']) && $body['joint_id'] > 0){
                $query->where('joint_id' , '=' , $body['joint_id']);
            }
            
            //判断考点id是否为空和合法
            if(isset($body['point_id']) && !empty($body['point_id']) && $body['point_id'] > 0){
                $query->where('point_id' , '=' , $body['point_id']);
            }
            
            //判断试题难度是否为空和合法
            if(isset($body['item_diffculty']) && !empty($body['item_diffculty']) && in_array($body['item_diffculty'] , [1,2,3])){
                $query->where('item_diffculty' , '=' , $body['item_diffculty']);
            }
            
            //判断试题名称是否为空
            if(isset($body['exam_name']) && !empty(isset($body['exam_name']))){
                $query->where('exam_content','like',$body['exam_name'].'%');
            }
        })->count();
        
        if($exam_count > 0){
            //获取试题列表
            $exam_list = self::select('id as exam_id','exam_content','is_publish')->where(function($query) use ($body){
                //题库id
                $query->where('bank_id' , '=' , $body['bank_id'])->where("subject_id" , "=" , $body['subject_id'])->where("type" , $body['type'])->where("parent_id" , 0);
                
                //删除状态
                $query->where('is_del' , '=' , 0);

                //获取后端的操作员id
                $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

                //操作员id
                $query->where('admin_id' , '=' , $admin_id);
                
                //判断审核状态是否为空和合法
                if(isset($body['is_publish']) && in_array($body['is_publish'] , [1,0])){
                    $query->where('is_publish' , '=' , $body['is_publish']);
                }
                
                //判断章id是否为空和合法
                if(isset($body['chapter_id']) && !empty($body['chapter_id']) && $body['chapter_id'] > 0){
                    $query->where('chapter_id' , '=' , $body['chapter_id']);
                }

                //判断节id是否为空和合法
                if(isset($body['joint_id']) && !empty($body['joint_id']) && $body['joint_id'] > 0){
                    $query->where('joint_id' , '=' , $body['joint_id']);
                }

                //判断考点id是否为空和合法
                if(isset($body['point_id']) && !empty($body['point_id']) && $body['point_id'] > 0){
                    $query->where('point_id' , '=' , $body['point_id']);
                }

                //判断试题难度是否为空和合法
                if(isset($body['item_diffculty']) && !empty($body['item_diffculty']) && in_array($body['item_diffculty'] , [1,2,3])){
                    $query->where('item_diffculty' , '=' , $body['item_diffculty']);
                }

                //判断试题名称是否为空
                if(isset($body['exam_name']) && !empty(isset($body['exam_name']))){
                    $query->where('exam_content','like',$body['exam_name'].'%');
                }
            })->orderByDesc('create_at')->offset($offset)->limit($pagesize)->get();
            return ['code' => 200 , 'msg' => '获取试题列表成功' , 'data' => ['exam_list' => $exam_list , 'total' => $exam_count , 'pagesize' => $pagesize , 'page' => $page]];
        }
        return ['code' => 200 , 'msg' => '获取试题列表成功' , 'data' => ['exam_list' => [] , 'total' => 0 , 'pagesize' => $pagesize , 'page' => $page]];
    }
}
