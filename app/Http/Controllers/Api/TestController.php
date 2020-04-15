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
    }
}
