<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChild extends Model {

        //指定别的表名
    public $table = 'live_childs';

    protected $fillable = [
        'admin_id',
        'live_id',
        'course_name',
        'account',
        'start_time',
        'end_time',
        'nickname',
        'accountIntro',
        'partner_id',
        'bid',
        'course_name',
        'start_time',
        'end_time',
        'zhubo_key',
        'admin_key',
        'user_key',
        'departmentID',
        'scenes',
        'add_time',
        'updateTime',
        'course_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];

    protected $appends = ['date', 'week', 'solt', 'time'];

    public function getDateAttribute($value) {
        return date('Y-m-d', strtotime($this->start_time));
    }

    public function getWeekAttribute($value) {
        $weekarray=array("日","一","二","三","四","五","六");
        return "星期".$weekarray[date("w", strtotime($this->start_time))];
    }

    public function getSoltAttribute($value) {
        return date('H:i', strtotime($this->start_time)).'-'.date('H:i', strtotime($this->end_time));
    }


    public function getTimeAttribute($value) {
        $no = date('a', strtotime($this->start_time));
        if ($no == 'pm'){
            return "下午";
        }else{
            return "上午";
        }
    }
}

