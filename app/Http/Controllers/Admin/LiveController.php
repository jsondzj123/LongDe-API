<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Models\Subject;
use Illuminate\Http\Request;
use  App\Tools\CurrentAdmin;
use Validator;
use App\Tools\MTCloud;
use App\Models\LessonLive;
use App\Models\Lesson;
use App\Models\LessonChild;
use App\Models\LiveClassChild;
use App\Models\LiveChild;
use App\Models\Teacher;

class LiveController extends Controller {

    /*
     * @param  直播列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/18 
     * return  array
     */
    public function index(Request $request){
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        $total = Live::where(['is_del'=> 0, 'is_forbid' => 0])->count();
        $live = Live::where(['is_del'=> 0, 'is_forbid' => 0])
            ->orderBy('created_at', 'desc')
            ->skip($offset)->take($pagesize)
            ->get();
        $data = [
            'page_data' => $live,
            'total' => $total,
        ];
        return $this->response($data);
    }


    /*
     * @param  直播详情
     * @param  直播id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/18 
     * return  array
     */
    public function show(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $live = Live::with('subjects')->findOrFail($request->input('id'));
        return $this->response($live);
    }


    /*
     * @param  班号列表
     * @param  直播id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/18 
     * return  array
     */
    public function classList(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $lesson_id = LessonLive::where('live_id', $request->input('id'))->first()['lesson_id'];
        $lessons = LessonChild::select('id', 'name', 'description', 'url', 'is_forbid')->where(['lesson_id' => $lesson_id, 'pid' => 0])->get();
        foreach ($lessons as $key => $value) {
            $childs = LiveChild::select('id', 'course_name', 'start_time', 'end_time', 'account')->whereIn('id' ,LiveClassChild::where('lesson_child_id', $value->id)->pluck('live_child_id'))->get();
            foreach ($childs as $k => $val) {
                $childs[$k]['teacher_name'] = Teacher::find($val->account)->real_name;
            }
            $lessons[$key]['childs'] = $childs;
        }
        return $this->response($lessons);
    }


    /**
     * 添加直播资源.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required',
            'name' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $subjectIds = json_decode($request->input('subject_id'), true);
        $user = CurrentAdmin::user();
        try {
            $live = Live::create([
                        'admin_id' => intval($user->id),
                        'name' => $request->input('name'),
                        'description' => $request->input('description'),
                    ]);
            if(!empty($subjectIds)){
                $live->subjects()->attach($subjectIds);
            }
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }


    /**
     * 直播批量关联课程.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lesson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'lesson_id' => 'required|json',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $lessonIds = json_decode($request->input('lesson_id'), true);
        try {
            $live = Live::find($request->input('id'));
            if(!empty($lessonIds)){
                $live->lessons()->attach($lessonIds); 
            }
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }


    /**
     * 修改直播资源
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $subjectIds = json_decode($request->input('subject_id'), true);
        try {
            $live = Live::findOrFail($request->input('id'));
            $live->name = $request->input('name') ?: $live->name;
            $live->description = $request->input('description') ?: $live->description;
            $live->save();
            $live->subjects()->sync($subjectIds);
            return $this->response("修改成功");
        } catch (Exception $e) {
            Log::error('修改课程信息失败' . $e->getMessage());
            return $this->response("修改成功");
        }
    }


    /**
     * 启用/禁用
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $live = Live::findOrFail($request->input('id'));
        if($live->is_forbid == 1){
            $live->is_forbid = 0;
        }else{
            $live->is_forbid = 1;
        }
        if (!$live->save()) {
            return $this->response("操作失败", 500);
        }
        return $this->response("操作成功");
    }

    /**
     * 删除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $live = Live::findOrFail($request->input('id'));
        $live->is_del = 1;
        if (!$live->save()) {
            return $this->response("操作失败", 500);
        }
        return $this->response("操作成功");
    }
}
