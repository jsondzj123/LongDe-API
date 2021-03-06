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
        if(!$payconfigArr){
            return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        }
        $schoolArr = School::getSchoolOne(['id'=>$payconfigArr['school_id'],'is_del'=>1],'is_forbid');
        if($schoolArr['code']!= 200){
             return response()->json($schoolArr);
        }
        if($schoolArr['data']['is_forbid'] != 1){
             return response()->json(['code'=>208,'msg'=>'请先开启学校状态']);
        }
        if($payconfigArr['wx_pay_state'] == 1){
                $update['wx_pay_state'] = -1;//禁用
        }else{
            $update['wx_pay_state'] = 1; //启用
        }
        $update['update_at'] = date('Y-m-d H:i:s');
        if(PaySet::doUpdate(['id'=>$data['id']],$update)){
             AdminLog::insertAdminLog([
                'admin_id'       =>   CurrentAdmin::user()['id'] ,
                'module_name'    =>  'PyaSet' ,
                'route_url'      =>  'admin/payset/doUpdateWxState' , 
                'operate_method' =>  'update' ,
                'content'        =>  json_encode($data).json_encode($update),
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
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
        if(!$payconfigArr){
            return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        }
        $schoolArr = School::getSchoolOne(['id'=>$payconfigArr['school_id'],'is_del'=>1],'is_forbid');
        if($schoolArr['code']!= 200){
             return response()->json($schoolArr);
        }
        if($schoolArr['data']['is_forbid'] != 1){
             return response()->json(['code'=>208,'msg'=>'请先开启学校状态']);
        }
        if($payconfigArr['zfb_pay_state'] == 1){
                $update['zfb_pay_state'] = -1;//禁用
        }else{
            $update['zfb_pay_state'] = 1; //启用
        }
        $update['update_at'] = date('Y-m-d H:i:s');
        if(PaySet::doUpdate(['id'=>$data['id']],$update)){
             AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'PaySet' ,
                    'route_url'      =>  'admin/payset/doUpdateZfbState' , 
                    'operate_method' =>  'update',
                    'content'        =>  json_encode($data).json_encode($update),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
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
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->first();
        if(!$payconfigArr){
            return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        }
        $schoolArr = School::getSchoolOne(['id'=>$payconfigArr['school_id'],'is_del'=>1],'is_forbid');
        if($schoolArr['code']!= 200){
             return response()->json($schoolArr);
        }
        if($schoolArr['data']['is_forbid'] != 1){
             return response()->json(['code'=>208,'msg'=>'请先开启学校状态']);
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
             AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'PaySet' ,
                    'route_url'      =>  'admin/payset/doUpdateHjState' , 
                    'operate_method' =>  'update',
                    'content'        =>  json_encode($data).json_encode($update),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
            return response()->json(['code'=>200,'msg'=>"更改成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'更改成功']);
        }
    }
    /*
     * @param  description   获取支付宝添加信息
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-28
     */
    public function getZfbConfig(){
        $data = self::$accept_data;
        if(!isset($data['id']) || empty($data['id'])){
            return response()->json(['code'=>201,'msg'=>'id缺少或为空']);
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->select('zfb_app_id','zfb_app_public_key','zfb_public_key')->first();
        if(!$payconfigArr){
             return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        } 
        if(!empty($payconfigArr['zfb_app_id'])){
            $payconfigArr['zfb_app_ids'] = substr_replace($payconfigArr['zfb_app_id'],'*********','10','15'); 
        }
        if(!empty($payconfigArr['zfb_app_public_key'])){
            $payconfigArr['zfb_app_public_keys'] = substr_replace($payconfigArr['zfb_app_public_key'],'*********','10','25'); 
        }
        if(!empty($payconfigArr['zfb_public_key'])){
            $payconfigArr['zfb_public_keys'] = substr_replace($payconfigArr['zfb_public_key'],'*********','10','25'); 
        }
        $arr['code'] = 200;
        $arr['msg']  = 'success';
        $arr['data'] = $payconfigArr;
        return response()->json($arr); 
    }
     /*
     * @param  description   获取微信添加信息
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-28
     */
    public function getWxConfig(){
        $data = self::$accept_data;
        if(!isset($data['id']) || empty($data['id'])){
            return response()->json(['code'=>201,'msg'=>'id缺少或为空']);
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->select('wx_app_id','wx_commercial_tenant_number','wx_api_key')->first();
        if(!$payconfigArr){
             return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        }
        if(!empty($payconfigArr['wx_app_id'])){
            $payconfigArr['wx_app_ids'] = substr_replace($payconfigArr['wx_app_id'],'*********','10','15'); 
        }
        if(!empty($payconfigArr['wx_commercial_tenant_number'])){
            $payconfigArr['wx_commercial_tenant_numbers'] = substr_replace($payconfigArr['wx_commercial_tenant_number'],'*********','10','25'); 
        }
        if(!empty($payconfigArr['wx_api_key'])){
            $payconfigArr['wx_api_keys'] = substr_replace($payconfigArr['wx_api_key'],'*********','10','25'); 
        }
        $arr['code'] = 200;
        $arr['msg']  = 'success';
        $arr['data'] = $payconfigArr;
        return response()->json($arr); 
    }

    /*
     * @param  description   获取汇聚支付添加信息
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-28
     */
    public function getHjConfig(){
        $data = self::$accept_data;
        if(!isset($data['id']) || empty($data['id'])){
            return response()->json(['code'=>201,'msg'=>'id缺少或为空']);
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->select('hj_md_key','hj_commercial_tenant_number','hj_wx_commercial_tenant_deal_number','hj_zfb_commercial_tenant_deal_number','hj_wx_pay_state','hj_zfb_pay_state')->first();
        if(!$payconfigArr){
             return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        }
        if(!empty($payconfigArr['hj_md_key'])){
            $payconfigArr['hj_md_keys'] = substr_replace($payconfigArr['hj_md_key'],'*********','10','15'); 
        }
        if(!empty($payconfigArr['hj_commercial_tenant_number'])){
            $payconfigArr['hj_commercial_tenant_numbers'] = substr_replace($payconfigArr['hj_commercial_tenant_number'],'*********','10','25'); 
        }
        if(!empty($payconfigArr['hj_wx_commercial_tenant_deal_number'])){
            $payconfigArr['hj_wx_commercial_tenant_deal_numbers'] = substr_replace($payconfigArr['hj_wx_commercial_tenant_deal_number'],'*********','10','25'); 
        }
        if(!empty($payconfigArr['hj_zfb_commercial_tenant_deal_number'])){
            $payconfigArr['hj_zfb_commercial_tenant_deal_numbers'] = substr_replace($payconfigArr['hj_zfb_commercial_tenant_deal_number'],'*********','10','25'); 
        }
        $arr['code'] = 200;
        $arr['msg']  = 'success';
        $arr['data'] = $payconfigArr;
        return response()->json($arr); 
    }


    /*
     * @param  description   添加/修改支付宝配置信息
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-28
     */
    public function doZfbConfig(){
        $data = self::$accept_data;
         $validator = Validator::make($data, 
                [
                    'id' => 'required|integer',
                    'app_id'=>'required',
                    'app_public_key'=>'required',
                    'public_key'=>'required',
                ],
                PaySet::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->select('admin_id')->first();
        if(!$payconfigArr){
            return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        } 
        $result = PaySet::doUpdate(['id'=>$data['id']],['zfb_app_id'=>$data['app_id'],'zfb_app_public_key'=>$data['app_public_key'],'zfb_public_key'=>$data['public_key'],'update_at'=>date('Y-m-d H:i:s')]);
        if($result){
             AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'Payset' ,
                    'route_url'      =>  'admin/payset/doZfbUpdate' , 
                    'operate_method' =>  'update',
                    'content'        =>  json_encode($data),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
            return response()->json(['code'=>200,'msg'=>"保存成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'保存成功']);
        }
    }
    /*
     * @param  description   添加/修改支付宝配置信息
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-28
     */
    public function doWxConfig(){
        $data = self::$accept_data;
         $validator = Validator::make($data, 
                [
                    'id' => 'required|integer',
                    'app_id'=>'required',
                    'shop_number'=>'required',
                    'api_key'=>'required',
                ],
                PaySet::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->select('admin_id')->first();
        if(!$payconfigArr){
            return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        } 
        $result = PaySet::doUpdate(['id'=>$data['id']],['wx_app_id'=>$data['app_id'],'wx_commercial_tenant_number'=>$data['shop_number'],'wx_api_key'=>$data['api_key'],'update_at'=>date('Y-m-d H:i:s')]);
        if($result){
             AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'Payset' ,
                    'route_url'      =>  'admin/payset/doWxUpdate' , 
                    'operate_method' =>  'update',
                    'content'        =>  json_encode($data),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
            return response()->json(['code'=>200,'msg'=>"保存成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'保存成功']);
        }
    }
    /*
     * @param  description   添加/修改支付宝配置信息
     * @param  参数说明       body包含以下参数[
            id   列表id
     * ]
     * @param author    lys
     * @param ctime     2020-05-28
     */
    public function doHjConfig(){
        $data = self::$accept_data;
        $validator = Validator::make($data, 
                [
                    'id' => 'required|integer',
                    'shop_number'=>'required',
                    'md_key'=>'required',
                    'wx_deal_shop_number'=>'required',
                    'wx_state'=>'required|integer',
                    'zfb_deal_shop_number'=>'required',
                    'zfb_state'=>'required|integer',
                ],
                PaySet::message());
        if($validator->fails()) {
            return response()->json(json_decode($validator->errors()->first(),1));
        }
        $payconfigArr  = PaySet::where(['id'=>$data['id']])->select('admin_id')->first();
        if(!$payconfigArr){
            return response()->json(['code'=>204,'msg'=>"数据不存在"]);
        } 
        $result = PaySet::doUpdate(['id'=>$data['id']],
                ['hj_commercial_tenant_number'=>$data['shop_number'],
                    'hj_md_key'=>$data['md_key'],
                    'hj_wx_commercial_tenant_deal_number'=>$data['wx_deal_shop_number'],
                    'hj_wx_pay_state'=>$data['wx_state'],
                    'hj_zfb_commercial_tenant_deal_number'=>$data['zfb_deal_shop_number'],
                    'hj_zfb_pay_state'=>$data['zfb_state'],
                    'update_at'=>date('Y-m-d H:i:s')]);
        if($result){
             AdminLog::insertAdminLog([
                    'admin_id'       =>   CurrentAdmin::user()['id'] ,
                    'module_name'    =>  'Payset' ,
                    'route_url'      =>  'admin/payset/doHjUpdate' , 
                    'operate_method' =>  'update',
                    'content'        =>  json_encode($data),
                    'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                    'create_at'      =>  date('Y-m-d H:i:s')
                ]);
            return response()->json(['code'=>200,'msg'=>"保存成功"]);
        }else{
            return response()->json(['code'=>203,'msg'=>'保存成功']);
        }
    }

}