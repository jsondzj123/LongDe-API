<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubjectLesson;
use App\Tools\CurrentAdmin;

class Lesson extends Model {

    //指定别的表名
    public $table = 'ld_lessons';

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
        'is_forbid',
        'pivot'
    ];

    protected $appends = ['is_auth'];

    public function getIsAuthAttribute($value) {
        $user = CurrentAdmin::user();
        if(!empty($user)){
            $school = LessonSchool::where(['school_id' => $user->school_id, 'lesson_id' => $this->id])->count();
            if($school > 0){
                //授权
                return 2;
            }
            if($user->id == $this->admin_id){
                //自增
                return  1;
            }
        }
        return  0;
    }

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
        return $this->belongsToMany('App\Models\Teacher', 'ld_lesson_teachers');
    }

    public function subjects() {
        return $this->belongsToMany('App\Models\Subject', 'ld_subject_lessons');
    }

    public function methods() {
        return $this->belongsToMany('App\Models\Method', 'ld_lesson_methods');
    }

    public function schools() {
        return $this->belongsTo('App\Models\LessonSchool');
    }
}
