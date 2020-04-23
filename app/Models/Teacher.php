<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model {
    //指定别的表名
    public $table = 'ld_statistics_teacher';
    //时间戳设置
    public $timestamps = false;

   /*
        * @param  descriptsion 讲师统计列表查询
        * @param  $schoolid     分校id
        * @param  $teachername  讲师姓名
        * @param  $teacherphone  讲师手机号
        * @param  $date  时间
        * @param  author  苏振文
        * @param  ctime   2020/4/22 10:21
        * return  array
        */
    public static function AllList($schoolid,$teachername,$teacherphone,$date) {
        $where=[];
        if($schoolid != ''){
            $where['school_id'] = $schoolid;
        }
        if($teachername != ''){
            $where['teacher_name'] = $teachername;
        }
        if($teacherphone != ''){
            $where['teacher_phone'] = $teacherphone;
        }
        //先查询缓存 再查表

        $return = self::where($where)->get()->toArray();
        //循环查询课时 连表
        foreach ($return as $k=>&$v){

        }
        //存入缓存中
        return $return;
    }
    /*
         * @param  descriptsion 讲师统计添加
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/22 16:44
         * return  array
         */
    public static function AddTeacher($schoolid,$teachername,$teacherphone,$date){
          if($schoolid == '' ||$teachername == '' || $teacherphone == ''){

          }
          self::toArray();
    }





}
