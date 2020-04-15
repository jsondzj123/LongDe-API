<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;


class Controller extends BaseController {
    public static $data;
    /*
     * 
     */
    public function __construct(Request $request) {
        self::$data = app('rsa')->servicersadecrypt($request);
        //app('rsa')->test();
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
