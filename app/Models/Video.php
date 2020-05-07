<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model {
      
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id', 'subject_id', 'name', 'category', 'url', 'size', 'status'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'is_del',
        'is_forbid' ,
        'created_at',
        'updated_at'
    ];


    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }


}

