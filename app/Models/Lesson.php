<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model {

    protected $fillable = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
