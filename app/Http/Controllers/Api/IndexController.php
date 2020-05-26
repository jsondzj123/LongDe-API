<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
                    'lession_info' =>   []
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
            $teacher_list = [
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
            ];
            return response()->json(['code' => 200 , 'msg' => '获取讲师列表成功' , 'data' => $teacher_list]);
        } catch (Exception $ex) {
            return response()->json(['code' => 500 , 'msg' => $ex->getMessage()]);
        }
    }
}
