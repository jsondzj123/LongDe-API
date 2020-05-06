<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;

class Exam extends Model {
    //指定别的表名
    public $table      = 'ld_question_exam';
    //时间戳设置
    public $timestamps = false;
}
