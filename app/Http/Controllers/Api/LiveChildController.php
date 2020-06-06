<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Tools\MTCloud;
use Log;
use App\Models\Lesson;
use App\Models\LessonLive;
use App\Models\LiveChild;

class LiveChildController extends Controller {



    //课程直播目录
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'lesson_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $lives = Lesson::find($request->input('lesson_id'))->lives->toArray();
        $childs = [];
        if(!empty($lives)){
            foreach ($lives as $key => $value) {
                //直播中
                $childs['live'] = LiveChild::where([
                    'is_del' => 0, 'is_forbid' => 0, 'status' => 2, 'live_id' => $value['id']
                ])->get();
                //预告
                $childs['advance'] = LiveChild::where([
                    'is_del' => 0, 'is_forbid' => 0, 'status' => 1, 'live_id' => $value['id']
                ])->get();
                //回放
                $childs['playback'] = LiveChild::where([
                    'is_del' => 0, 'is_forbid' => 0, 'status' => 3, 'live_id' => $value['id']
                ])->get();
            }
        }
        
        return $this->response($childs);
    }




    //进入直播课程
    public function courseAccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
           'course_id' => 'required',
           'student_id' => 'required',
           'nickname' => 'required',
           'role' => 'required',
           'type' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $MTCloud = new MTCloud();
        // 参数：课程ID
        // 进入直播课程
        if($request->input('type') == 1){
             $res = $MTCloud->courseAccess($request->input('course_id'), $request->input('student_id'), $request->input('nickname'),
                    $request->input('role'));
        }else{
             $res = $MTCloud->courseAccessPlayback(
                 $request->input('course_id'), 
                 $request->input('student_id'), 
                 $request->input('nickname'),
                 $request->input('role'));
        }
        if(!array_key_exists('code', $res) && !$res['code'] == 0){
            Log::error('进入直播间失败:'.json_encode($res));
            return $this->response('进入直播间失败', 500);
        }
        return $this->response($res);
    }
}
