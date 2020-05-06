<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectLesson extends Model {

    protected $fillable = [
    	'subject_id',
        'lesson_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

