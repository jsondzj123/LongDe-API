<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;

class QuestionSubject extends Model {
    //指定别的表名
    public $table      = 'ld_question_subject';
    //时间戳设置
    public $timestamps = false;

    /*
     * @param  description   添加题库科目方法
     * @param  data          数组数据
     * @param  author        dzj
     * @param  ctime         2020-04-29
     * return  int
     */
    public static function insertSubject($data) {
        return self::insertGetId($data);
    }

    /*
     * @param  descriptsion    获取题库科目列表
     * @param  参数说明         body包含以下参数[
     *     bank_id   题库id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-25
     * return  array
     */
    public static function getSubjectList($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断科目id是否合法
        if(!isset($body['bank_id']) || empty($body['bank_id']) || $body['bank_id'] <= 0){
            return ['code' => 202 , 'msg' => '科目id不合法'];
        }

        //获取题库科目列表
        $subject_list = self::where(function($query) use ($body){
            //删除状态
            $query->where('is_del' , '=' , 0);
            
            //题库id
            $query->where('bank_id' , '=' , $body['bank_id']);
        })->select('id as subject_id','subject_name')->orderByDesc('create_at')->get();
        return ['code' => 200 , 'msg' => '获取题库科目列表成功' , 'data' => $subject_list];
    }

    /*
     * @param  descriptsion    更改题库科目的方法
     * @param  参数说明         body包含以下参数[
     *     subject_id   科目id
     *     subject_name 题库科目名称
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public static function doUpdateSubject($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断题库科目id是否合法
        if(!isset($body['subject_id']) || empty($body['subject_id']) || $body['subject_id'] <= 0){
            return ['code' => 202 , 'msg' => '题库科目id不合法'];
        }

        //判断科目名称书否为空
        if(!isset($body['subject_name']) || empty($body['subject_name'])){
            return ['code' => 201 , 'msg' => '请输入科目名称'];
        }

        //获取科目id
        $subject_id = $body['subject_id'];
        
        //将更新时间追加
        $body['update_at'] = date('Y-m-d H:i:s');
        unset($body['subject_id']);

        //根据讲师或教务id更新信息
        if(false !== self::where('id',$subject_id)->update($body)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   1  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doUpdateSubject' , 
                'operate_method' =>  'update' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '更新成功'];
        } else {
            return ['code' => 203 , 'msg' => '更新失败'];
        }
    }


    /*
     * @param  description   增加题库科目的方法
     * @param  参数说明       body包含以下参数[
     *     subject_name    科目名称
     *     bank_id         题库id
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
     * return string
     */
    public static function doInsertSubject($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断题库id是否合法
        if(isset($body['bank_id']) && $body['bank_id'] <= 0){
            return ['code' => 202 , 'msg' => '题库id不合法'];
        }

        //判断题库科目名称是否为空
        if(!isset($body['subject_name']) || empty($body['subject_name'])){
            return ['code' => 201 , 'msg' => '请输入科目名称'];
        }

        //将后台人员id追加
        $body['admin_id']   = 1;
        $body['create_at']  = date('Y-m-d H:i:s');

        //将数据插入到表中
        $subject_id = self::insertSubject($body);
        if($subject_id && $subject_id > 0){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   1  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doInsertSubject' , 
                'operate_method' =>  'insert' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '添加成功' , 'data' => $subject_id];
        } else {
            return ['code' => 203 , 'msg' => '添加失败'];
        }
    }

    /*
     * @param  descriptsion    删除题库科目的方法
     * @param  参数说明         body包含以下参数[
     *      subject_id   科目id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public static function doDeleteSubject($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断题库科目id是否合法
        if(!isset($body['subject_id']) || empty($body['subject_id']) || $body['subject_id'] <= 0){
            return ['code' => 202 , 'msg' => '题库科目id不合法'];
        }

        //追加更新时间
        $data = [
            'is_del'     => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];

        //根据题库科目id更新删除状态
        if(false !== self::where('id',$body['subject_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   1  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doDeleteSubject' , 
                'operate_method' =>  'delete' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '删除成功'];
        } else {
            return ['code' => 203 , 'msg' => '删除失败'];
        }
    }
    
    /*
     * @param  descriptsion    根据题库科目id批量更新题库id
     * @param  参数说明         body包含以下参数[
     *      bank_id       题库id
     *      subject_ids   科目id[1,2,3,4]
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public static function doUpdateBankIds($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断题库id是否合法
        if(!isset($body['bank_id']) || empty($body['bank_id']) || $body['bank_id'] <= 0){
            return ['code' => 202 , 'msg' => '题库id不合法'];
        }

        //判断题库科目id是否合法
        if(!isset($body['subject_ids']) || empty($body['subject_ids'])){
            return ['code' => 202 , 'msg' => '题库科目id不合法'];
        }

        //根据题库科目id更新题库
        $subject_ids = explode(',',$body['subject_ids']);
        if(false !== self::whereIn('id',$subject_ids)->update(['bank_id' => $body['bank_id'] , 'update_at' => date('Y-m-d H:i:s')])){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   1  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doUpdateBankIds' , 
                'operate_method' =>  'delete' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '更新成功'];
        } else {
            return ['code' => 203 , 'msg' => '更新失败'];
        }
    }
}
