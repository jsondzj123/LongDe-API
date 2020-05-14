<?php

namespace App\Http\Controllers;

use http\Client\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use Maatwebsite\Excel\Facades\Excel;
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
    public function __construct() {
        //self::$accept_data = app('rsa')->servicersadecrypt($request);
        //app('rsa')->Test();
        self::$accept_data = $_REQUEST;
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
            return response()->json(['msg' => $data]);
        } elseif (is_string($data)) {
            return response()->json(['code' => $statusCode, 'msg' => $data]);
        } else {
            return response()->json(['code' => $statusCode, 'msg' => 'success', 'data' => $data]);
        }
        return response()->json($data);
    }
    
    /*
     * @param  description   导入功能方法
     * @param  参数说明[
     *     $imxport      导入文件名称
     *     $excelpath    excel文件路径
     *     $is_limit     是否限制最大表格数量(1代表限制,0代表不限制)
     *     $limit        限制数量
     * ]
     * @param  author        dzj
     * @param  ctime         2020-04-30
    */
    public static function doImportExcel($imxport , $excelpath , $is_limit = 0 , $limit = 0){
        //获取提交的参数
        try{
            //导入数据方法
            $exam_array = Excel::toArray($imxport , $excelpath);
            
            //判断导入的excel文件中是否有信息
            if(!$exam_array || empty($exam_array)){
                return ['code' => 204 , 'msg' => '暂无信息导入'];
            } else {
                $array = [];
                //循环excel表中数据信息
                foreach($exam_array as $v){
                    //去掉header头字段信息(不加入表中)【备注:去掉二维数组中第一个数组】
                    unset($v[0]);
                    foreach($v as $k1 => $v1){
                        //去掉二维数组中最后一个空元素
                        unset($v1[count($v1)-1]);
                        for($i=0;$i<count($v1);$i++){
                            if($v1[$i] && !empty($v1[$i])){
                                $array[$k1] = $v1;
                            }
                        }
                    }
                }
            }
            //判断excel表格中总数量是否超过最大限制
            $max_count = count($array);
            if($is_limit > 0 && $max_count > $limit){
                return ['code' => 202 , 'msg' => '超过最大导入数量'];
            }
            return ['code' => 200 , 'msg' => '获取数据成功' , 'data' => $array];
        } catch (Exception $ex) {
            return ['code' => 500 , 'msg' => $ex->getMessage()];
        }
    }
    
    /*
     * @param  description   检测真实文件后缀格式的方法
     * @param  参数说明[
     *     $file             文件数组
     *     $file["name"]     获取原文件名称
     *     $file["tmp_name"] 临时文件名称
     * ]
     * @param  author        dzj
     * @param  ctime         2020-05-14
     * return  boolean($flag 1表示真实excel , 0表示伪造或者不是excel)
    */
    public static function detectUploadFileMIME($file){
        $flag = 0;
        $file_array = explode (".", $file["name"]);
        $file_extension = strtolower (array_pop($file_array));
        switch ($file_extension) {
            case "xls" :
                // 2003 excel
                $fh  = fopen($file["tmp_name"], "rb");
                $bin = fread($fh, 8);
                fclose($fh);
                $strinfo  = @unpack("C8chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex ($num);
                }
                if ($typecode == "d0cf11e0a1b11ae1") {
                    $flag = 1;
                }
                break;
            case "xlsx" :
                // 2007 excel
                $fh  = fopen($file["tmp_name"], "rb");
                $bin = fread($fh, 4);
                fclose($fh);
                $strinfo = @unpack("C4chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex ($num);
                }
                if ($typecode == "504b34") {
                    $flag = 1;
                }
                break;
        }
        return $flag;
    }
    
   /** delDir()删除文件夹及文件夹内文件函数
    * @param string $path   文件夹路径
    * @param string $delDir 是否删除改
    * @return boolean
    */
    public static function delDir($path, $del = false){
        $handle = opendir($path);
        if ($handle) {
            while (false !== ($item = readdir($handle))) {
                if (($item != ".") && ($item != "..")) {
                    is_dir("$path/$item") ? self::delDir("$path/$item", $del) : unlink("$path/$item");
                }
            }
            closedir($handle);
            if ($del) {
                return rmdir($path);
            }
        }elseif(file_exists($path)) {
            return unlink($path);
        }else {
            return false;
        }
    }
}
