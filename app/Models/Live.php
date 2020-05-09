<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Live extends Model {

    protected $fillable = [
        'admin_id',
        'subject_id',
        'name',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];


    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }
}

