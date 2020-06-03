<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonChild extends Model {

	//指定别的表名
    public $table = 'ld_lesson_childs';


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
    
    public function videos() {
        return $this->belongsToMany('App\Models\Video', 'ld_lesson_videos', 'child_id');
    }
}

