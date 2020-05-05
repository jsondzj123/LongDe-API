<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\Admin;
use Log;
use JWTAuth;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;





class AuthenticateController extends Controller {

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

    public function register(Request $request) {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return response($validator->errors()->first(), 422);
        }
        $user = $this->create($request->all())->toArray();
        return $this->login($user);
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
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'username' => 'required|max:255|unique:admins',
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
        return Admin::create([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),//bcrypt($data['password']),
            'email' => isset($data['email']) ?: '',
            'admin_id' => isset($data['admin_id']) ?: 0,
            'realname' => isset($data['realname']) ?: '',
            'sex' => isset($data['sex']) ?: 0,
            'mobile' => isset($data['mobile']) ?: '',
            'email' => isset($data['email']) ?: '',
        ]);
    }


    public function setTokenToRedis($userId, $token) {
        try {
            Redis::set('longde:admin:' . env('APP_ENV') . ':user:token', $userId, $token);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }
}