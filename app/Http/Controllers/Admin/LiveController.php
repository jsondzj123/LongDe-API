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

class LiveController extends Controller {

    /*
     * @param  直播列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/18 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $total = Live::where(['is_del'=> 0, 'is_forbid' => 0])->count();
        $live = Live::where(['is_del'=> 0, 'is_forbid' => 0])
            ->orderBy('created_at', 'desc')
            ->skip($currentCount)->take($count)
            ->get();
        foreach ($live as $value) {
            $value->subject->parent = Subject::find($value->subject->pid);
        }
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
    public function show($id) {
        $live = Live::with('subject')->findOrFail($id);
        return $this->response($live);
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
        $user = CurrentAdmin::user();
        try {
            Live::create([
                'admin_id' => intval($user->id),
                'subject_id' => $request->input('subject_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
            ]);

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
    public function lesson($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|json',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $user = CurrentAdmin::user();
        $lessonIds = json_decode($request->input('lesson_id'), true);
        $live = Live::find($id);
        try {
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
        $user = CurrentAdmin::user();
        $live = new Live();
        $live->admin_id = $user->id;
        $live->subject_id = $request->input('subject_id') ?: $live->subject_id;
        $live->name = $request->input('name') ?: $live->name;
        $live->description = $request->input('description') ?: $live->description;
        try {
            $live->save();
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
    public function edit($id) {
        $live = Live::findOrFail($id);
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
    public function destroy($id) {
        $live = Live::findOrFail($id);
        $live->id_del = 1;
        if (!$live->save()) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
