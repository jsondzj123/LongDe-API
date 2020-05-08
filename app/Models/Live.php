<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Live extends Model {

    protected $fillable = [
        'name',
        'cover',
        'describe',
        'url', 
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];
}

