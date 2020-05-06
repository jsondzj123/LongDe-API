<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTeacher extends Model {

    public static function getInfoById($id){
       // $info = self::get($id);
        $info = self::where(['id'=>$id])->first();
        return $info;
    }


}

