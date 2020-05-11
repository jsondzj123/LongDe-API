<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonVideo extends Model {

	//指定别的表名
    public $table = 'lesson_videos';


    protected $fillable = [
    	'video_id',
        'child_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];

    public function videos() {
        return $this->belongsToMany('App\Models\Video', 'lesson_videos');
    }
}
