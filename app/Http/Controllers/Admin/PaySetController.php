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
     * @param ctime     2020-05-27
     */
    public function getList(){
        $result = PaySet::getList(self::$accept_data);
        if($result['code'] == 200){
            return response()->json($result);
        }else{
            return response()->json($result);
        }
    }
    /*
     * @param  description   更改支付状态
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-27
     */
    public function doUpdatePayState(){
    	$data = self::$accept_data;
    	if(!isset($data['id']) || empty($data['id'])){
    		return response()->json(['code'=>201,'msg'=>'id缺少或为空']);
    	}
    	$payconfigArr  = PaySet::where(['id'=>$data['id']])->first();
        if($payconfigArr['pay_status'] == 1){
            //禁用
            $update['pay_status'] = -1;
            $update['wx_pay_state'] = -1;
            $update['zfb_pay_state'] = -1;
            $update['hj_wx_pay_state'] = -1;
            $update['hj_zfb_pay_state'] = -1;
        }else{
            //启用
            $update['pay_status'] = 1;
            $update['wx_pay_state'] = 1;
            $update['zfb_pay_state'] = 1;
            $update['hj_wx_pay_state'] = 1;
            $update['hj_zfb_pay_state'] = 1;
        }
        $update['update_at'] = date('Y-m-d H:i:s');

        if(PaySet::doUpdate(['id'=>$data['id']],$update)){
            return response()->json(['code'=>200,'msg'=>"更改成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'更改成功']);
        }
    }
     /*
     * @param  description   更改微信状态
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-27
     */
    public function doUpdateWxState(){
        $data = self::$accept_data;
        if(!isset($data['id']) || empty($data['id'])){
            return response()->json(['code'=>201,'msg'=>'id缺少或为空']);
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->first();
        if($payconfigArr['wx_pay_state'] == 1){
                $update['wx_pay_state'] = -1;//禁用
        }else{
            $update['wx_pay_state'] = 1; //启用
        }
        $update['update_at'] = date('Y-m-d H:i:s');
        if(PaySet::doUpdate(['id'=>$data['id']],$update)){
            return response()->json(['code'=>200,'msg'=>"更改成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'更改成功']);
        }
    }
     /*
     * @param  description   更改支付宝状态
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-27
     */
    public function doUpdateZfbState(){
        $data = self::$accept_data;
        if(!isset($data['id']) || empty($data['id'])){
            return response()->json(['code'=>201,'msg'=>'id缺少或为空']);
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->first();
        if($payconfigArr['zfb_pay_state'] == 1){
                $update['zfb_pay_state'] = -1;//禁用
        }else{
            $update['zfb_pay_state'] = 1; //启用
        }
        $update['update_at'] = date('Y-m-d H:i:s');
        if(PaySet::doUpdate(['id'=>$data['id']],$update)){
            return response()->json(['code'=>200,'msg'=>"更改成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'更改成功']);
        }
    }
    /*
     * @param  description   更改汇聚状态
     * @param  参数说明       body包含以下参数[
            id   列表id
            hj_state  汇聚状态
     * ]
     * @param author    lys
     * @param ctime     2020-05-27
     */
    public function doUpdateHjState(){
        $data = self::$accept_data;
        if(!isset($data['id']) || empty($data['id'])){
            return response()->json(['code'=>201,'msg'=>'id缺少或为空']);
        }
        if(!isset($data['hj_state']) || empty($data['hj_state'])){
            return response()->json(['code'=>201,'msg'=>'hj状态类型缺少或为空']);
        }
        if($data['hj_state'] == 1){
            //禁用
            $update['hj_wx_pay_state'] = -1;
            $update['hj_zfb_pay_state'] = -1;
        }else{
            //启用
            $update['hj_wx_pay_state'] = 1;
            $update['hj_zfb_pay_state'] = 1;
        }
        $update['update_at'] = date('Y-m-d H:i:s');
        if(PaySet::doUpdate(['id'=>$data['id']],$update)){
            return response()->json(['code'=>200,'msg'=>"更改成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'更改成功']);
        }
    }


}