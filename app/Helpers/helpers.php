<?php

use Illuminate\Support\Facades\Config;

/*返回json串
   * addtime 2020.4.17
   * auther liyinsheng
   * $code  int   状态码
   * $data  array  数据数组
   * return  string
* */
//if (! function_exists('responseJson')) {
    function responseJson($code,$msg='', $data = [])
    {
        $arr = config::get('code');
        if (!in_array($code, $arr)) {
            return response()->json(['code' => 404, 'msg' => '非法请求']);
        }else{
            return response()->json(['code' => $code, 'msg' => $arr[$code], 'data' => $data]);
        }
    }
    /*递归
    * addtime 2020.4.27
    * auther liyinsheng
    * $array  array  数据数组
    * $pid    int  父级id
    * $pid    int  层级
    * return  string
    * */

    function getTree($array, $pid =0, $level = 0){

        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        static $list = [];
        foreach ($array as $key => $value){
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['pid'] == $pid){
                //父节点为根节点的节点,级别为0，也就是第一级
                $value['level'] = $level;
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                getTree($array, $value['id'], $level+1);

            }
        }
        return $list;
    }
//}
?>
