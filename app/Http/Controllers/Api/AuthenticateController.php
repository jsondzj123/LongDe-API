<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Log;





class AuthenticateController extends Controller {



    public function postLogin(Request $request) {
        //$this->accept_data
        $validator = Validator::make($request->all(), [
            'username'=> 'required',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $user_info = User::where('username', $request->input('username'))
            ->where('password', md5($request->input('password')))
            ->first();    
        if ($user_info) {
            if (!$token = Auth::login($user_info)) {
                return $this->response(['code' => 500, 'msg' => '系统错误，无法生成令牌']);
            } else {
                $user_info['token'] = $token;
                $this->setTokenToRedis($user_info->id, $token);
                return $this->response(['code' => 200, 'data' => $user_info]);
            }
        } else {
            return $this->response(['code' => 404, 'msg' => '用户不存在']);
        }
    }

    public function register(Request $request) {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first(), 422);
        }
        $user = $this->create($request->all());
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = Auth::login($user)) {
                return response('用户名或密码不正确', 401);
            }
        } catch (JWTException $e) {
            Log::error('创建token失败' . $e->getMessage());
            // something went wrong whilst attempting to encode the token
            return response('创建token失败', 500);
        }

        $user['token'] = $token;
        $this->setTokenToRedis($user->id, $token);
        return $this->response(['code' => 200, 'data' => $user]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'username' => 'required|max:255|unique:users',
            'mobile' => 'unique:users',
            'password' => 'required|min:6',
            'email' => 'email',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
            'username' => $data['username'],
            'mobile' => $data['mobile'],
            'password' => md5($data['password']),
            'email' => $data['email'],
        ]);
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|digits:11',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }


        $user = User::where('username', $request->input('username'))->firstOrfail();
        $user->password = md5($request->input('password'));
        if (!$user->save()) {
            return $this->response('修改密码失败', 500);
        }
        $token = Auth::fromUser($user);
        $this->setTokenToRedis($user->id, $token);
        return $this->response(['message' => '修改密码成功', 'token' => $token]);
    }

    public function setTokenToRedis($userId, $token) {
        try {
            Redis::set('longde:api:' . env('APP_ENV') . ':user:token', $userId, $token);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }
}
