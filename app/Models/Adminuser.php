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
         * @param  $user_id     用户id
         * @param  author  苏振文
         * @param  ctime   2020/4/25 15:44
         * return  array
         */
    public static function getUserOne($id){
        if(empty($id) || !intval($id)){
            return ['code'=>202,'msg'=>'参数为空或类型不正确'];
        }
        $userInfo = self::where(['id'=>$id])->first();
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

    public static function upUserStatus($where,$update){
        $result = self::where($where)->update($update);
        return $result;
    }
}
