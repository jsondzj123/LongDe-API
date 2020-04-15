<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
#use App\Models\User;
#use App\Models\Auth;

#use DB;
#use Illuminate\Support\Facades\Redis;
use App\Models\Testuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Tymon\JWTAuth\JWTAuth;



class TestController extends Controller {
    protected $jwt;


    public function __construct(JWTAuth $jwt) {
        echo 'hhhh';
        exit;
    }

    //改
    public function userUpdate(){
        echo 'aaaa';
        exit;
    }
    /*
         * @param  descriptsion 作用
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/15 16:49
         * return  array
         */
    public function zs(){
        echo "修改ssh连接";
    }
}
