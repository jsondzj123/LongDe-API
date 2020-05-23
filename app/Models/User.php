<?php
namespace App\Models;
 
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    public $table = 'ld_student';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'real_name', 'phone', 'sex', 'educational', 'address_locus', 'qq', 'wechat','email','office_phone','contact_people','contact_phone','head_icon','sign','province_id','city_id','nickname','papers_type','papers_num'
    ];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'create_at',
        'update_at',
        'is_forbid',
        'reg_source',
        'state_status',
        'enroll_status',
        'remark',
        'family_phone',
        'age',
        'address',
        'id',
        'admin_id',
        'school_id',
        'birthday'
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
 
    public function getJWTCustomClaims()
    {
        return [];
    }
}
