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
        'is_free'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid',
        'size',
        'start_at',
        'end_at'
    ];

    public function getUrlAttribute($value) {
        if ($value) {
            $photos = json_decode($value, true);
            return $photos;
        }
        return $value;
    }
        
    public function videos() {
        return $this->belongsToMany('App\Models\Video', 'ld_lesson_videos', 'child_id');
    }
}

