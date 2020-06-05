<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Tools\MTCloud;
use Log;

class LiveChildController extends Controller {


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
