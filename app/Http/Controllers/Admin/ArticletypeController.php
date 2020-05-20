<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Articletype;
use App\Models\School;

class ArticletypeController extends Controller {
    /*
         * @param  获取分类列表
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/29 14:29
         * return  array
         */
    public function getTypeList(){
        try{
            $list = Articletype::getArticleList(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  禁用&启用分类
         * @param  $id
         * @param  $type 0禁用1启用
         * @param  author  苏振文
         * @param  ctime   2020/4/30 14:19
         * return  array
         */
    public function editStatusForId(){
        try{
            $list = Articletype::editStatusToId(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  删除分类
         * @param  $id 分类id
         * @param  author  苏振文
         * @param  ctime   2020/4/30 14:31
         * return  array
         */
    public function exitDelForId(){
        try{
            $list = Articletype::editDelToId(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    //获取学校信息
    public function schoollist(){
        $role_id = isset(AdminLog::getAdminInfo()->admin_user->role_id) ? AdminLog::getAdminInfo()->admin_user->role_id : 0;
        if($role_id == 1){
            $school = School::select('id as value','name as label')->where(['is_forbid'=>1,'is_del'=>1])->get()->toArray();
        }else{
            $school_id = isset(AdminLog::getAdminInfo()->admin_user->school_id) ? AdminLog::getAdminInfo()->admin_user->school_id : 0;
            $data['school_id'] = $school_id;
            $school = School::select('id as value','name as label')->where(['id'=>$data['school_id'],'is_forbid'=>1,'is_del'=>1])->get()->toArray();
        }
        return response()->json(['code' => 200 , 'msg' =>'成功','data'=>$school]);
    }

    /*
         * @param  添加
         * @param  author  苏振文
         * @param  ctime   2020/4/30 14:43
         * return  array
         */
    public function addType(){
        try{
            $list = Articletype::addType(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  修改分类信息
         * @param  author  苏振文
         * @param  ctime   2020/4/30 15:11
         * return  array
         */
    public function exitTypeForId(){
        try{
            $list = Articletype::editForId(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    /*
         * @param  单条查询
         * @param  $id 类型id
         * @param  author  苏振文
         * @param  ctime   2020/5/4 10:00
         * return  array
         */
    public function OnelistType(){
        try{
            $list = Articletype::oneFind(self::$accept_data);
            return response()->json($list);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
