<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Articletype;
use Illuminate\Http\Request;

class ArticletypeController extends Controller {
    /*
         * @param  获取分类列表
         * @param  $user_id     参数
         * @param  author  苏振文
         * @param  ctime   2020/4/29 14:29
         * return  array
         */
    public function getTypeList(){
        $school = isset($_POST['school_id'])?$_POST['school_id']:'';
        $page = isset($_POST['page'])?$_POST['page']:20;
        $list = Articletype::getArticleList($school,$page);
        return response()->json(['code' => 200 , 'msg' => '获取成功','data'=>$list]);
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
        $id = $_POST['id'];
        $type = $_POST['type'];
        $edit = Articletype::editStatusToId($type,$id);
        if($edit==200){
            return response()->json(['code' => $edit , 'msg' => '修改成功']);
        }else{
            return response()->json(['code' => $edit , 'msg' => '修改失败']);
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
        $id = $_POST['id'];
        $edit = Articletype::editDelToId($id);
        if($edit==200){
            return response()->json(['code' => $edit , 'msg' => '删除成功']);
        }else{
            return response()->json(['code' => $edit , 'msg' => '删除失败']);
        }
    }
    /*
         * @param  添加
         * @param  author  苏振文
         * @param  ctime   2020/4/30 14:43
         * return  array
         */
    public function addType(Request $request){
        $data = $request->post();
        $add = Articletype::addType($data);
        if($add==400){
            return response()->json(['code' => 400 , 'msg' => '此分类已存在']);
        }elseif ($add == 500){
            return response()->json(['code' => 500 , 'msg' => '参数不正确']);
        }else{
            if($add){
                return response()->json(['code' => $add , 'msg' => '添加成功']);
            }else{
                return response()->json(['code' => $add , 'msg' => '添加失败']);
            }
        }
    }
    /*
         * @param  修改分类信息
         * @param  author  苏振文
         * @param  ctime   2020/4/30 15:11
         * return  array
         */
    public function exitTypeForId(Request $request){
        $data = $request->post();
        $exid = Articletype::editForId($data);
        if($exid == 200){
            return response()->json(['code' => $exid , 'msg' => '修改成功']);
        }else{
            return response()->json(['code' => $exid , 'msg' => '修改失败']);
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
        $id = $_POST['id'];
        $find = Articletype::oneFind($id);
        if(!$find){
            return response()->json(['code' => 300 , 'msg' => '获取失败']);
        }else{
            return response()->json(['code' => 200 , 'msg' => '获取成功','data'=>$find]);
        }
    }
}
