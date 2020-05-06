<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubjectLesson;

class Lesson extends Model {

    protected $fillable = [
    	'title',
        'keyword',
        'cover',
        'price',
        'favorable_price',
        'method', 
        'teacher_id',
        'description',
        'introduction'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function teachers() {
        return $this->belongsToMany('App\Models\Teacher', 'lesson_teachers');
    }
}
