<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class IndexController extends Controller {
    /*
     * @param  description   首页轮播图接口
     * @param author    dzj
     * @param ctime     2020-05-25
     * return string
     */
    public function getChartList() {
        //获取提交的参数
        try{
            $rotation_chart_list = [
                [
                    'chart_id'     =>   1 ,
                    'title'        =>   '轮播图1' ,
                    'jump_url'     =>   'http://www.baidu.com' ,
                    'pic_image'    =>   "https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg" ,
                    'type'         =>   1 ,
                    'lession_info' => [
                        'lession_id'  => 1 ,
                        'lession_name'=> '课程名称1'
                    ]
                ] , 
                [
                    'chart_id'     =>   2 ,
                    'title'        =>   '轮播图2' ,
                    'jump_url'     =>   'http://www.sina.com' ,
                    'pic_image'    =>   "https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg" ,
                    'type'         =>   0 ,
                    'lession_info' =>   [
                        'lession_id'  => 0 ,
                        'lession_name'=> ''
                    ]
                ] ,
                [
                    'chart_id'     =>   3 ,
                    'title'        =>   '轮播图3' ,
                    'jump_url'     =>   'http://www.163.com' ,
                    'pic_image'    =>   "https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg" ,
                    'type'         =>   1 ,
                    'lession_info' => [
                        'lession_id'  => 2 ,
                        'lession_name'=> '课程名称2'
                    ]
                ]
            ];
            return response()->json(['code' => 200 , 'msg' => '获取轮播图列表成功' , 'data' => $rotation_chart_list]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   首页公开课接口
     * @param author    dzj
     * @param ctime     2020-05-25
     * return string
     */
    public function getOpenClassList() { 
        //获取提交的参数
        try{
            $open_class_list = [
                [
                    'open_class_id'     =>   1 ,
                    'cover'             =>   "https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg" ,
                    'teacher_name'      =>   '刘老师' ,
                    'start_date'        =>   '2020-05-25' ,
                    'start_time'        =>   '09:00' ,
                    'end_time'          =>   '12:00' ,
                ] , 
                [
                    'open_class_id'     =>   2 ,
                    'cover'             =>   "https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg" ,
                    'teacher_name'      =>   '张老师' ,
                    'start_date'        =>   '2020-05-26' ,
                    'start_time'        =>   '09:30' ,
                    'end_time'          =>   '12:00' ,
                ] ,
                [
                    'open_class_id'     =>   3 ,
                    'cover'             =>   "https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg" ,
                    'teacher_name'      =>   '杜老师' ,
                    'start_date'        =>   '2020-05-27' ,
                    'start_time'        =>   '10:00' ,
                    'end_time'          =>   '12:00' ,
                ]
            ];
            return response()->json(['code' => 200 , 'msg' => '获取公开课列表成功' , 'data' => $open_class_list]);
            //echo strtotime('2020-05-28 11:00:00').'-'.strtotime('2020-05-28 12:00:00');
            
            //判断公开课列表是否为空
            /*$open_class_count = Lesson::where('is_public' , 1)->where('status' , 2)->where('is_del' , 0)->where('is_forbid' , 0)->where('end_at', '>=' , time())->count();
            if($open_class_count && $open_class_count > 0){
                //获取公开课列表
                $open_class_list = Lesson::select('id' , 'cover' , 'start_at' , 'end_at')->where('is_public' , 1)
                        ->where('status' , 2)->where('is_del' , 0)->where('is_forbid' , 0)->where('end_at', '>=' , time())
                        ->orderBy('start_at' , 'ASC')->offset(0)->limit(3)->get()->toArray();
                
                //新数组赋值
                $lession_array = [];
                
                //循环公开课列表
                foreach($open_class_list as $k=>$v){
                    //根据课程id获取讲师姓名
                    $info = DB::table('ld_lesson_lives')->select("ld_lecturer_educationa.real_name")->where("ld_lesson_lives.lesson_id" , $v['id'])->leftJoin('ld_live_teachers' , function($join){
                        $join->on('ld_lesson_lives.live_id', '=', 'ld_live_teachers.live_id');
                    })->leftJoin("ld_lecturer_educationa" , function($join){
                        $join->on('ld_live_teachers.teacher_id', '=', 'ld_lecturer_educationa.id')->where("ld_lecturer_educationa.type" , 2);
                    })->first();
                    
                    //新数组赋值
                    $lession_array[] = [
                        'open_class_id'  =>  $v['id'] ,
                        'cover'          =>  $v['cover'] && !empty($v['cover']) ? $v['cover'] : '' ,
                        'teacher_name'   =>  $info && !empty($info) ? $info->real_name : '' ,
                        'start_date'     =>  date('Y-m-d' , $v['start_at']) ,
                        'start_time'     =>  date('H:i' , $v['start_at']) ,
                        'end_time'       =>  date('H:i' , $v['end_at']) 
                    ];
                }
                return response()->json(['code' => 200 , 'msg' => '获取公开课列表成功' , 'data' => $lession_array]);
            } else {
                return response()->json(['code' => 200 , 'msg' => '获取公开课列表成功' , 'data' => ""]);
            }*/
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   首页讲师接口
     * @param author    dzj
     * @param ctime     2020-05-25
     * return string
     */
    public function getTeacherList() {
        //获取提交的参数
        try{
            /*$teacher_list = [
                [
                    'teacher_id'            =>   1 ,
                    'real_name'             =>   "张老师" ,
                    'head_icon'             =>   'https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg' ,
                    'lession_parent_name'   =>   '大分类名称1' ,
                    'lession_child_name'    =>   '小分类名称1'
                ] , 
                [
                    'teacher_id'            =>   2 ,
                    'real_name'             =>   "刘老师" ,
                    'head_icon'             =>   'https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg' ,
                    'lession_parent_name'   =>   '大分类名称2' ,
                    'lession_child_name'    =>   '小分类名称2'
                ] ,
                [
                    'teacher_id'            =>   3 ,
                    'real_name'             =>   "杜老师" ,
                    'head_icon'             =>   'https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg' ,
                    'lession_parent_name'   =>   '大分类名称3' ,
                    'lession_child_name'    =>   '小分类名称3'
                ]
            ];*/
            
            //判断讲师列表是否为空
            $teacher_count = Teacher::where("is_del" , 0)->where("is_forbid" , 0)->where("is_recommend" , 1)->where("type" , 2)->count();
            if($teacher_count && $teacher_count > 0){
                //新数组赋值
                $teacher_array = [];
                
                //获取讲师列表
                $teacher_list  = Teacher::where("is_del" , 0)->where("is_forbid" , 0)->where("is_recommend" , 1)->where("type" , 2)->offset(0)->limit(6)->get()->toArray();
                foreach($teacher_list as $k=>$v){
                    //根据大分类的id获取大分类的名称
                    if($v['parent_id'] && $v['parent_id'] > 0){
                        $lession_parent_name = Subject::where("id" , $v['parent_id'])->where("status" , 1)->where("is_del" , 0)->where("is_forbid" , 0)->value("name");
                    }
                    
                    //根据小分类的id获取小分类的名称
                    if($v['child_id'] && $v['child_id'] > 0){
                        $lession_child_name  = Subject::where("id" , $v['child_id'])->where("status" , 1)->where("is_del" , 0)->where("is_forbid" , 0)->value("name");
                    }

                    //数组赋值
                    $teacher_array[] = [
                        'teacher_id'   =>   $v['id'] ,
                        'real_name'    =>   $v['real_name'] ,
                        'head_icon'    =>   $v['head_icon'] ,
                        'lession_parent_name' => $v['parent_id'] > 0 ? !empty($lession_parent_name) ? $lession_parent_name : '' : '',
                        'lession_child_name'  => $v['child_id']  > 0 ? !empty($lession_child_name)  ? $lession_child_name  : '' : ''
                    ];
                }
                return response()->json(['code' => 200 , 'msg' => '获取讲师列表成功' , 'data' => $teacher_array]);
            } else {
                return response()->json(['code' => 200 , 'msg' => '获取讲师列表成功' , 'data' => ""]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    
    /*
     * @param  description   APP版本升级接口
     * @param author    dzj
     * @param ctime     2020-05-27
     * return string
     */
    public function checkVersion() {
        try {
            /*$version_info = [
                'is_online'         =>   1 ,
                'is_mustup'         =>   1 ,
                'version'           =>   'v1.0' ,
                'content'           =>   [
                    '1.导师主页咨询入口增加「微咨询」选项。' ,
                    '2.导师服务评价规则更新。' ,
                    '3.专家圈列表增加搜索功能。' ,
                    '4.小课部分增加讨论人数。' ,
                    '5.「微咨询」页面优化。'
                ],
                'download_url'      => "http://www.baidu.com"
            ];*/
            
            //获取版本的最新更新信息
            $version_info = DB::table('ld_version')->select('is_online','is_mustup','version','content','download_url')->orderBy('create_at' , 'DESC')->first();
            $version_info->content = json_decode($version_info->content , true);
            return response()->json(['code' => 200 , 'msg' => '获取版本升级信息成功' , 'data' => $version_info]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
