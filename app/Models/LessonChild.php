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

    protected $appends = [
        'course_id',
    ];

    protected $casts = [
        'is_free' => 'string',
        'category' => 'string'
    ];

    public function getUrlAttribute($value) {
        if ($value) {
            $photos = json_decode($value, true);
            return $photos;
        }
        return [];
    }

    public function getCourseIdAttribute($value)
    {
        $video = LessonVideo::where('child_id', $this->id)->first();
        if(!empty($video)){
            $course = Video::find($video['video_id']);
            if(!empty($course)){
                return $course['course_id'];
            }
        }
        return 0;
    }



    public function lives() {

        return $this->belongsToMany('App\Models\Live', 'ld_live_class_childs', 'lesson_child_id', 'live_child_id');
    }
}

