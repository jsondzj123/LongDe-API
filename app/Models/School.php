<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class School extends Model {
    //指定别的表名
    public $table = 'ld_school';
    //时间戳设置
    public $timestamps = false;
    /*
         * @param  descriptsion 获取学校信息
         * @param  $school_id   学校id
         * @param  $field   字段列
         * @param  $page   页码
         * @param  $limit  显示条件
         * @param  author       lys
         * @param  ctime   2020/4/29 
         * return  array
         */
    public static function getSchoolOne($where,$field = ['*']){
        $schoolInfo = self::where($where)->select($field)->first()->toArray();
        if($schoolInfo){
            return ['code'=>200,'msg'=>'获取学校信息成功','data'=>$schoolInfo];
        }else{
            return ['code'=>201,'msg'=>'学校信息不存在'];
        }
    }
    // public static  function getSchoolAll(){

    // }
        /*
         * @param  descriptsion 获取学校信息
         * @param  $field   字段列
         * @param  author       lys
         * @param  ctime   2020/4/30
         * return  array
         */
    public static  function getSchoolAlls($field = ['*']){
        return  self::select($field)->get()->toArray();

    }



}
