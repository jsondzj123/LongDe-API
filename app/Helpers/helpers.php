<?php

use Illuminate\Support\Facades\Config;

/*返回json串
   * addtime 2020.4.17
   * auther liyinsheng
   * $code  int   状态码
   * $data  array  数据数组
   * return  string
* */
//if (! function_exists('responseJson')) {
    function responseJson($code, $data = [])
    {
        $arr = config::get('code');
<<<<<<< HEAD
=======

//        print_r($newarr);die;

>>>>>>> szw
        if (!in_array($code, $arr)) {
            return response()->json(['code' => 404, 'msg' => '非法请求']);
        }
        return response()->json(['code' => $code, 'msg' => $arr[$code], 'data' => $data]);
    }

//}
?>
