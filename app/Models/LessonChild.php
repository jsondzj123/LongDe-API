<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonChild extends Model {

	//指定别的表名
    public $table = 'lesson_childs';


    protected $fillable = [
        'id',
    	'admin_id',
        'lesson_id',
        'pid',
        'name',
        'description',
        'category', 
        'url',
        'size',
        'is_free'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];

    public function childs() {
        return $this->id;
    }
}

