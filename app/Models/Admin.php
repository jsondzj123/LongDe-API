<?php
namespace App\Models;
 
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Tools\CurrentAdmin;
use App\Models\Roleauth;

class Admin extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    public $table = 'ld_admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'email', 'mobile', 'realname', 'sex', 'admin_id','teacher_id','school_status','school_id','is_forbid','is_del'
    ];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ]; 


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
 
    public function getJWTCustomClaims()
    {
        return ['role' => 'admin'];
    }


    public static function message()
    {
        return [
            'id.required'  => '用户id不能为空',
            'id.integer'   =>'用户id不合法',
            'school_id.required'  => '学校id不能为空',
            'school_id.integer'   =>'学校id类型不合法',
            'username.required' => '账号不能为空',
            'username.unique'  => '账号已存在',
            'realname.required' => '真实姓名不能为空',
            'mobile.required'  => '手机号不能为空',
            'mobile.regex' => '手机号不合法',
            'sex.integer'  => '性别标识不合法',
            'sex.required' => '性别标识不能为空',
            'password.required'  => '密码不能为空',
            'pwd.required' => '确认密码不能为空',
            'role_id.required' => '角色id不能为空',
            'role_id.integer' => '角色id不合法',
        ];

    }

    /*
         * @param  descriptsion 后台账号信息
         * @param  $user_id     用户id
         * @param  author  苏振文
         * @param  ctime   2020/4/25 15:44
         * return  array
         */
    // public static function GetUserOne($id){
    //     $return = self::where(['id'=>$id])->first();
    //     return $return;
    // }
    

    /*
         * @param  descriptsion 后台账号信息
         * @param  $where[
         *    id   =>       用户id
         *    ....
         * ]
         * @param  author  苏振文
         * @param  ctime   2020/4/25 15:44
         * return  array
         */
    public static function getUserOne($where){

        $userInfo = self::where($where)->first();
        if($userInfo){
            return ['code'=>200,'msg'=>'获取后台用户信息成功','data'=>$userInfo];
        }else{
            return ['code'=>201,'msg'=>'后台用户信息不存在'];
        }
    }
    /*
     * @param  descriptsion 获取后台用户列表
     * @param  $where  array     查询条件
     * @param  $title  string   查询条件(用于用户列表查询)
     * @param  $page   int     当前页
     * @param  $limit  int     每页显示
     * @param  author   lys
     * @param  ctime   2020/4/28 13:25
     * return  array
     */
    public static  function getUserAll($where=[],$title='',$page = 1,$limit= 10){
    
        $data = self::leftjoin('ld_role_auth','ld_role_auth.id', '=', 'ld_admin_user.role_id')
            ->where($where)
            ->where(function($query) use ($title){
                if($title != ''){
                    $query->where('ld_admin_user.real_name','like','%'.$title.'%')
                    ->orWhere('ld_admin_user.account','like','%'.$title.'%')
                    ->orWhere('ld_admin_user.phone','like','%'.$title.'%');
                }
            })
            ->get()->forPage($page,$limit)->toArray();
        return $data;  
    }
    /*
     * @param  descriptsion 更新状态方法
     * @param  $where[
     *    id   =>       用户id
     *    ....
     * ]
     * @param  $update[
     *    is_del   =>      删除状态码
     *    is_forbid =>     启禁状态码
     * ]
     * @param  author  lys
     * @param  ctime   2020-04-13
     * return  int
     */
    public static function upUserStatus($where,$update){
        
        $result = self::where($where)->update($update);
        return $result;
    }
    /*
     * @param  descriptsion 添加用户方法
     * @param  $insertArr[
     *    phone   =>     手机号
     *    account =>     登录账号
     *    ....
     * ]
     * @param  author  duzhijian
     * @param  ctime   2020-04-13
     * return  int
     */
    public static function insertAdminUser($insertArr){
        return  self::insertGetId($insertArr);
        
    }
    /*
     * @param  description   获取用户列表
     * @param  参数说明       body包含以下参数[
     *     search       搜索条件 （非必填项）
     *     page         当前页码 （不是必填项）
     *     limit        每页显示条件 （不是必填项）
     *     school_id    学校id  （非必填项）
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */
    public static function getAdminUserList($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
         // $adminUserInfo  = CurrentAdmin::user();  //当前登录用户所有信息
        $adminUserInfo['school_status'] = 0; //学校状态
        $adminUserInfo['school_id'] = 2; //学校id
        if($adminUserInfo['school_status'] == 1){
            //判断学校id是否合法
            if(!isset($body['school_id']) ||  $body['school_id'] <= 0){
                return ['code' => 202 , 'msg' => '学校id不合法'];
            }
        }
       
        //判断搜索条件是否合法
        if(!isset($body['search']) ){
            return ['code' => 202 , 'msg' => '缺少参数'];
        }
        $school_id = $adminUserInfo['school_id'];//学校
        if(!empty($body['school_id'])){
            $school_id = $body['school_id'];//根据搜索条件查询
        }

        $pagesize = isset($body['pagesize']) && $body['pagesize'] > 0 ? $body['pagesize'] : 15;
        $page     = isset($body['page']) && $body['page'] > 0 ? $body['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        if($adminUserInfo['school_status'] == 1){       //
            $SchoolInfo = School::where('id','!=',$adminUserInfo['school_id'])->where('is_del',1)->get(); //获取分校列表
        }else{
            $SchoolInfo = [];
        }
        $admin_count = self::where(['is_del'=>1,'school_id'=>$school_id])->count();
        $sum_page = ceil($admin_count/$pagesize);
        if($admin_count >0){
            $adminUserData =  self::leftjoin('ld_role_auth','ld_role_auth.id', '=', 'ld_admin.role_id')
                ->where(function($query) use ($body,$school_id){
                if(!empty($body['search'])){
                    $query->where('ld_admin.real_name','like','%'.$body['search'].'%')
                        ->orWhere('ld_admin.account','like','%'.$body['search'].'%')
                        ->orWhere('ld_admin.phone','like','%'.$body['search'].'%');
                }
                    $query->where('ld_admin.is_del',1);
                    $query->where('ld_admin.school_id',$school_id);
                })->offset($offset)->limit($pagesize)->get();
            return ['code'=>200,'msg'=>'Success','data'=>['admin_list' => $adminUserData ,'school_list'=>$SchoolInfo, 'total' => $admin_count , 'pagesize' => $pagesize , 'page' => $page,'search'=>$body['search'],'sum_page'=>$sum_page]];
        }
        return ['code'=>200,'msg'=>'Success','data'=>['admin_list' => [] ,'school_list'=>$SchoolInfo, 'total' => 0 , 'pagesize' => $pagesize , 'page' => $page,'search'=>$body['search'],'sum_page'=>$sum_page]];
    }


    /*
     * @param  description   获取角色列表
     * @param  参数说明       body包含以下参数[
     *     search       搜索条件 （非必填项）
     *     page         当前页码 （不是必填项）
     *     limit        每页显示条件 （不是必填项）
     *  
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */
    public static function getAuthList($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
         $adminUserInfo  = CurrentAdmin::user();  //当前登录用户所有信息
        // $adminUserInfo['school_status'] = 0; //学校状态
        // $adminUserInfo['school_id'] = 2; //学校id
        //判断搜索条件是否合法
        if(!isset($body['search']) ){
            return ['code' => 202 , 'msg' => '缺少参数'];
        }
        $pagesize = isset($body['pagesize']) && $body['pagesize'] > 0 ? $body['pagesize'] : 15;
        $page     = isset($body['page']) && $body['page'] > 0 ? $body['page'] : 1;
        $offset   = ($page - 1) * $pagesize;
        $role_auth_count = Roleauth::where(['is_del'=>1,'school_id'=> $adminUserInfo['school_id']])->count();
        $sum_page = ceil($role_auth_count/$pagesize);
        if($role_auth_count >0){
            $roleRuthData =  self::rightjoin('ld_role_auth','ld_role_auth.admin_id', '=', 'ld_admin.id')
                ->where(function($query) use ($body,$adminUserInfo){
                if(!empty($body['search'])){
                    $query->where('ld_role_auth.role_name','like','%'.$body['search'].'%');
                }
                    $query->where('ld_admin.is_del',1);
                    $query->where('ld_role_auth.school_id',$adminUserInfo['school_id']);
                })
                ->select('ld_role_auth.role_name','ld_admin.username','ld_role_auth.auth_desc','ld_role_auth.create_time','ld_role_auth.id')
                ->offset($offset)->limit($pagesize)->get();
            return ['code'=>200,'msg'=>'Success','data'=>['role_auth_list' => $roleRuthData , 'total' => $role_auth_count , 'pagesize' => $pagesize , 'page' => $page,'search'=>$body['search'],'sum_page'=>$sum_page]];
        }
        return ['code'=>200,'msg'=>'Success','data'=>['role_auth_list' => [] , 'total' => 0 , 'pagesize' => $pagesize , 'page' => $page,'search'=>$body['search'],'sum_page'=>$sum_page]];
    }
}
