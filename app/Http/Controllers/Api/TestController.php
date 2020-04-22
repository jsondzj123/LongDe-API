<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Teacher;

class TestController extends Controller {
    public function TeacherList(){
        $schoolid = isset($_POST['school_id'])?$_POST['school_id']:'';
        $teachername = isset($_POST['teachername'])?$_POST['teachername']:'';
        $teacherphone = isset($_POST['teacherphone'])?$_POST['teacherphone']:'';
        $date = isset($_POST['date'])?$_POST['date']:'';
        $list = Teacher::AllList($schoolid,$teachername,$teacherphone,$date);
        return $list;
    }
}
