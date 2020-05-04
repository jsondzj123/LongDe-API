<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
#use App\Models\Auth;

#use DB;
#use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Tymon\JWTAuth\JWTAuth;



class AdminController extends Controller {
            


    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return $this->response($admin);
    }

    /*
     * @param 根据用户ID获取用户信息
     * @param $user_id
     */
    public function getUserList(Request $request){
        echo "aaaaa";
    }
}
