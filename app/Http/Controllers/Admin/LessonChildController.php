<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonChild;
use Illuminate\Http\Request;
use App\Tools\CurrentAdmin;
use DB;
use Validator;
use App\Models\Teacher;

class LessonChildController extends Controller {

    /*
     * @param  章节列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/8 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $lesson_id = $request->input('lesson_id');
        $total = LessonChild::where(['is_del' => 0, 'is_forbid' => 0, 'lesson_id' => $lesson_id])->count();
        $lesson = LessonChild::where(['is_del' => 0, 'is_forbid' => 0, 'lesson_id' => $lesson_id])
                ->skip($currentCount)->take($count)
                ->get();
        foreach ($lesson as $k => $value) {
            $lesson[$k]['childs'] = LessonChild::where('pid', $value->id)->get();
        }
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
        $lesson = LessonChild::find($id);
        $lesson['childs'] = LessonChild::where('pid', $id)->get();
        if(empty($lesson)){
            return $this->response('课程不存在', 404);
        }
        return $this->response($lesson);
    }


    /**
     * 添加课程.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($lesson_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'pid'       => 'required',
            'category'  => 'required_unless:pid,0', 
            'url'       => 'required_unless:pid,0',
            'size'      => 'required_unless:pid,0',
            'is_free'   => 'required_unless:pid,0',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $user = CurrentAdmin::user();
        try {
            $lesson = LessonChild::create([
                    'admin_id' => intval($user->id),
                    'lesson_id' => $lesson_id,
                    'name'      => $request->input('name'),
                    'pid'       => $request->input('pid'),
                    'category'  => $request->input('category') ?: 0, 
                    'url'       => $request->input('url'),
                    'size'      => $request->input('size') ?: 0,
                    'is_free'   => $request->input('is_free') ?: 0,
                ]);
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }


    /**
     * Update the specified resource in storage.
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
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $lesson = LessonChild::findOrFail($id);;
        $lesson->title = $request->input('title') ?: $lesson->title;
        $lesson->keyword = $request->input('keyword') ?: $lesson->keyword;
        $lesson->cover = $request->input('cover') ?: $lesson->cover;
        $lesson->price = $request->input('price') ?: $lesson->price;
        $lesson->method = $request->input('method') ?: $lesson->method;
        $lesson->description = $request->input('description') ?: $lesson->description;
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
        $lesson = LessonChild::findOrFail($id);
        $lesson->is_del = 1;
        if (!$lesson->save()) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
