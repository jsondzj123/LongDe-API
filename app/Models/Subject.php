<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Subject extends Model {

	protected $fillable = [
        'id',
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


    public function parent()
    {

        return $this->where('id', $this->pid)->first();
    }
}

