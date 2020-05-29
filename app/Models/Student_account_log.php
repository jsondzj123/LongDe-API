<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class Student_account_log extends Model {
    //指定别的表名
    public $table      = 'ld_student_account_log';
    //时间戳设置
    public $timestamps = false;


}
