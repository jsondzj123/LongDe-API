<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UserController extends Controller {
    /*
     * @param  description   根据用户id获取用户信息
     * @param author    dzj
     * @param ctime     2020-05-23
     * return string
     */
    public function getUserInfoById() {
        //获取提交的参数
        try{
            $user_id = 1;
            //根据用户id获取用户详情
            $user_info = User::find($user_id);
            if($user_info && !empty($user_info)){
                return response()->json(['code' => 200 , 'msg' => '获取学员信息成功' , 'data' => $user_info]);
            } else {
                return response()->json(['code' => 203 , 'msg' => '获取学员信息失败']);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
