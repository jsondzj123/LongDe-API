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
}

