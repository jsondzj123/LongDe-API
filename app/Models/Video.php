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
        'status',
        'updated_at'
    ];


    protected $appends = ['subject_id', 'subject_first_name', 'subject_second_name'];

    public function getSubjectFirstNameAttribute($value) {
        $subjects = $this->belongsToMany('App\Models\Subject', 'ld_subject_videos')->where('pid', 0)->first();
        if(!empty($subjects)){
            $name = $subjects['name'];
        }else{
            $name = '';
        }
        return $name;
    }

    public function getSubjectSecondNameAttribute($value) {
        $subjects = $this->belongsToMany('App\Models\Subject', 'ld_subject_videos')->where('pid', '!=', 0)->first();
        if(!empty($subjects)){
            $name = $subjects['name'];
        }else{
            $name = '';
        }
        return $name;
    }

    public function getSubjectIdAttribute($value)
    {
        return $this->belongsToMany('App\Models\Subject', 'ld_subject_videos')->pluck('id');
    }

    public function subjects() {
        return $this->belongsToMany('App\Models\Subject', 'ld_subject_videos')->withTimestamps();
    }

}

