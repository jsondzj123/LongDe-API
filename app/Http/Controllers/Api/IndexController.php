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
    
    /*
     * @param  description   公开课列表接口
     * @param author    dzj
     * @param ctime     2020-05-25
     * return string
     */
    public function getOpenPublicList() {
        //获取提交的参数
        try{
            $pagesize = isset(self::$accept_data['pagesize']) && self::$accept_data['pagesize'] > 0 ? self::$accept_data['pagesize'] : 15;
            $page     = isset(self::$accept_data['page']) && self::$accept_data['page'] > 0 ? self::$accept_data['page'] : 1;
            $offset   = ($page - 1) * $pagesize;
            $today_class     = [];
            $tomorrow_class  = [];
            $over_class      = [];
            $arr             = [];
            $lession_list= DB::table('ld_lessons')->select(DB::raw("any_value(id) as id") , DB::raw("any_value(cover) as cover") , DB::raw("any_value(start_at) as start_at") , DB::raw("any_value(end_at) as end_at") , DB::raw("from_unixtime(start_at , '%Y-%m-%d') as start_time"))->where('is_public',1)->where('is_del',0)->where('is_forbid',0)->where('status',2)->orderBy('start_at' , 'DESC')->groupBy('start_time')->offset($offset)->limit($pagesize)->get()->toArray();
            //判读公开课列表是否为空
            if($lession_list && !empty($lession_list)){
                foreach($lession_list as $k=>$v){
                    //获取当天公开课列表的数据
                    if($v->start_time == date('Y-m-d')){
                        //根据开始日期和结束日期进行查询
                        $class_list = DB::table('ld_lessons')->select('id as open_class_id' , 'title' , 'cover' , DB::raw("from_unixtime(start_at , '%H:%i') as start_time") , DB::raw("from_unixtime(end_at , '%H:%i') as end_time") , 'start_at' , 'end_at')->where('start_at' , '>=' , strtotime($v->start_time.' 00:00:00'))->where('end_at' , '<=' , strtotime($v->start_time.' 23:59:59'))->where('is_public',1)->where('is_del',0)->where('is_forbid',0)->where('status',2)->orderBy('start_at' , 'ASC')->get()->toArray();
                        $today_arr = [];
                        foreach($class_list as $k1=>$v1){
                            //判断课程状态
                            if($v1->end_at < time()){
                                $status = 3;
                            } elseif($v1->start_at > time()){
                                $status = 2;
                            } else {
                                $status = 1;
                            }
                            //封装数组
                            $today_arr[] = [
                                'open_class_id'       =>   $v1->open_class_id  ,
                                'cover'               =>   $v1->cover ,
                                'start_time'          =>   $v1->start_time ,
                                'end_time'            =>   $v1->end_time ,
                                'open_class_name'     =>   $v1->title ,
                                'status'              =>   $status
                            ];
                        }
                        //课程时间点排序
                        array_multisort(array_column($today_arr, 'start_time') , SORT_ASC , $today_arr);
                        //公开课日期赋值
                        $today_class[$v->start_time]['open_class_date']   = $v->start_time;
                        //公开课列表赋值
                        $today_class[$v->start_time]['open_class_list']   = $today_arr;
                    } else if($v->start_time > date('Y-m-d')) {
                        //公开课日期赋值
                        $class_list = DB::table('ld_lessons')->select('id as open_class_id' , 'title' , 'cover' , DB::raw("from_unixtime(start_at , '%H:%i') as start_time") , DB::raw("from_unixtime(end_at , '%H:%i') as end_time") , 'start_at' , 'end_at')->where("start_at" , ">" , strtotime($v->start_time.' 00:00:00'))->where("end_at" , "<" , strtotime($v->start_time.' 23:59:59'))->where('is_public',1)->where('is_del',0)->where('is_forbid',0)->where('status',2)->orderBy('start_at' , 'ASC')->get()->toArray();
                        $date2_arr = [];
                        foreach($class_list as $k2=>$v2){
                            $date2_arr[] = [
                                'open_class_id'       =>   $v2->open_class_id  ,
                                'cover'               =>   $v2->cover ,
                                'start_time'          =>   $v2->start_time ,
                                'end_time'            =>   $v2->end_time ,
                                'open_class_name'     =>   $v2->title ,
                                'status'              =>   2
                            ];
                        }
                        //课程时间点排序
                        array_multisort(array_column($date2_arr, 'start_time') , SORT_ASC , $date2_arr);
                        //公开课日期赋值
                        $tomorrow_class[$v->start_time]['open_class_date']   = $v->start_time;
                        //公开课列表赋值
                        $tomorrow_class[$v->start_time]['open_class_list']   = $date2_arr;
                    } else {
                        //公开课日期赋值
                        $class_list = DB::table('ld_lessons')->select('id as open_class_id' , 'title' , 'cover' , DB::raw("from_unixtime(start_at , '%H:%i') as start_time") , DB::raw("from_unixtime(end_at , '%H:%i') as end_time") , 'start_at' , 'end_at')->where("start_at" , ">" , strtotime($v->start_time.' 00:00:00'))->where("end_at" , "<" , strtotime($v->start_time.' 23:59:59'))->where('is_public',1)->where('is_del',0)->where('is_forbid',0)->where('status',2)->orderBy('start_at' , 'ASC')->get()->toArray();
                        $date_arr = [];
                        foreach($class_list as $k2=>$v2){
                            $date_arr[] = [
                                'open_class_id'       =>   $v2->open_class_id  ,
                                'cover'               =>   $v2->cover ,
                                'start_time'          =>   $v2->start_time ,
                                'end_time'            =>   $v2->end_time ,
                                'open_class_name'     =>   $v2->title ,
                                'status'              =>   3
                            ];
                        }
                        //课程时间点排序
                        array_multisort(array_column($date_arr, 'start_time') , SORT_ASC , $date_arr);
                        //公开课日期赋值
                        $over_class[$v->start_time]['open_class_date']   = $v->start_time;
                        //公开课列表赋值
                        $over_class[$v->start_time]['open_class_list']   = $date_arr;
                    }
                }
                //判断明天课程是否为空
                if($tomorrow_class && !empty($tomorrow_class)){
                    //课程时间点排序
                    array_multisort(array_column($tomorrow_class, 'open_class_date') , SORT_ASC , $tomorrow_class);
                }
                $arr =  array_merge(array_values($today_class) , array_values($tomorrow_class) , array_values($over_class));
            } 
            return response()->json(['code' => 200 , 'msg' => '获取公开课列表成功' , 'data' => $arr]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   名师列表接口
     * @param author    dzj
     * @param ctime     2020-06-02
     * return string
     */ 
    public function getFamousTeacherList(){
        //获取提交的参数
        try{
            $famous_teacher_list = [
                [
                    'teacher_id'     =>   1 ,
                    'teacher_icon'   =>   'https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg' ,
                    'teacher_name'   =>   '张老师' ,
                    'star_num'       =>   5 ,
                    'lesson_number'  =>   10 ,
                    'student_number' =>   20
                ] , 
                [
                    'teacher_id'     =>   2 ,
                    'teacher_icon'   =>   'https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg' ,
                    'teacher_name'   =>   '梁老师' ,
                    'star_num'       =>   7 ,
                    'lesson_number'  =>   12 ,
                    'student_number' =>   100
                ] ,
                [
                    'teacher_id'     =>   3 ,
                    'teacher_icon'   =>   'https://dss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3256100974,305075936&fm=26&gp=0.jpg' ,
                    'teacher_name'   =>   '刘老师' ,
                    'star_num'       =>   9 ,
                    'lesson_number'  =>   9 ,
                    'student_number' =>   29
                ]
            ];
            return response()->json(['code' => 200 , 'msg' => '获取名师列表成功' , 'data' => $famous_teacher_list]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   名师详情接口
     * @param author    dzj
     * @param ctime     2020-06-02
     * return string
     */ 
    public function getFamousTeacherInfo(){
        //获取提交的参数
        try{
            //获取名师id
            $teacher_id  = isset(self::$accept_data['teacher_id']) && !empty(self::$accept_data['teacher_id']) && self::$accept_data['teacher_id'] > 0 ? self::$accept_data['teacher_id'] : 0;
            if(!$teacher_id || $teacher_id <= 0 || !is_numeric($teacher_id)){
                return response()->json(['code' => 202 , 'msg' => '名师id不合法']);
            }
            
            //空数组赋值
            $teacher_array = "";
            
            //根据名师的id获取名师的详情信息
            $teacher_info  =  Teacher::where('id' , $teacher_id)->where('type' , 2)->where('is_del' , 0)->where('is_forbid' , 0)->first();
            if($teacher_info && !empty($teacher_info)){
                //名师数组信息
                $teacher_array = [
                    'teacher_icon'   =>   $teacher_info->head_icon  ,
                    'teacher_name'   =>   $teacher_info->real_name  ,
                    'teacher_content'=>   $teacher_info->content
                ];
            }
            return response()->json(['code' => 200 , 'msg' => '获取名师详情成功' , 'data' => $teacher_array]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
    
    /*
     * @param  description   名师课程列表接口
     * @param author    dzj
     * @param ctime     2020-06-02
     * return string
     */ 
    public function getTeacherLessonList(){
        //获取提交的参数
        try{
            //获取名师id
            $teacher_id  = isset(self::$accept_data['teacher_id']) && !empty(self::$accept_data['teacher_id']) && self::$accept_data['teacher_id'] > 0 ? self::$accept_data['teacher_id'] : 0;
            if(!$teacher_id || $teacher_id <= 0 || !is_numeric($teacher_id)){
                return response()->json(['code' => 202 , 'msg' => '名师id不合法']);
            }
            
            //分页相关的参数
            $pagesize = isset(self::$accept_data['pagesize']) && self::$accept_data['pagesize'] > 0 ? self::$accept_data['pagesize'] : 15;
            $page     = isset(self::$accept_data['page']) && self::$accept_data['page'] > 0 ? self::$accept_data['page'] : 1;
            $offset   = ($page - 1) * $pagesize;
            
            //获取名师课程列表
            $teacher_lesson_list = DB::table('ld_lessons as l')->select("l.id as lesson_id","l.title","l.cover","l.price","l.favorable_price","l.method")->leftJoin('ld_lesson_teachers as t' , function($join){
                        $join->on('l.id', '=', 't.lesson_id');
                    })->where("t.teacher_id",$teacher_id)->where("l.is_public" , 0)->where("l.is_del" , 0)->where("l.is_forbid" , 0)->offset($offset)->limit($pagesize)->get()->toArray();
            if($teacher_lesson_list && !empty($teacher_lesson_list)){
                return response()->json(['code' => 200 , 'msg' => '获取名师课程列表成功' , 'data' => $teacher_lesson_list]);
            } else {
                return response()->json(['code' => 200 , 'msg' => '获取名师课程列表成功' , 'data' => ""]);
            }
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }


    /**
     * @param  description   首页学科接口
     * @param author    sxl
     * @param ctime     2020-05-28
     * @return string
     */
    public function getSubjectList() {
        //获取提交的参数
        try{
            $subject = Subject::select('id', 'name')->where('pid', 0)->limit(6)->get();
            //dd($subject);
            return $this->response($subject);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }

    /**
     * @param  description   首页课程接口
     * @param author    sxl
     * @param ctime     2020-05-28
     * @return string
     */
    public function getLessonList() {
        //获取提交的参数
        try{
            $subject = Subject::select('id', 'name')->where('pid', 0)->limit(4)->get();
            $lessons = [];
            foreach ($subject as $key => $value) {
                $lessons[$key]['subject'] = $value;
                $lessons[$key]['lesson'] = Lesson::select('id', 'title', 'cover', 'buy_num', 'price as old_price', 'favorable_price')
                                            ->with(['subjects' => function ($query) {
                                                $query->select('id', 'name');
                                            }])
                                            ->whereHas('subjects', function ($query) use ($value)
                                                {
                                                    $query->where('id', $value->id);
                                                })->get();
            }
            return $this->response($lessons);
        } catch (Exception $ex) {
            return $this->response($ex->getMessage());
        }
    }
}
