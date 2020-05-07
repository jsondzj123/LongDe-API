<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminLog;
use Validator;

class Papers extends Model {
    //指定别的表名
    public $table      = 'ld_question_papers';
    //时间戳设置
    public $timestamps = false;
    
    /*
     * @param  description   增加试卷的方法
     * @param  参数说明       body包含以下参数[
     *     subject_id      科目id
     *     bank_id         题库id
     *     papers_name     试卷名称
     *     diffculty       试题类型(1代表真题,2代表模拟题,3代表其他)
     *     papers_time     答题时间
     *     area            所属区域
     *     cover_img       封面图片
     *     content         试卷描述
     *     type            选择题型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)
     * ]
     * @param author    dzj
     * @param ctime     2020-05-07
     * return string
     */
    public static function doInsertPapers($body=[]){
        //规则结构
        $rule = [
            'subject_id'     =>   'bail|required|numeric|min:1' ,
            'bank_id'        =>   'bail|required|numeric|min:1' ,
            'papers_name'    =>   'bail|required' ,
            'diffculty'      =>   'bail|required|numeric|between:1,3' ,
            'papers_time'    =>   'bail|required|numeric' ,
            'area'           =>   'bail|required|numeric|min:1' ,
            'cover_img'      =>   'bail|required' ,
            'content'        =>   'bail|required' ,
            'type'           =>   'bail|required'
        ];
        
        //信息提示
        $message = [
            'subject_id.required'   =>  json_encode(['code'=>201,'msg'=>'科目id为空']) ,
            'subject_id.min'        =>  json_encode(['code'=>202,'msg'=>'科目id不合法']) ,
            'bank_id.required'      =>  json_encode(['code'=>201,'msg'=>'题库id为空']) ,
            'bank_id.min'           =>  json_encode(['code'=>202,'msg'=>'题库id不合法']) ,
            'papers_name.required'  =>  json_encode(['code'=>201,'msg'=>'试卷名称为空']) ,
            'diffculty.required'    =>  json_encode(['code'=>201,'msg'=>'请选择试题类型']) ,
            'diffculty.between'     =>  json_encode(['code'=>202,'msg'=>'试题类型不合法']) ,
            'papers_time.required'  =>  json_encode(['code'=>201,'msg'=>'请输入答题时间']) ,
            'area.required'         =>  json_encode(['code'=>201,'msg'=>'请选择所属区域']) ,
            'area.min'              =>  json_encode(['code'=>202,'msg'=>'所属区域不合法']) ,
            'cover_img.required'    =>  json_encode(['code'=>201,'msg'=>'请上传封面图片']) ,
            'content.required'      =>  json_encode(['code'=>201,'msg'=>'请输入试卷描述']) ,
            'type.required'         =>  json_encode(['code'=>201,'msg'=>'请选择题型'])
        ];
        
        $validator = Validator::make($body , $rule , $message);
        if ($validator->fails()) {
            return json_decode($validator->errors()->first() , true);
        }
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

        //将后台人员id追加
        $body['admin_id']   = $admin_id;
        $body['create_at']  = date('Y-m-d H:i:s');

        //将数据插入到表中
        $papers_id = self::insertGetId($body);
        if($papers_id && $papers_id > 0){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doInsertPapers' , 
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
     * @param  description   更改试卷的方法
     * @param  参数说明       body包含以下参数[
     *     papers_id       试卷id
     *     papers_name     试卷名称
     *     diffculty       试题类型(1代表真题,2代表模拟题,3代表其他)
     *     papers_time     答题时间
     *     area            所属区域
     *     cover_img       封面图片
     *     content         试卷描述
     *     type            选择题型(1代表单选题2代表多选题3代表不定项4代表判断题5填空题6简答题7材料题)
     * ]
     * @param author    dzj
     * @param ctime     2020-05-07
     * return string
     */
    public static function doUpdatePapers($body=[]){
        //规则结构
        $rule = [
            'papers_id'      =>   'bail|required|numeric|min:1' ,
            'papers_name'    =>   'bail|required' ,
            'diffculty'      =>   'bail|required|numeric|between:1,3' ,
            'papers_time'    =>   'bail|required|numeric' ,
            'area'           =>   'bail|required|numeric|min:1' ,
            'cover_img'      =>   'bail|required' ,
            'content'        =>   'bail|required' ,
            'type'           =>   'bail|required'
        ];
        
        //信息提示
        $message = [
            'papers_id.required'    =>  json_encode(['code'=>201,'msg'=>'试卷id为空']) ,
            'papers_id.min'         =>  json_encode(['code'=>202,'msg'=>'试卷id不合法']) ,
            'papers_name.required'  =>  json_encode(['code'=>201,'msg'=>'试卷名称为空']) ,
            'diffculty.required'    =>  json_encode(['code'=>201,'msg'=>'请选择试题类型']) ,
            'diffculty.between'     =>  json_encode(['code'=>202,'msg'=>'试题类型不合法']) ,
            'papers_time.required'  =>  json_encode(['code'=>201,'msg'=>'请输入答题时间']) ,
            'area.required'         =>  json_encode(['code'=>201,'msg'=>'请选择所属区域']) ,
            'area.min'              =>  json_encode(['code'=>202,'msg'=>'所属区域不合法']) ,
            'cover_img.required'    =>  json_encode(['code'=>201,'msg'=>'请上传封面图片']) ,
            'content.required'      =>  json_encode(['code'=>201,'msg'=>'请输入试卷描述']) ,
            'type.required'         =>  json_encode(['code'=>201,'msg'=>'请选择题型'])
        ];
        
        $validator = Validator::make($body , $rule , $message);
        if ($validator->fails()) {
            return json_decode($validator->errors()->first() , true);
        }
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

        //获取试卷id
        $papers_id = $body['papers_id'];
        
        //将更新时间追加
        $body['update_at'] = date('Y-m-d H:i:s');
        unset($body['papers_id']);

        //根据试卷id更新信息
        if(false !== self::where('id',$papers_id)->update($body)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doUpdatePapers' , 
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
     * @param  descriptsion    删除试卷的方法
     * @param  参数说明         body包含以下参数[
     *      papers_id   试卷id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-07
     * return  array
     */
    public static function doDeletePapers($body=[]) {
        //规则结构
        $rule = [
            'papers_id'   =>   'bail|required|min:1'
        ];
        
        //信息提示
        $message = [
            'papers_id.required'    =>  json_encode(['code'=>201,'msg'=>'试卷id为空']) ,
            'papers_id.min'         =>  json_encode(['code'=>202,'msg'=>'试卷id不合法']) ,
        ];
        
        $validator = Validator::make($body , $rule , $message);
        if ($validator->fails()) {
            return json_decode($validator->errors()->first() , true);
        }

        //追加更新时间
        $data = [
            'is_del'     => 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

        //根据试卷id更新删除状态
        if(false !== self::where('id',$body['papers_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doDeletePapers' , 
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
     * @param  descriptsion    试卷发布/取消发布的方法
     * @param  参数说明         body包含以下参数[
     *      papers_id   试卷id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-07
     * return  array
     */
    public static function doPublishPapers($body=[]) {
        //规则结构
        $rule = [
            'papers_id'   =>   'bail|required|min:1'
        ];
        
        //信息提示
        $message = [
            'papers_id.required'    =>  json_encode(['code'=>201,'msg'=>'试卷id为空']) ,
            'papers_id.min'         =>  json_encode(['code'=>202,'msg'=>'试卷id不合法']) ,
        ];
        
        $validator = Validator::make($body , $rule , $message);
        if ($validator->fails()) {
            return json_decode($validator->errors()->first() , true);
        }
        
        //根据试卷的id获取试卷的状态
        $is_publish = self::where('id',$body['papers_id'])->pluck('is_publish');

        //追加更新时间
        $data = [
            'is_publish' => $is_publish[0] > 0 ? 0 : 1 ,
            'update_at'  => date('Y-m-d H:i:s')
        ];
        
        //获取后端的操作员id
        $admin_id = isset(AdminLog::getAdminInfo()->admin_user->id) ? AdminLog::getAdminInfo()->admin_user->id : 0;

        //根据试卷id更新试卷状态
        if(false !== self::where('id',$body['papers_id'])->update($data)){
            //添加日志操作
            AdminLog::insertAdminLog([
                'admin_id'       =>   $admin_id  ,
                'module_name'    =>  'Question' ,
                'route_url'      =>  'admin/question/doPublishPapers' , 
                'operate_method' =>  'update' ,
                'content'        =>  json_encode($body) ,
                'ip'             =>  $_SERVER["REMOTE_ADDR"] ,
                'create_at'      =>  date('Y-m-d H:i:s')
            ]);
            return ['code' => 200 , 'msg' => '操作成功'];
        } else {
            return ['code' => 203 , 'msg' => '操作失败'];
        }
    }
    
    
    /*
     * @param  descriptsion    根据试卷id获取试卷详情信息
     * @param  参数说明         body包含以下参数[
     *     papers_id   试卷id
     * ]
     * @param  author          dzj
     * @param  ctime           2020-05-07
     * return  array
     */
    public static function getPapersInfoById($body=[]) {
        //规则结构
        $rule = [
            'papers_id'   =>   'bail|required|min:1'
        ];
        
        //信息提示
        $message = [
            'papers_id.required'    =>  json_encode(['code'=>201,'msg'=>'试卷id为空']) ,
            'papers_id.min'         =>  json_encode(['code'=>202,'msg'=>'试卷id不合法']) ,
        ];
        
        $validator = Validator::make($body , $rule , $message);
        if ($validator->fails()) {
            return json_decode($validator->errors()->first() , true);
        }

        //根据id获取试卷详细信息
        $papers_info = self::select('papers_name','diffculty','papers_time','area','cover_img','content','type')->findOrFail($body['papers_id']);
        return ['code' => 200 , 'msg' => '获取试卷信息成功' , 'data' => $papers_info];
    }
}
