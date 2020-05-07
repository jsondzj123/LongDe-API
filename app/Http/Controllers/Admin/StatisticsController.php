<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;

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
       $data=[
           'school_id'=>isset($_POST['school_id'])?$_POST['school_id']:'',
           'reg_source'=>isset($_POST['reg_source'])?$_POST['reg_source']:'',
           'enroll_status'=>isset($_POST['enroll_status'])?$_POST['enroll_status']:'',
           'real_name'=>isset($_POST['real_name'])?$_POST['real_name']:'',
           'phone'=>isset($_POST['phone'])?$_POST['phone']:'',
           'state_time'=>isset($_POST['state_time'])?$_POST['state_time']:'',
           'end_time'=>isset($_POST['end_time'])?$_POST['end_time']:'',
           'type'=>isset($_POST['type'])?$_POST['type']:'',
           'num'=>isset($_POST['num'])?$_POST['num']:20
       ];
       $statetime = (!empty($data['state_time']))?$data['state_time']:"1999-01-01 12:12:12";
       $endtime = (!empty($data['end_time']))?$data['end_time']:"2999-01-01 12:12:12";
       if($data['type'] == 1){
           $studentList = Student::select('ld_student.phone','ld_student.real_name','ld_student.create_at','ld_student.reg_source','ld_student.enroll_status','ld_school.name')
               ->leftJoin('ld_school','ld_school.id','=','ld_student.school_id')
               ->where(['ld_student.is_forbid'=>1,'ld_school.is_del'=>1,'ld_school.is_forbid'=>1])
               ->where(function($query) use ($data) {
                   //分校
                   if($data['school_id'] != ''){
                       $query->where('ld_student.school_id',$data['school_id']);
                   }
                   //来源
                   if($data['reg_source'] != ''){
                       $query->where('ld_student.reg_source',$data['reg_source']);
                   }
                   //用户类型
                   if($data['enroll_status'] != ''){
                       $query->where('ld_student.enrioll_status',$data['enroll_status']);
                   }
                   //用户姓名
                   if($data['real_name'] != ''){
                       $query->where('ld_student.real_name','like','%'.$data['real_name'].'%');
                   }
                   //用户手机号
                   if($data['phone'] != ''){
                       $query->where('ld_student.phone','like','%'.$data['phone'].'%');
                   }
               })
               ->whereBetween('ld_student.create_at', [$statetime, $endtime])
               ->paginate($data['num']);;
           //根据时间将用户分类查询总数
           $website = 0; //官网
           $offline = 0; //线下
           $mobile = 0; //手机端
           $count = 0;  //总人数
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
           return response()->json(['code'=>200,'msg'=>'获取成功','data'=>$studentList,'website'=>$website,'offline'=>$offline,'mobile'=>$mobile,'count'=>$count]);
       }else{
           //暂时不知道怎么写

       }
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

   }
}
