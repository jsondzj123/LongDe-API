<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController {
    //接受数据参数
    public static $accept_data;
    /*

     * @param  description   基础底层数据加密部分
     * @param  $request      数据接收参数
     * @param  author        duzhijian
     * @param  ctime         2020-04-16
     * return  string
     */
    public function __construct(Request $request) {
        self::$accept_data = app('rsa')->servicersadecrypt($request);
        //app('rsa')->Test();
    }

     /*返回json串
     * addtime 2020.4.28
     * auther 孙晓丽
     * $code  int   状态码
     * $data  array  数据数组
     * return  string
     * */
    protected function response($data, $statusCode = 200)
    {
        if ($statusCode == 200 && is_string($data)) {
            return response()->json(['message' => $data]);
        } elseif (is_string($data)) {
            return response()->json(['error' => $data], $statusCode);
        } else {
            return response()->json($data, $statusCode);
        }
        return response()->json($data);
    }

}
