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



class UserController extends Controller {
    protected $jwt;

//    public function __construct(JWTAuth $jwt) {
//        echo 'aaaa';
//        exit;
       // $this->jwt = $jwt;
//    }


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
    public function getUserInfoById(Request $request){
        /*$userinfo = DB::table("longdeapi_user")->get()->toArray();
        echo "<pre>";
        print_r($userinfo);*/

        //return User::getMember();
        /*$data = [
            'mobile'  =>  '15893641025' ,
            'username'=>  '刘莉莉'
        ];
        $rs = User::doUserAdd($data);
        if($rs && !empty($rs)){
            return response()->json(['code'=>200,'msg'=>'添加成功']);
        } else {
            return response()->json(['code'=>500,'msg'=>'添加失败']);
        }*/

        $rs = User::find(2)->getUserLessionList;
        if($rs && !empty($rs)){
            return response()->json(['code'=>200,'msg'=>'添加成功','data'=>$rs]);
        } else {
            return response()->json(['code'=>500,'msg'=>'添加失败']);
        }

        //return response()->json(['username'=>'aaaa']);
    }


    /*public function userLogin(Request $request) {

        $user = Auth::where('id',1)->first();

        $token = $this->jwt->fromUser($user);
        return response()->json(compact('token'),200);


        $user = \App\Models\Auth::where('username', $request->input('username'))
                ->where('password', $request->input('password'))->first();

        $token = Auth::login($user);
        echo "<prew>";
        print_r($token);
        exit;
        if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
            return response()->json(['user_not_found'], 404);
        }
        return response()->json(compact('token'));
    }*/


    /**
     * 登录
     *
     * @author duzhijian
     *
     * @param \Illuminate\Http\Request;
     * @return \Illuminate\Http\Response;
     */
    public function login(Request $request) {
        $response = array('code' => '0');
        try {
            $user = \App\Models\Auth::where('username', $request->input('username'))
                ->where('password', $request->input('password'))->first();

            if (!$token = Auth::login($user)) {
                $response['code']     = '5000';
                $response['errorMsg'] = '系统错误，无法生成令牌';
            } else {
                $response['data']['user_id']      = strval($user->id);
                $response['data']['access_token'] = $token;
                $response['data']['expires_in']   = strval(time() + 86400);
            }
        } catch (QueryException $queryException) {
            $response['code'] = '5002';
            $response['msg']  = '无法响应请求，服务端异常';
        }
        return response()->json($response);
    }


    public function getUserinfo(Request $request){
        /*$user = \App\Models\Auth::where('username', 'zzz')
                ->where('password', '09421eca667afc4e13ba944a3b41cf2846004c20')->first();
        $token = $this->jwt->fromUser($user);
        $this->jwt->setToken($token);*/
        //
        //$token = $this->jwt->parseToken()->toUser();
//echo $this->jwt->getToken().'<br>';
        $token =  $request->input('token');
echo $this->jwt->refresh($token);
//echo $this->jwt->getPayload();
        //return $token;
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
    public function test(){
        echo "将文件提交到远程自己的分支";
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
