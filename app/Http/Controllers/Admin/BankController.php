<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;

class BankController extends Controller {
    /*
     * @param  description   添加题库的方法
     * @param  参数说明         body包含以下参数[
     *     topic_name  题库名称
     *     subject_id  科目id
     *     parent_id   一级分类id
     *     child_id    二级分类id
     *     describe    题库描述
     * ]
     * @param author    dzj
     * @param ctime     2020-05-06
     */
    public function doInsertBank() {
        //获取提交的参数
        try{
            $data = Bank::doInsertBank(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '添加成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   更新题库的方法
     * @param  参数说明         body包含以下参数[
     *     bank_id     题库id
     *     topic_name  题库名称
     *     subject_id  科目id
     *     parent_id   一级分类id
     *     child_id    二级分类id
     *     describe    题库描述
     * ]
     * @param author    dzj
     * @param ctime     2020-05-06
     */
    public function doUpdateBank() {
        //获取提交的参数
        try{
            $data = Bank::doUpdateBank(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '更新成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  descriptsion    删除题库的方法
     * @param  参数说明         body包含以下参数[
     *      bank_id   题库id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public function doDeleteBank() {
        //获取提交的参数
        try{
            $data = Bank::doDeleteBank(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '删除成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  descriptsion    题库开启/关闭的方法
     * @param  参数说明         body包含以下参数[
     *      bank_id   题库id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-06
     * return  array
     */
    public function doOpenCloseBank() {
        //获取提交的参数
        try{
            $data = Bank::doOpenCloseBank(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '操作成功']);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  descriptsion    根据题库id获取题库详情信息
     * @param  参数说明         body包含以下参数[
     *     bank_id   题库id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-06
     * return  array
     */
    public function getBankInfoById(){
        //获取提交的参数
        try{
            $data = Bank::getBankInfoById(self::$accept_data);
            if($data['code'] == 200){
                return response()->json(['code' => 200 , 'msg' => '获取题库信息成功' , 'data' => $data['data']]);
            } else {
                return response()->json(['code' => $data['code'] , 'msg' => $data['msg']]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
