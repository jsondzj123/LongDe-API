<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonSchool;
use Illuminate\Http\Request;
use App\Tools\CurrentAdmin;
use DB;
use Validator;

class LessonSchoolController extends Controller {

    /**
     * @param  分校课程列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $subject_id = $request->input('subject_id') ?: [];
        //$subjectIds = json_decode($subject_id, true);
        $total = LessonSchool::count();
        $lesson = LessonSchool::with('lesson')
                ->orderBy('status', 'desc')
                ->skip($currentCount)->take($count)
                ->get();

        $data = [
            'page_data' => $lesson,
            'total' => $total,
        ];
        return $this->response($data);
    }



    /*
     * @param  课程详情
     * @param  课程id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function show($id) {
        $lesson = LessonSchool::with('lesson')->find($id);
        if(empty($lesson)){
            return $this->response('不存在', 404);
        }
        return $this->response($lesson);
    }


    /**
     * 授权添加课程.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|json',
            'school_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $user = CurrentAdmin::user();

        $lessonIds = json_decode($request->input('lesson_id'), true);

        try {
                foreach ($lessonIds as $value) {
                    LessonSchool::create([
                        'admin_id' => intval($user->id),
                        'lesson_id' => $value,
                        'school_id' => $request->input('school_id'),
                    ]);
                }
            
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }


    /**
     * 修改授权课程内容
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'keyword' => 'required',
            'cover' => 'required',
            'price' => 'required',
            'favorable_price' => 'required',
            'method' => 'required',
            'teacher_id' => 'required',
            'description' => 'required',
            'introduction' => 'required',
            'subject_id' => 'required',
            'is_public' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $lesson = Lessonschool::findOrFail($id);;
        $lesson->title = $request->input('title') ?: $lesson->title;
        $lesson->keyword = $request->input('keyword') ?: $lesson->keyword;
        $lesson->cover = $request->input('cover') ?: $lesson->cover;
        $lesson->price = $request->input('price') ?: $lesson->price;
        $lesson->method = $request->input('method') ?: $lesson->method;
        $lesson->description = $request->input('description') ?: $lesson->description;
        $lesson->is_public = $request->input('is_public') ?: $lesson->is_public;
        try {
            $lesson->save();
            return $this->response("修改成功");
        } catch (Exception $e) {
            Log::error('修改课程信息失败' . $e->getMessage());
            return $this->response("修改成功");
        }
    }


    
    /**
     * 添加/修改课程资料
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'url' => 'required|json',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $lesson = Lesson::findOrFail($id);;
        $lesson->url = $request->input('url');
        try {
            $lesson->save();
            return $this->response("修改成功");
        } catch (Exception $e) {
            Log::error('修改课程信息失败' . $e->getMessage());
            return $this->response("修改成功");
        }
    }



    /**
     * 删除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $lesson = Lesson::findOrFail($id);
        $lesson->is_del = 1;
        if (!$lesson->save()) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
