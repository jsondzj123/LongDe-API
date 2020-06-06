<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LessonChild;
use App\Models\LessonVideo;
use App\Models\Video;
use Illuminate\Http\Request;
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
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $lesson_id = $request->input('lesson_id') ?: 0;
        $pid = $request->input('pid') ?: 0;
        $lessons =  LessonChild::select('id', 'name', 'description', 'pid')->where(['is_del'=> 0, 'is_forbid' => 0, 'pid' => 0])
                ->orderBy('created_at', 'desc')->get();
        foreach ($lessons as $key => $value) {
            
            $childs = LessonChild::with('videos')->with(['lives' => function ($query) {
                        $query->with('childs');
                    }])->where(['is_del'=> 0, 'is_forbid' => 0, 'pid' => $value->id])->get();
            $value['childs'] = $childs;
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
        $lesson = LessonChild::with(['lives' => function ($query) {
                $query->with('childs');
            }])->find($request->input('id'));
        if(empty($lesson)){
            return $this->response('课程小节不存在', 404);
        }
        return $this->response($lesson);
    }
}
