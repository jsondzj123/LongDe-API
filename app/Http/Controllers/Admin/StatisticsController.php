<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\LessonTeacher;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectLesson;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller {
   /*
        * @param  学员统计
        * @param  school_id  分校id
        * @param  reg_source  0官网1手机端2线下
        * @param  enrill_status  0未消费1消费
        * @param  real_name  姓名
        * @param  phone  手机号
        * @param  statr_time  开始时间
        * @param  end_time  结束时间
        * @param  type  1统计表2趋势图
        * @param  num  每页条数
        * @param  author  苏振文
        * @param  ctime   2020/5/7 11:19
        * return  array
        */
   public function StudentList(){
       $data = self::$accept_data;
       $data['num'] = isset($data['num'])?$data['num']:20;
       $stime = (!empty($data['state_time']))?$data['state_time']:date('Y-m-d');
       $etime = ((!empty($data['end_time']))?$data['end_time']:date('Y-m-d'));
       $statetime = $stime." 00:00:00";
       $endtime = $etime." 23:59:59";
       $studentList = Student::select('ld_student.phone','ld_student.real_name','ld_student.create_at','ld_student.reg_source','ld_student.enroll_status','ld_school.name')
           ->leftJoin('ld_school','ld_school.id','=','ld_student.school_id')
           ->where(['ld_student.is_forbid'=>1,'ld_school.is_del'=>1,'ld_school.is_forbid'=>1])
           ->where(function($query) use ($data) {
               //分校
               if(!empty($data['school_id'])&&$data['school_id'] != ''){
                   $query->where('ld_student.school_id',$data['school_id']);
               }
               //来源
               if(!empty($data['reg_source'])&&$data['reg_source'] != ''){
                   $query->where('ld_student.reg_source',$data['reg_source']);
               }
               //用户类型
               if(!empty($data['enroll_status'])&&$data['enroll_status'] != ''){
                   $query->where('ld_student.enrioll_status',$data['enroll_status']);
               }
               //用户姓名
               if(!empty($data['real_name'])&&$data['real_name'] != ''){
                   $query->where('ld_student.real_name','like','%'.$data['real_name'].'%');
               }
               //用户手机号
               if(!empty($data['phone'])&&$data['phone'] != ''){
                   $query->where('ld_student.phone','like','%'.$data['phone'].'%');
               }
           })
           ->whereBetween('ld_student.create_at', [$statetime, $endtime])
           ->paginate($data['num']);
       //根据时间将用户分类查询总数
       $website = 0; //官网
       $offline = 0; //线下
       $mobile = 0; //手机端
       $count = 0;  //总人数
       if(!empty($studentList)){
           foreach ($studentList as $k=>$v){
               if($v['reg_source'] == 0){
                   $website++;
               }
               if($v['reg_source'] == 1){
                   $mobile++;
               }
               if($v['reg_source'] == 2){
                   $offline++;
               }
               $count++;
           }
       }
       if($data['type'] == 2){
           //根据时间分组，查询出人数 ，时间列表
           $lists = Student::select(DB::raw("date_format(ld_student.create_at,'%Y-%m-%d') as time"),DB::raw('count(*) as num'))
               ->leftJoin('ld_school','ld_school.id','=','ld_student.school_id')
               ->where(['ld_student.is_forbid'=>1])
               ->where(function($query) use ($data) {
                   //分校
                   if(!empty($data['school_id'])&&$data['school_id'] != ''){
                       $query->where('ld_student.school_id',$data['school_id']);
                   }
                   //来源
                   if(!empty($data['reg_source'])&&$data['reg_source'] != ''){
                       $query->where('ld_student.reg_source',$data['reg_source']);
                   }
                   //用户类型
                   if(!empty($data['enroll_status'])&&$data['enroll_status'] != ''){
                       $query->where('ld_student.enrioll_status',$data['enroll_status']);
                   }
                   //用户姓名
                   if(!empty($data['real_name'])&&$data['real_name'] != ''){
                       $query->where('ld_student.real_name','like','%'.$data['real_name'].'%');
                   }
                   //用户手机号
                   if(!empty($data['phone'])&&$data['phone'] != ''){
                       $query->where('ld_student.phone','like','%'.$data['phone'].'%');
                   }
               })
               ->whereBetween('ld_student.create_at', [$statetime, $endtime])
               ->groupBy(DB::raw("date_format(ld_student.create_at,'%Y-%m-%d')"))
               ->get()->toArray();
           //循环出所有日期
           $stimestamp = strtotime($stime);
           $etimestamp = strtotime($etime);
           // 计算日期段内有多少天
           $days = ($etimestamp-$stimestamp)/86400+1;
           $arr=[];
           for($i=0; $i<$days; $i++) {
               $arr[] =['time'=> date('Y-m-d', $stimestamp + (86400 * $i)),'num'=>0];
           }
           //数组处理
           $xlen = [];
           $ylen = [];
           if(!empty($lists)){
               foreach ($arr as $k=>&$v){
                   foreach ($lists as $ks=>$vs){
                       if($v['time'] == $vs['time']){
                           $v['num'] = $vs['num'];
                       }
                   }
                   $xlen[]=$v['time'];
                   $ylen[]=$v['num'];
               }
           }else{
               foreach ($arr as $k=>&$v){
                   $xlen[]=$v['time'];
                   $ylen[]=$v['num'];
               }
           }
           $studentList=[
               'xlen'=>$xlen,
               'ylen'=>$ylen
           ];
       }
       return response()->json(['code'=>200,'msg'=>'获取成功','data'=>$studentList,'website'=>$website,'offline'=>$offline,'mobile'=>$mobile,'count'=>$count]);
   }
   /*
        * @param  课时统计
        * @param  school_id  分校id
        * @param  real_name  讲师姓名
        * @param  phone  讲师手机号
        * @param  statr_time  开始时间
        * @param  end_time 结束时间
        * @param  author  苏振文
        * @param  ctime   2020/5/7 17:03
        * return  array
        */
   public function TeacherList(){
       $data = self::$accept_data;
       $data['num'] = isset($data['num'])?$data['num']:20;
       $stime = (!empty($data['state_time']))?$data['state_time']:date('Y-m-d');
       $etime = ((!empty($data['end_time']))?$data['end_time']:date('Y-m-d'));
       $statetime = $stime." 00:00:00";
       $endtime = $etime." 23:59:59";
       $teacher = Lecturer::select('ld_lecturer_educationa.id','ld_lecturer_educationa.real_name','ld_lecturer_educationa.phone','ld_lecturer_educationa.number','ld_school.name')
            ->leftJoin('ld_school','ld_school.id','=','ld_lecturer_educationa.school_id')
            ->where(function($query) use ($data) {
                //分校
                if(!empty($data['school_id'])&&$data['school_id'] != ''){
                    $query->where('ld_lecturer_educationa.school_id',$data['school_id']);
                }
                //用户姓名
                if(!empty($data['real_name'])&&$data['real_name'] != ''){
                    $query->where('ld_lecturer_educationa.real_name','like','%'.$data['real_name'].'%');
                }
                //用户手机号
                if(!empty($data['phone'])&&$data['phone'] != ''){
                    $query->where('ld_lecturer_educationa.phone','like','%'.$data['phone'].'%');
                }
            })
            ->where(['ld_lecturer_educationa.type'=>2,'ld_lecturer_educationa.is_del'=>0,'ld_lecturer_educationa.is_forbid'=>0])
            ->whereBetween('ld_lecturer_educationa.create_at', [$statetime, $endtime])
            ->orderBy('ld_lecturer_educationa.id','desc')
            ->paginate($data['num']);
       $num = Lecturer::where(['type'=>2,'is_del'=>0,'is_forbid'=>0])->sum('number');
       return response()->json(['code'=>200,'msg'=>'获取成功','data'=>$teacher,'count'=>$num]);
   }

   /*
        * @param  讲师授课详情
        * @param  id    讲师授课详情
        * @param  author  苏振文
        * @param  ctime   2020/5/8 14:26
        * return  array
        */
   public function TeacherClasshour(){
        //讲师关联课程  lesson_teachers
        //课程关联科目id  subject_lessons
        //科目表  subject
        //课程表  lessons
       //根据讲师 查询所有的课程  根据课程 查询科目
       $id = $_POST['id'];
       $lesson = LessonTeacher::where(['teacher_id'=>$id])->get()->toArray();
       foreach ($lesson as $k=>&$v){
           //课程信息
          $lessons = Lesson::where('id',$v['lesson_id'])->first()->toArray();
          $subid = SubjectLesson::where('lesson_id',$v['lesson_id'])->first()->toArray();
          //学科信息
          $subject = Subject::where('id',$subid['subject_id'])->first()->toArray();
          if($subject['pid'] != 0){
              $subjectOne = Subject::where('id',$subject['pid'])->first()->toArray();
          }
          $v['lesson_name'] = $lessons['title'];
          $v['subject_name'] = $subjectOne['name'];
          $v['subject_to_name'] = $subject['name'];
       }
       print_r($lesson);die;
   }






   /*
        * @param  直播统计
        * @param
        * @param  author  苏振文
        * @param  ctime   2020/5/8 14:50
        * return  array
        */
   public function LiveList(){
       $data=[
           'school_id'=>isset($_POST['school_id'])?$_POST['school_id']:'',
           'real_name'=>isset($_POST['real_name'])?$_POST['real_name']:'',
           'phone'=>isset($_POST['phone'])?$_POST['phone']:'',
           'state_time'=>isset($_POST['state_time'])?$_POST['state_time']:'',
           'end_time'=>isset($_POST['end_time'])?$_POST['end_time']:'',
           'num'=>isset($_POST['num'])?$_POST['num']:20
       ];
   }
   /*
        * @param  直播详情
        * @param  $user_id     参数
        * @param  author  苏振文
        * @param  ctime   2020/5/8 20:24
        * return  array
        */
   public function LiveDetails(){
   }
}
