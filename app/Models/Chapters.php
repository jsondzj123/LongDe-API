<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;

class Chapters extends Model {
    //指定别的表名
    public $table      = 'ld_question_chapters';
    //时间戳设置
    public $timestamps = false;

    /*
     * @param  description   添加章节考点方法
     * @param  data          数组数据
     * @param  author        dzj
     * @param  ctime         2020-04-29
     * return  int
     */
    public static function insertChapters($data) {
        return self::insertGetId($data);
    }

    /*
     * @param  descriptsion    获取章节考点列表
     * @param  参数说明         body包含以下参数[
     *     bank_id     题库id
     *     subject_id  科目id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public static function getChaptersList($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断题库id是否合法
        if(!isset($body['bank_id']) || empty($body['bank_id']) || $body['bank_id'] <= 0){
            return ['code' => 202 , 'msg' => '题库id不合法'];
        }
        
        //判断科目id是否合法
        if(!isset($body['subject_id']) || empty($body['subject_id']) || $body['subject_id'] <= 0){
            return ['code' => 202 , 'msg' => '科目id不合法'];
        }

        //获取题库章节考点列表
        $chapters_list = self::where(function($query) use ($body){
            //删除状态
            $query->where('is_del' , '=' , 0);
            
            //题库id
            $query->where('bank_id' , '=' , $body['bank_id']);
            
            //科目id
            $query->where('subject_id' , '=' , $body['subject_id']);
        })->select('id','name','parent_id')->get()->toArray();
        return ['code' => 200 , 'msg' => '获取章节考点列表成功' , 'data' => self::getParentsList($chapters_list)];
    }
    
    /*
     * @param  descriptsion    实现三级分类的列表
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public static function getParentsList($categorys,$pId = 0,$l=0){
        $list =array();
        foreach ($categorys as $k=>$v){
            if ($v['parent_id'] == $pId){
                unset($categorys[$k]);
                if ($l < 2){
                    $v['children'] = self::getParentsList($categorys,$v['id'],$l+1);
                }
                $list[] = $v;
            }
        }
        return $list;
    }

    /*
     * @param  description   更改章节考点的方法
     * @param  参数说明       body包含以下参数[
     *     chapters_id       章节考点id
     *     name              章节考点名称
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
     * return string
     */
    public static function doUpdateChapters($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断章节考点id是否合法
        if(!isset($body['chapters_id']) || empty($body['chapters_id']) || $body['chapters_id'] <= 0){
            return ['code' => 202 , 'msg' => 'id不合法'];
        }

        //判断名称书否为空
        if(!isset($body['name']) || empty($body['name'])){
            return ['code' => 201 , 'msg' => '请输入名称'];
        }

        //获取章节考点id
        $chapters_id = $body['chapters_id'];
        
        //将更新时间追加
        $body['update_at'] = date('Y-m-d H:i:s');
        unset($body['chapters_id']);

        //根据id更新信息
        if(false !== self::where('id',$chapters_id)->update($body)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   1  ,
                'module_name'    =>  'Chapters' ,
                'route_url'      =>  'admin/question/doUpdateChapters' , 
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
     * @param  description   增加章节考点的方法
     * @param  参数说明       body包含以下参数[
     *     parent_id         父级id[章id或节id]
     *     subject_id        科目id
     *     bank_id           题库id
     *     name              章节考点名称
     *     type              添加类型(0代表章1代表节2代表考点)
     * ]
     * @param author    dzj
     * @param ctime     2020-04-29
     * return string
     */
    public static function doInsertChapters($body=[]){
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }
        
        //判断添加类型是否合法
        if(!isset($body['type']) || $body['type'] < 0 || !in_array($body['type'] , [0,1,2])){
            return ['code' => 202 , 'msg' => '添加类型不合法'];
        }
        
        //判断题库id是否合法
        if(isset($body['bank_id']) && $body['bank_id'] <= 0){
            return ['code' => 202 , 'msg' => '题库id不合法'];
        }
        
        //判断科目id是否合法
        if(isset($body['subject_id']) && $body['subject_id'] <= 0){
            return ['code' => 202 , 'msg' => '科目id不合法'];
        }
        
        //判断是节添加还是考点添加
        if($body['type'] == 1 && (!isset($body['parent_id']) || $body['parent_id'] <= 0)){ //节添加或考点添加
            return ['code' => 202 , 'msg' => '章id不合法'];
        } else if($body['type'] == 2 && (!isset($body['parent_id']) || $body['parent_id'] <= 0)){
            return ['code' => 202 , 'msg' => '节id不合法'];
        }

        //判断章节考点名称是否为空
        if(!isset($body['name']) || empty($body['name'])){
            return ['code' => 201 , 'msg' => '请输入名称'];
        }

        //将后台人员id追加
        $body['admin_id']   = 1;
        $body['create_at']  = date('Y-m-d H:i:s');

        //将数据插入到表中
        $chapters_id = self::insertChapters($body);
        if($chapters_id && $chapters_id > 0){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   1  ,
                'module_name'    =>  'Chapters' ,
                'route_url'      =>  'admin/question/doInsertChapters' , 
                'operate_method' =>  'insert' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '添加成功'];
        } else {
            return ['code' => 203 , 'msg' => '添加失败'];
        }
    }

    /*
     * @param  descriptsion    删除章节考点的方法
     * @param  参数说明         body包含以下参数[
     *      chapters_id   章节考点id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-04-29
     * return  array
     */
    public static function doDeleteChapters($body=[]) {
        //判断传过来的数组数据是否为空
        if(!$body || !is_array($body)){
            return ['code' => 202 , 'msg' => '传递数据不合法'];
        }

        //判断章节考点id是否合法
        if(!isset($body['chapters_id']) || empty($body['chapters_id']) || $body['chapters_id'] <= 0){
            return ['code' => 202 , 'msg' => 'id不合法'];
        }

        //追加更新时间
        $data = [
            'is_del'     => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];

        //根据题库科目id更新删除状态
        if(false !== self::where('id',$body['chapters_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   1  ,
                'module_name'    =>  'Chapters' ,
                'route_url'      =>  'admin/question/doDeleteChapters' , 
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
}