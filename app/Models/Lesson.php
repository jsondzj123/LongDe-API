<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubjectLesson;

class Lesson extends Model {

    //指定别的表名
    public $table      = 'ld_lessons';

    protected $fillable = [
        'admin_id',
    	'title',
        'keyword',
        'cover',
        'price',
        'favorable_price',
        'method', 
        'teacher_id',
        'description',
        'introduction',
        'buy_num',
        'ttl',
        'status',
        'subject_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];

    public function getUrlAttribute($value) {
        if ($value) {
            $photos = json_decode($value, true);
            foreach ($photos as $k => $v) {
                if (!empty($v) && strpos($v, 'http://') === false && strpos($v, 'https://') === false) {
                    $photos[$k] = $v;
                }
            }
            return $photos;
        }
        return $value;
    }

    public function teachers() {
        return $this->belongsToMany('App\Models\Teacher', 'lesson_teachers');
    }

    public function subjects() {
        return $this->belongsToMany('App\Models\Subject', 'subject_lessons');
    }

    public function schools() {
        return $this->belongsTo('App\Models\LessonSchool');
    }
}
