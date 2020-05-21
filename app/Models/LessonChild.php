<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonChild extends Model {

	//指定别的表名
    public $table = 'ld_lesson_childs';


    protected $fillable = [
        'id',
    	'admin_id',
        'lesson_id',
        'pid',
        'name',
        'description',
        'category', 
        'url',
        'size',
        'is_free'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];

    public function videos() {
        return $this->belongsToMany('App\Models\Video', 'lesson_videos', 'child_id');
    }
}

