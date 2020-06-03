<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Live extends Model {

    //指定别的表名
    public $table = 'ld_lives';

    protected $fillable = [
        'admin_id',
        'name',
        'description',
        'is_use',
    ];

    protected $hidden = [
        'subject_id',
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];

    protected $appends = ['is_use'];

    public function getIsUseAttribute($value) {
        $num = LessonLive::where('live_id', $this->id)->count();
        if($num > 0){
            return 1;
        }
        return  0;
    }

    public function admin() {
        return $this->belongsTo('App\Models\Admin', 'admin_id');
    }

    public function lessons() {
        return $this->belongsToMany('App\Models\Lesson', 'ld_lesson_lives')->withTimestamps();
    }

    public function subjects() {
        return $this->belongsToMany('App\Models\Subject', 'ld_subject_lives')->withTimestamps();
    }
}

