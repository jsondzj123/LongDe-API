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
        $return = self::where($where)->get()->toArray();
        return $return;
    }





}
