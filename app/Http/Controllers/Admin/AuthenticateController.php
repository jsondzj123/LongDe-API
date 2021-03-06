<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\School;
use Log;
use JWTAuth;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Admin\AdminUserController as AdminUser;




class AuthenticateController extends Controller {


    public function postLogin(Request $request) {

        $validator = Validator::make($request->all(), [
            'username'=> 'required',
            'password'=> 'required'
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }

        $credentials = $request->only('username', 'password');

        return $this->login($credentials);
    }

    public function register(Request $request) {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response($validator->errors()->first(), 202);
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
    {   //begin 识别网校（用户）
        // $school = School::where(['dns'=>$_SERVER["SERVER_NAME"],'is_del'=>1])->first();
        // if(!$school){
        //      return $this->response('学校不存在', 401);
        // }else{
        //     if($school['is_forbid'] != 1){
        //         return $this->response('学校已禁用，请联系网校负责人', 401);
        //     }
        // }
        // $data['school_ld'] = $school['id'];
        //end 
        try {
            if (!$token = JWTAuth::attempt($data)) {
                return $this->response('用户名或密码不正确', 401);
            }
        } catch (JWTException $e) {
            Log::error('创建token失败' . $e->getMessage());
            return $this->response('创建token失败', 500);
        }
        $user = JWTAuth::user();
        $user['token'] = $token;
        $this->setTokenToRedis($user->id, $token);
      
        $AdminUser = new AdminUser();
        $user['auth'] = [];     //5.14 该账户没有权限返回空  begin
        if($user['role_id']>0){
             $admin_user =  $AdminUser->getAdminUserLoginAuth($user['role_id']);  //获取后台用户菜单栏（lys 5.5）
            if($admin_user['code']!=200){
                return response()->json(['code'=>$admin_user['code'],'msg'=>$admin_user['msg']]);
            }
            $user['auth'] = $admin_user['data'];
        }               //5.14 end
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
            'username' => 'required|max:255|unique:ld_admin',
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
