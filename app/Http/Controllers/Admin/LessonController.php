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
use App\Tools\MTCloud;
use App\Models\LiveChild;
use App\Models\LiveTeacher;
use App\Models\Live;


class LessonController extends Controller {

    /**
     * @param  课程列表
     * @param  pagesize   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        $subject_id = $request->input('subject_id') ?: 0;
        $method = $request->input('method') ?: 0;
        $status = $request->input('status') ?: 0;
        $auth = (int)$request->input('auth') ?: 0;
        $public = (int)$request->input('public') ?: 0;
        $user = CurrentAdmin::user();   
        $data =  Lesson::with('subjects', 'methods')->select('id', 'admin_id', 'title', 'cover', 'price', 'favorable_price', 'buy_num', 'status', 'is_del', 'is_forbid')
                ->where(['is_del' => 0, 'is_forbid' => 0])

                ->whereHas('subjects', function ($query) use ($subject_id)
                    {
                       if($subject_id != 0){
                            $query->where('id', $subject_id);
                        }
                    })
                ->whereHas('methods', function ($query) use ($method)
                    {
                        if($method != 0){
                            $query->where('id', $method);
                        }
                    })
                ->where(function($query) use ($status){
                    if($status == 0){
                        $query->whereIn("status", [1, 2, 3]);
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
        $lesson = collect($lessons)->skip($offset)->take($pagesize);
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
     * return  array->with(['subjects' => function ($query) {
                $query->select('id', 'name');
            }])
     */
    public function show($id) {
        $lesson = Lesson::with(['teachers' => function ($query) {
                $query->select('id', 'real_name');
            }])
        ->with(['subjects' => function ($query) {
                $query->select('id', 'name');
            }])
        ->with(['methods' => function ($query) {
                $query->select('id', 'name');
            }])
        ->find($id);
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
            'subject_id' => 'required',
            'title' => 'required',
            'price' => 'required',
            'favorable_price' => 'required',
            'teacher_id' => 'required|json',
            'method_id' => 'required|json',
            'cover' => 'required',
            'description' => 'required',
            'introduction' => 'required',
            'is_public' => 'required',
            'nickname' => 'required_if:is_public,1',
            'start_at' => 'required_if:is_public,1',
            'end_at' => 'required_if:is_public,1',
            'barrage' => 'required_if:is_public,1',
            'modetype' => 'required_if:is_public,1',
        ]);

        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $methodIds = json_decode($request->input('method_id'), true);
        $subjectIds = json_decode($request->input('subject_id'), true);
        $teacherIds = json_decode($request->input('teacher_id'), true);
        $user = CurrentAdmin::user();
        //DB::beginTransaction(); //开启事务
        try {
            $lesson = Lesson::create([
                    'admin_id' => intval($user->id),
                    'title' => $request->input('title'),
                    'keyword' => $request->input('keyword') ?: NULL,
                    'price' => $request->input('price'),
                    'favorable_price' => $request->input('favorable_price'),
                    'cover' => $request->input('cover'),
                    'description' => $request->input('description'),
                    'introduction' => $request->input('introduction'),
                    'is_public' => $request->input('is_public'),
                    'buy_num' => $request->input('buy_num') ?: 0,
                    'ttl' => $request->input('ttl') ?: 0,
                    'status' => $request->input('status') ?: 1,
                ]);
            if(!empty($teacherIds)){
                $lesson->teachers()->attach($teacherIds); 
            }
            if(!empty($subjectIds)){
                $lesson->subjects()->attach($subjectIds); 
            }
            if(!empty($methodIds)){
                $lesson->methods()->attach($methodIds); 
            }
            if($request->input('is_public') == 1){ 
                $this->addLive($request->all(), $lesson->id);  
            }
            //DB::commit();  //提交
        } catch (Exception $e) {
            //DB::rollback();  //回滚
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
            'is_public' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $methodIds = json_decode($request->input('method_id'), true);
        $subjectIds = json_decode($request->input('subject_id'), true);
        $teacherIds = json_decode($request->input('teacher_id'), true);
        try {
            $lesson = Lesson::findOrFail($id);
            $lesson->title   = $request->input('title') ?: $lesson->title;
            $lesson->keyword = $request->input('keyword') ?: $lesson->keyword;
            $lesson->cover   = $request->input('cover') ?: $lesson->cover;
            $lesson->price   = $request->input('price') ?: $lesson->price;
            $lesson->favorable_price = $request->input('favorable_price') ?: $lesson->favorable_price;
            $lesson->description = $request->input('description') ?: $lesson->description;
            $lesson->buy_num = $request->input('buy_num') ?: $lesson->buy_num;
            $lesson->ttl     = $request->input('ttl') ?: $lesson->ttl;
            $lesson->start_at = $request->input('start_at') ?: $lesson->start_at;
            $lesson->end_at = $request->input('end_at') ?: $lesson->end_at;
            $lesson->status = $request->input('status') ?: $lesson->status;
            $lesson->save();
            if(!empty($subjectIds)){
                $lesson->subjects()->detach(); 
                $lesson->subjects()->attach($subjectIds);
            }
            if(!empty($teacherIds)){
                $lesson->teachers()->detach(); 
                $lesson->teachers()->attach($teacherIds); 
            }
            if(!empty($methodIds)){
                $lesson->methods()->detach(); 
                $lesson->methods()->attach($methodIds);  
            }
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
     * 修改课程状态
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request) {
        $validator = Validator::make($request->all(), [
            'id'     => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        try {
            $lesson = Lesson::findOrFail($request->input('id'));
            $lesson->status = $request->input('status');
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


    //公开课创建直播
    public function addLive($data, $lesson_id)
    {
        $user = CurrentAdmin::user();
        try {
            $MTCloud = new MTCloud();
            $res = $MTCloud->courseAdd(
                $data['title'],
                $data['teacher_id'],
                $data['start_at'],
                $data['end_at'],
                $data['nickname'],
                '',
                [   
                    'barrage' => $data['barrage'], 
                    'modetype' => $data['modetype'],
                ]
            );
            if(!array_key_exists('code', $res) && !$res["code"] == 0){
                Log::error('欢拓创建失败:'.json_encode($res));
                return false;
            }
            $live = Live::create([
                    'admin_id' => intval($user->id),
                    'subject_id' => $data['subject_id'],
                    'name' => $data['title'],
                    'description' => $data['description'],
                ]);
            
            $live->lessons()->attach([$lesson_id]);
            $livechild =  LiveChild::create([
                            'admin_id'   => $user->id,
                            'live_id'    => $live->id,
                            'course_name' => $data['title'],
                            'account'     => $data['teacher_id'],
                            'start_time'  => $data['start_at'],
                            'end_time'    => $data['end_at'],
                            'nickname'    => $data['nickname'],
                            'partner_id'  => $res['data']['partner_id'],
                            'bid'         => $res['data']['bid'],
                            'course_id'   => $res['data']['course_id'],
                            'zhubo_key'   => $res['data']['zhubo_key'],
                            'admin_key'   => $res['data']['admin_key'],
                            'user_key'    => $res['data']['user_key'],
                            'add_time'    => $res['data']['add_time'],
                        ]);
            LiveTeacher::create([
                'admin_id' => $user->id,
                'live_id' => $live->id,
                'live_child_id' => $livechild->id,
                'teacher_id' => $data['teacher_id'],
            ]);
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return false;
        }
        return true;
    }
}
