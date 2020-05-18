<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Live extends Model {

    protected $fillable = [
        'admin_id',
        'subject_id',
        'name',
        'description',
        'is_use',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];

    protected $appends = ['is_use'];

    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }

    public function getIsUseAttribute($value) {
        $use = LessonLive::where('live_id', $this->id)->count();
        //dd($use);
        return  $use;
    }

    public function lessons() {
        return $this->belongsToMany('App\Models\Lesson', 'lesson_lives');
    }
}

