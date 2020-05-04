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
        rDate('200','成功',$list);
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
            rDate($edit,'修改成功');
        }else{
            rDate($edit,'修改失败');
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
            rDate($edit,'删除成功');
        }else{
            rDate($edit,'删除失败');
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
            rDate('400','此分类已存在');
        }elseif ($add == 500){
            rDate('500','参数不正确');
        }else{
            if($add){
                rDate($add,'添加成功');
            }else{
                rDate($add,'添加失败');
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
            rDate($exid,'修改成功');
        }else{
            rDate($exid,'修改失败');
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
            rDate('300','获取失败');
        }else{
            rDate('200','获取成功',$find);
        }
    }
}
