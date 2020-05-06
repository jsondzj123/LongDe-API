<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectLesson extends Model {

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
}

