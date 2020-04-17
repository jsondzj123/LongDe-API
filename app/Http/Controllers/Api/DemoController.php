<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
#use App\Models\User;
#use App\Models\Auth;


#use DB;
#use Illuminate\Support\Facades\Redis;
use App\Models\Demo_User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Code;

class DemoController extends Controller {

    public function demo(Request $request){
        $id = $request->id;
        $data = Demo_User::getInfoById($id);

        if(empty($data)){
            return responseJson(0);
        }else{
            return responseJson(200,$data);
        }

    }

    /**
     * @Notes:添加数据
     * @Author: liyinsheng
     * @Date: 2020/4/14
     * @Time: 18:46
     * @Interface insert
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
   public  function insert(){
       $arr= [];
       echo getRandom(32);die;
       return  responseJson(0);//        return $this->responseJson('0');
   }






}

