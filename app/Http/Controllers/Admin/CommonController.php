<?php
namespace App\Http\Controllers\Admin;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CommonController extends BaseController {
   /*
     * @param  description   讲师或教务搜索列表
     * @param  参数说明       body包含以下参数[
     *     parent_id     学科分类id
     *     real_name     老师姓名
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
    */
    public function getTeacherSearchList(Request $request){
        //获取提交的参数
        try{
            //判断token或者body是否为空
            if(!empty($request->input('token')) && !empty($request->input('body'))){
                $rsa_data = app('rsa')->servicersadecrypt($request);
            } else {
                $rsa_data = [];
            }
            
            //获取讲师教务搜索列表
            $data = \App\Models\Teacher::getTeacherSearchList($rsa_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取老师搜索列表成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
