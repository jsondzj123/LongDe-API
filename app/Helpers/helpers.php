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
        if (!in_array($code, $arr)) {
            return response()->json(['code' => 404, 'msg' => '非法请求']);
        }
        return response()->json(['code' => $code, 'msg' => $arr[$code], 'data' => $data]);
    }

//}

/*
 * @param  description   获取IP地址
 * @param  author        dzj
 * @param  ctime         2020-04-27
 */
function getip() {
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    return $realip;
}
?>
