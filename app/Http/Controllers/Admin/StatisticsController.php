<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class StatisticsController extends Controller {
   /*
        * @param  学员统计
        * @param  $user_id     参数
        * @param  author  苏振文
        * @param  ctime   2020/5/7 11:19
        * return  array
        */
   public function StudentList(){
       $arr=[
           'school_id'=>$_POST['school_id'],
           'reg_source'=>$_POST['reg_source'],
           'enrill_status'=>$_POST['enrill_status'],
           'real_name'=>$_POST['real_name'],
           'phone'=>$_POST['phone'],
           'date'=>$_POST['date'],
           'type'=>$_POST['type']
       ];
   }
}
