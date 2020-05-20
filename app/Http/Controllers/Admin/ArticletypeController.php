<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Articletype;

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
