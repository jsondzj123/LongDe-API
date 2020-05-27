<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Admin as Adminuser;
use App\Models\Roleauth;
use App\Models\Authrules;
use App\Models\School;
use App\Models\PaySet;
use Illuminate\Support\Facades\Redis;
use App\Tools\CurrentAdmin;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminLog;
use Illuminate\Support\Facades\DB;
class PaySetController extends Controller {

     /*
     * @param  description   获取支付配置列表
     * @param  参数说明       body包含以下参数[
     *     search       搜索条件 （非必填项）
     *     page         当前页码 （不是必填项）
     *     pagesize     每页显示条件 （不是必填项）
     * ]
     * @param author    lys
     * @param ctime     2020-04-29
     */
    public function getList(){
        $result = PaySet::getList(self::$accept_data);
        if($result['code'] == 200){
            return response()->json($result);
        }else{
            return response()->json($result);
        }
    }
    
    // public function doUpdatePayState(){
    // 	$data = self::$accept_data;
    // 	if(!isset($data['id'] || empty($data['id']))){
    // 		return json_decode(['code'=>201,'msg'=>'id缺少或为空'])
    // 	}
    // 	PaySet::where(['school_id'])
    // }


}