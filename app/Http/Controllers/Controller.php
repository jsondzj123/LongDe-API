<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
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
    }

    /*返回json串
     * addtime 2020.4.14
     * auther liyinsheng
     * $code  int   状态码
     * $data  array  数据数组
     * return  string
     * */
    public function responseJson($code,$data=[]){
        $arr = config::get('code');
        if(in_array($code,$arr)){
            return response()->json(['code'=>404,'msg'=>'非法请求']);
        }
        return response()->json(['code' => $code, 'msg' => $arr[$code],'data'=>$data]);
    }
}
