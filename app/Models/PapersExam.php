<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;
use App\MOdels\Papers;
use App\Models\Exam;
use Validator;
use Illuminate\Support\Facades\Redis;
class PapersExam extends Model {
    //指定别的表名
    public $table      = 'ld_question_papers_exam';
    //时间戳设置
    public $timestamps = false;

    /*
     * @param  description   试卷选择试题添加
     * @param  参数说明       body包含以下参数[
     * 操作员id
     *  subject_id  科目id
     *  papers_id   试卷id
     *  exam_id 试题id
     *  type    试题类型
     *  grade   每题得分
     * ]
     * @param  author        zzk
     * @param  ctime         2020-05-11
     */
    public static function InsertTestPaperSelection($body=[]){
        //规则结构
        $rule = [
            'subject_id'   =>   'required|numeric' ,
            'papers_id'    =>   'required|numeric' ,
            'exam_id'      =>   'required|numeric' ,
            'type'         =>   'required|numeric' ,
            // 'joint_id'     =>   'required|numeric' ,
            // 'chapter_id'   =>   'required|numeric' ,
            'grade'        =>   'required|numeric' ,
        ];

        //信息提示
        $message = [
            'subject_id.required'   =>  json_encode(['code'=>201,'msg'=>'科目id为空']) ,
            'papers_id.required'   =>  json_encode(['code'=>201,'msg'=>'试卷id为空']) ,
            'exam_id.required'   =>  json_encode(['code'=>201,'msg'=>'试题id为空']) ,
            'type.required'   =>  json_encode(['code'=>201,'msg'=>'试题类型为空']) ,
            // 'chapter_id.required'   =>  json_encode(['code'=>201,'msg'=>'章id为空']) ,
            // 'joint_id.required'   =>  json_encode(['code'=>201,'msg'=>'节id为空']) ,
            'grade.required'   =>  json_encode(['code'=>201,'msg'=>'每题得分为空']) ,

        ];

        $validator = Validator::make($body , $rule , $message);
        if ($validator->fails()) {
            return json_decode($validator->errors()->first() , true);
        }

        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

        //将后台人员id追加
        $body['admin_id']   = $admin_id;
        $body['create_at']  = date('Y-m-d H:i:s');
        $data = [
            "subject_id" => $body['subject_id'],
            "papers_id" => $body['papers_id'],
            "exam_id" => $body['exam_id'],
            "type" => $body['type'],
            "grade" => $body['grade'],
            "admin_id" => $body['admin_id'],
            "create_at" => $body['create_at'],
        ];
        //将数据插入到表中
        $papersexam_id = self::insertGetId($data);
        if($papersexam_id && $papersexam_id > 0){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/InsertTestPaperSelection' ,
                'operate_method' =>  'insert' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '添加试题到试卷成功'];
        } else {
            return ['code' => 203 , 'msg' => '添加试题到试卷失败'];
        }
    }
    /*
     * @param  description   获取试题数据
     * @param  参数说明       body包含以下参数[
     *     type            试题类型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)
     *     papers_id       试卷id
     *     chapter_id      章id
     *     chapter_id      节id
     *     exam_name       题目名称
     *     page            页码
     * ]
     * @param author    zzk
     * @param ctime     2020-05-12
     * return string
     */
    public static function GetExam($body=[],$page =1,$limit = 10){
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->id) ? AdminLog::getAdminInfo()->id : 0;

        //每页显示的条数
        $pagesize = isset($body['pagesize']) && $body['pagesize'] > 0 ? $body['pagesize'] : 20;
        $page     = isset($body['page']) && $body['page'] > 0 ? $body['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        //获取总页码

        //获取试卷id
        $papers_id = $body['papers_id'];
        //获取章id
        $chapter_id = $body['chapter_id'];
        //获取节id
        $joint_id = $body['joint_id'];
        //获取题目名称
        $exam_name = $body['exam_name'];
        if(isset($exam_name) && isset($chapter_id) && isset($joint_id)){
            //通过条件获取所有试题
            $exam_count = Exam::where(['is_del'=>1])->orWhere('exam_content', 'like', '%'.$exam_name.'%')->orWhere('joint_id',$joint_id)->orWhere('chapter_id',$chapter_id)->count();
            $exam_list = Exam::where(['is_del'=>1])->orWhere('exam_content', 'like', '%'.$exam_name.'%')->orWhere('joint_id',$joint_id)->orWhere('chapter_id',$chapter_id)->select('id','exam_content','item_diffculty','chapter_id','joint_id')->forPage($page,$limit)->get()->toArray();
        }else{
            $exam_count = Exam::where('is_del' , '=' , 0)->count();
            $exam_list = Exam::where(['is_del'=>0])->select('id','exam_content','item_diffculty')->forPage($page,$limit)->get()->toArray();
        }

        return ['code' => 200 , 'msg' => '获取成功','data'=>['exam_list' => $exam_list , 'total' => $exam_count , 'pagesize' => $pagesize , 'page' => $page]];
    }
    /*
     * @param  description   检测试卷试题
     * @param  参数说明       body包含以下参数[
     *     type            试题类型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)
     *     papers_id       试卷id
     * ]
     * @param author    zzk
     * @param ctime     2020-05-11
     * return string
     */
    public static function GetRepetitionExam($body=[]){
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->id) ? AdminLog::getAdminInfo()->id : 0;
        //获取试卷id
        $papers_id = $body['papers_id'];
        //获取分类
        $type = $body['type'];
        if(!empty($type)){
            //通过试卷id获取该试卷下的所有试题按照分类进行搜索
            $exam = self::where(['ld_question_papers_exam.papers_id'=>$papers_id,'ld_question_papers_exam.type'=>$type,'ld_question_papers_exam.is_del'=>0])
            ->join('ld_question_exam', 'ld_question_papers_exam.exam_id', '=', 'ld_question_exam.id')
            ->select('ld_question_papers_exam.id','ld_question_papers_exam.exam_id','ld_question_exam.exam_content')
            ->get()
            ->toArray();
        }else{
            $exam = self::where(['ld_question_papers_exam.papers_id'=>$papers_id,'ld_question_papers_exam.is_del'=>0])
            ->join('ld_question_exam', 'ld_question_papers_exam.exam_id', '=', 'ld_question_exam.id')
            ->select('ld_question_papers_exam.id','ld_question_papers_exam.exam_id','ld_question_exam.exam_content')
            ->get()
            ->toArray();
        }
        $last_ages = array_column($exam,'exam_id');
        array_multisort($last_ages ,SORT_ASC,$exam);
        return ['code' => 200 , 'msg' => '获取成功','data'=>$exam];
    }
    /*
     * @param  description   选择试题
     * @param  参数说明       body包含以下参数[
     *     type            试题类型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)
     *     papers_id       试卷id
     * ]
     * @param author    zzk
     * @param ctime     2020-05-11
     * return string
     */
    public static function GetTestPaperSelection($body=[]){
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->id) ? AdminLog::getAdminInfo()->id : 0;
        //获取试卷id
        $papers_id = $body['papers_id'];
        //获取分类
        $type = $body['type'];
        if(!empty($type)){
            //通过试卷id获取该试卷下的所有试题按照分类进行搜索
            $exam = self::where(['papers_id'=>$papers_id,'type'=>$type,'is_del'=>0])->select('id','exam_id')->get()->toArray();
        }else{
            $exam = self::where(['papers_id'=>$papers_id,'is_del'=>0])->select('id','exam_id')->get()->toArray();
        }
        foreach($exam as $k => $exams){
            if(empty(Exam::where(['id'=>$exams['exam_id'],'is_del'=>0])->select('exam_content')->first()['exam_content'])){
                unset($exam[$k]);
            }else{
                $exam[$k]['exam_content'] = Exam::where(['id'=>$exams['exam_id'],'is_del'=>0])->select('exam_content')->first()['exam_content'];
            }

        }
        return ['code' => 200 , 'msg' => '获取成功','data'=>$exam];
    }
    /*
     * @param  description   软删试卷试题
     * @param  参数说明       body包含以下参数
     * [
     *     papersexam_id       试卷内试题id
     * ]
     * @param author    zzk
     * @param ctime     2020-05-11
     * return string
     */
    public static function DeleteTestPaperSelection($body=[]){
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->id) ? AdminLog::getAdminInfo()->id : 0;
        //获取试题id
        $papersexam_id = $body['papersexam_id'];

        $examOne = self::where(['id'=>$papersexam_id])->first();
        if(!$examOne){
            return ['code' => 204 , 'msg' => '参数不对'];
        }

        //追加更新时间
        $data = [
            'is_del'     => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];

        //根据题库id更新删除状态
        if(false !== self::where('id',$body['papersexam_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/DeleteTestPaperSelection' ,
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
     * @param  description   获取试卷试题详细
     * @param  参数说明       body包含以下参数
     * [
     *     exam_id       试题id
     * ]
     * @param author    zzk
     * @param ctime     2020-05-11
     * return string
     */
    public static function oneTestPaperSelection($body=[]){
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->id) ? AdminLog::getAdminInfo()->id : 0;
        //获取试题id
        $exam_id = $body['exam_id'];
        $examOne = Exam::where(['id'=>$exam_id])->first();
        if(empty($examOne)){
            $examOne = array();
        }
        return ['code' => 200 , 'msg' => '获取成功','data'=>$examOne];

    }
}
