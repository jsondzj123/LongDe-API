<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LessonChild;
use Illuminate\Http\Request;
use DB;
use Validator;

class LessonChildController extends Controller {

    /**
     * @param  小节列表
     * @param  pagesize   page
     * @param  author  孙晓丽
     * @param  ctime   2020/5/26 
     * @return  array
     */
    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required',
            'pid' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $lesson_id = $request->input('lesson_id') ?: 0;
        $pid = $request->input('pid') ?: 0;
        $lessons =  LessonChild::where(['is_del'=> 0, 'is_forbid' => 0, 'pid' => $pid])
                ->orderBy('created_at', 'desc')->get();
        if($pid == 0){
            foreach ($lessons as $key => $value) {
                $value['childs'] = LessonChild::where('pid', $value->id)->get();
            }
        }
        return $this->response($lessons);
    }


    /**
     * @param  小节详情
     * @param  课程id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function show(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $lesson = LessonChild::find($request->input('id'));
        if(empty($lesson)){
            return $this->response('课程小节不存在', 404);
        }
        return $this->response($lesson);
    }
}
