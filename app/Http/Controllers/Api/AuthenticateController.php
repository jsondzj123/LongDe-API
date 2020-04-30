<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Log;
use JwtAuth;

class AuthenticateController extends Controller {


    /**
     * 登录
     *
     * @param  array  $request
     * @return array
     */
    public function postLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'username'=> 'required',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $credentials = $request->only('username', 'password');
        return $this->login($credentials);
    }

    /**
     * 注册
     *
     * @param  array  $request
     * @return  array 
     */
    public function register(Request $request) {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first(), 422);
        }
        $user = $this->create($request->all());
        return $this->login($credentials);
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
            'mobile' => 'min:11',
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
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'email' => isset($data['email']) ?: '',
        ]);
    }


    /**
     * 身份认证
     *
     * @param  array  $data
     * @return User
     */
    protected function login(array $data)
    {
        try {
            if (!$token = JWTAuth::attempt($data)) {
                return response('用户名或密码不正确', 401);
            }
        } catch (JWTException $e) {
            Log::error('创建token失败' . $e->getMessage());
            return response('创建token失败', 500);
        }

        $user = JWTAuth::user();
        $user['token'] = $token;
        $this->setTokenToRedis($user->id, $token);
        return $this->response($user);
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
            Redis::hset('longde:api:' . env('APP_ENV') . ':user:token', $userId, $token);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }
}
