<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Tools\MTCloud;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
//        $MTCloud = new MTCloud();
//        $res = $MTCloud->courseGet(1048458);
//        if(!array_key_exists('code', $res) && !$res['code'] == 0){
//            Log::error('进入直播间失败:'.json_encode($res));
//            return $this->response('进入直播间失败', 500);
//        }
//        return $this->response($res['data']);
        $file = isset($_FILES['file']) && !empty($_FILES['file']) ? $_FILES['file'] : '';

        //存放文件路径
        $file_path= app()->basePath() . "/public/upload/excel/";
        //判断上传的文件夹是否建立
        if(!file_exists($file_path)){
            mkdir($file_path , 0777 , true);
        }

        //重置文件名
        $filename = time() . rand(1,10000) . uniqid() . substr($file['name'], stripos($file['name'], '.'));
        $path     = $file_path.$filename;

        //判断文件是否是通过 HTTP POST 上传的
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            //上传文件方法
            move_uploaded_file($_FILES['file']['tmp_name'], $path);
        }

        $exam_list = self::doImportExcel(new \App\Imports\UsersImport , $path);
//        dd($exam_list['data']);
        foreach($exam_list['data'] as $key=>$value){
            $video = Video::where(['course_id' => $value[0]])->first();
            if(empty($video)){
                Video::create([
                    'admin_id' => 1,
                    'name' => $value[0],
                    'category' => 1,
                    'url' => 'test.mp4',
                    'course_id' => $value[1]
                ]);
            }
        }
    }
}
