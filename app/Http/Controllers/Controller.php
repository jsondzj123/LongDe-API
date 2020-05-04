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
//        self::$accept_data = app('rsa')->servicersadecrypt($request);
        //app('rsa')->Test();
    }

}
