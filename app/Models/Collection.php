<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubjectLesson;
use App\Tools\CurrentAdmin;

class Collection extends Model {

    //指定别的表名
    public $table = 'ld_collections';

    protected $fillable = [
        'lesson_id',
        'student_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del'
    ];

    public function lessons() {
        return $this->belongsToMany('App\Models\Lesson', 'ld_lesson_methods');
    }
}
