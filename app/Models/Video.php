<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model {
      

    public $table = 'ld_videos';
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id', 'name', 'category', 'url', 'size', 'status'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function subjects() {
        return $this->belongsToMany('App\Models\Subject', 'ld_subject_videos')->withTimestamps();
    }

}

