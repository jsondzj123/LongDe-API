<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CurrentUser;


class UserController extends Controller {

    /*
     * @param 根据用户ID获取用户信息
     * @param $user_id
     */
    public function getUserInfoById(Request $request){

        $rs = User::find(2)->getUserLessionList;
        if($rs && !empty($rs)){
            return response()->json(['code'=>200,'msg'=>'添加成功','data'=>$rs]);
        } else {
            return response()->json(['code'=>500,'msg'=>'添加失败']);
        }

        //return response()->json(['username'=>'aaaa']);
    }

    public function show($id){
        $user = User::find($id);
        return response()->json(['user' => $user]);
    }


    /**
     * 用户登出
     *
     * @author AdamTyn
     *
     * @return \Illuminate\Http\Response;
     */
    public function logout()
    {
        $response = array('code' => '0');

        \App\Models\Auth::invalidate(true);

        return response()->json($response);
    }

    /**
     * 更新用户Token
     *
     * @author AdamTyn
     *
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Http\Response;
     */
    public function refreshToken()
    {
        $response = array('code' => '0');

        if (!$token = \App\Models\Auth::refresh(true, true)) {
            $response['code']     = '5000';
            $response['errorMsg'] = '系统错误，无法生成令牌';
        } else {
            $response['data']['access_token'] = $token;
            $response['data']['expires_in']   = strval(time() + 86400);
        }

        return response()->json($response);
    }

    //增
    public function userAddfind(){

        $a = Testuser::getUserList();

        $username = $_POST['name'];
        $password = $_POST['pass'];
        $id = DB::table('ce_testuser')->insertGetId(
            ['username' => $username, 'password' => $password]
        );
        return $this->responseJson(200,$id);

    }
    //查
    public function userlist(){
//        $user = DB::table('ce_testuser')->get();

        $user =Testuser::Find(1);
        return $this->responseJson(200,$user);
    }
    //删
    public function userDelForId(){
        $id = $_POST['id'];
        $status = DB::table('users')->where(array('id'=>$id))->delete();
        return response()->json($status);
    }
    //改
    public function userUpdate(){

        DB::table('users')
            ->where('id', 1)
            ->update(['votes' => 1]);
    }
}
