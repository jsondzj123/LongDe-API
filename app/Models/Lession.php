<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lession extends Model {
    //指定别的表名
    public $table = 'longdeapi_lession';
    //时间戳设置
    public $timestamps = false;
}
