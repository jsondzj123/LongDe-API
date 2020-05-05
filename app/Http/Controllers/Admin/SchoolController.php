<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Adminuser;
use App\Models\Roleauth;
use App\Models\Authrules;
use App\Models\School;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class SchoolController extends Controller {
  
     /*
     * @param  description 获取分校列表  
     * @param  参数说明       body包含以下参数[
     *     school_name       搜索条件
     *     school_dns        分校域名
     *     page         当前页码  必填项
     *     limit        每页显示条数
     * ]
     * @param author    lys
     * @param ctime     2020-05-05
     */
    public function getSchoolList(Request $request){
    
            $validator = Validator::make($request->all(), School::rule(),School::message());
            if ($validator->fails()) {
                echo $validator->errors()->first();die;
                return  $this->response($validator->errors()->first(), 422);
            }
            echo 1;die;
      
    }
    
    


     
}
