<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Tools\CurrentAdmin;
use DB;
use Validator;
use App\Models\Teacher;
use App\Models\LessonSchool;

class LessonController extends Controller {

    /**
     * @param  课程列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $subject_id = $request->input('subject_id') ?: 0;
        $method = $request->input('method') ?: 0;
        $status = $request->input('status') ?: 0;
        $auth = (int)$request->input('auth') ?: 0;
        $user = CurrentAdmin::user();   
        $data =  Lesson::select('id', 'admin_id', 'title', 'cover', 'price', 'favorable_price', 'buy_num', 'method', 'status', 'is_del', 'is_forbid')
                ->where(['is_del' => 0, 'is_forbid' => 0])

                // ->whereHas('subjects', function ($query) use ($subject_id)
                //     {
                //         if($subject_id != 0){
                //             $query->where('subjects.id', $subject_id);
                //         }
                //     })
                ->where(function($query) use ($method, $status){
                    if($method == 0){
                        $query->whereIn("method", [1, 2, 3]);
                    }else{
                        $query->where("method", $method);
                    }
                    if($status == 0){
                        $query->whereIn("status", [0, 1, 2]);
                    }else{
                        $query->where("status", $status);
                    }
                });
        $lessons = [];
        foreach ($data->get()->toArray() as $value) {
            
            if($auth == 0){
                if($value['is_auth'] == 1 || $value['is_auth'] == 2){
                    $lessons[] = $value;   
                }
                 
            }else{
                if($value['is_auth'] == $auth){
                    $lessons[] = $value;   
                }
            }
        }
        $total = collect($lessons)->count();
        $lesson = collect($lessons)->skip($currentCount)->take($count);
        $data = [
            'page_data' => $lessons,
            'total' => $total,
        ];
        return $this->response($data);
    }

    /**
     * @param  分校课程列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function list()
    {
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $subject_id = $request->input('subject_id');
        $method = $request->input('method');
        $status = $request->input('status');
        $auth = $request->input('auth');
        $user = CurrentAdmin::user();
        //自增课程
        $lesson_ids = Lesson::where('admin_id', $user->id)->pluck('id');
        //授权课程
        $school_lesson_ids = LessonStock::where([
            'school_id' => $user->school_id,
            'is_forbid' => 0])->pluck('lesson_id');
        $ids = $lesson_ids->merge($school_lesson_ids);
        $lesson = Lesson::with('subjects')->whereHas('subjects', function ($query) use ($subject_id)
                        {
                            $query->where('subjects.id', $subject_id);
                        })
                ->whereIn('id', $ids)
                ->get();
        $total = $lesson->count();
        foreach ($lesson as $key=>$value) {
            if(in_array($value->id, $school_lesson_ids->toArray())){
                $lesson[$key]['is_auth'] = 1;
            }else{
                $lesson[$key]['is_auth'] = 0;
            }
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
        $lesson = Lesson::with('teachers', 'subjects')->find($id);
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
    public function store(Request $request)
    {
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
            'buy_num' => 'required',
            'ttl' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $subjectIds = json_decode($request->input('subject_id'), true);
        $teacherIds = json_decode($request->input('teacher_id'), true);
        $user = CurrentAdmin::user();

        try {
            $lesson = Lesson::create([
                    'admin_id' => intval($user->id),
                    'title' => $request->input('title'),
                    'keyword' => $request->input('keyword'),
                    'cover' => $request->input('cover'),
                    'price' => $request->input('price'),
                    'favorable_price' => $request->input('favorable_price'),
                    'method' => $request->input('method'),
                    'description' => $request->input('description'),
                    'introduction' => $request->input('introduction'),
                    'is_public' => $request->input('is_public'),
                    'buy_num' => $request->input('buy_num'),
                    'ttl' => $request->input('ttl'),
                ]);
            if(!empty($teacherIds)){
                $lesson->teachers()->attach($teacherIds); 
            }
            if(!empty($subjectIds)){
                $lesson->subjects()->attach($subjectIds); 
            }
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }


    /**
     * 修改课程
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
            'buy_num' => 'required',
            'ttl' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $lesson = Lesson::findOrFail($id);;
        $lesson->title = $request->input('title') ?: $lesson->title;
        $lesson->keyword = $request->input('keyword') ?: $lesson->keyword;
        $lesson->cover = $request->input('cover') ?: $lesson->cover;
        $lesson->price = $request->input('price') ?: $lesson->price;
        $lesson->method = $request->input('method') ?: $lesson->method;
        $lesson->description = $request->input('description') ?: $lesson->description;
        $lesson->is_public = $request->input('is_public') ?: $lesson->is_public;
        $lesson->buy_num = $request->input('buy_num') ?: $lesson->buy_num;
        $lesson->ttl = $request->input('ttl') ?: $lesson->ttl;
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
            return $this->response($validator->errors()->first(), 202);
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
     * 删除课程
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
