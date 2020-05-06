<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model {

	protected $fillable = [
    	'admin_id',
        'pid',
        'name',
        'cover',
        'description',
        'status', 
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'is_del',
        'is_forbid'
    ];


    public function childs()
    {
    	return $this->where('pid', $this->id)->get();
    }
}

