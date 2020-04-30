<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Adminuser extends Model {
    //指定别的表名
    public $table = 'ld_admin_user';
    //时间戳设置
    public $timestamps = false;
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
        $result = self::insertGetId($insertArr);
        return $result;
    }
}
