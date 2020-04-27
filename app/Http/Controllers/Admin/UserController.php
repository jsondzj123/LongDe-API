<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
#use App\Models\User;
#use App\Models\Auth;

#use DB;
#use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Tymon\JWTAuth\JWTAuth;



class UserController extends Controller {
    protected $jwt;

    /*public function __construct(JWTAuth $jwt) {
        $this->jwt = $jwt;
    }*/

    public function loginAndRegister(){
        //echo 'nnn';
       //Redis::set('name', 'guwenjie');
        $values = Redis::get('name');
        dd($values);

    }

    /*
     * @param 根据用户ID获取用户信息
     * @param $user_id
     */
    public function getUserList(Request $request){
        echo "aaaaa";
    }
}
