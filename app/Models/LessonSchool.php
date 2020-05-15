<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubjectLesson;

class LessonSchool extends Model {

    protected $fillable = [
        'admin_id',
        'school_id',
        'lesson_id',
    	// 'title',
     //    'keyword',
     //    'cover',
     //    'price',
     //    'favorable_price',
     //    'method', 
     //    'description',
     //    'introduction',
     //    'buy_num',
     //    'ttl',
     //    'status',
     //    'url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];


    public function lesson() {
        return $this->belongsTo('App\Models\Lesson');
    }


    
}
