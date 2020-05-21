<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTeacher extends Model {

	    //指定别的表名
    public $table = 'ld_lesson_teachers';

    public static function getInfoById($id){
       // $info = self::get($id);
        $info = self::where(['id'=>$id])->first();
        return $info;
    }


}

