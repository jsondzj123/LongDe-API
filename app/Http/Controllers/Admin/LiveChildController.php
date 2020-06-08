<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Models\Subject;
use Illuminate\Http\Request;
use  App\Tools\CurrentAdmin;
use Validator;
use App\Tools\MTCloud;
use App\Models\LiveChild;
use App\Models\LiveTeacher;
use App\Models\LiveClassChild;
use Log;

class LiveChildController extends Controller {

     /**
     * @param  课次列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/18 
     * @return  array
     */
    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'live_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        $live_id = $request->input('live_id');
        $total = LiveChild::where([
                'is_del' => 0,
                'is_forbid' => 0,
                'live_id' => $live_id,
            ])->count();
        $lesson = LiveChild::select('id', 'course_name', 'start_time', 'end_time', 'modetype')
            ->where([
                'is_del' => 0, 
                'is_forbid' => 0, 
                'live_id' => $live_id
            ])
            ->skip($offset)->take($pagesize)
            ->get();
    
        $data = [
            'page_data' => $lesson,
            'total' => $total,
        ];
        return $this->response($data);
    }

    /**
     * 添加课次.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_child_id' => 'required',
            'live_id' => 'required',
            'lesson_name' => 'required',
            'teacher_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'nickname' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $user = CurrentAdmin::user();
        try{
            $MTCloud = new MTCloud();
            $res = $MTCloud->courseAdd(
                        $request->input('lesson_name'),
                        $request->input('teacher_id'),
                        $request->input('start_time'),
                        $request->input('end_time'),
                        $request->input('nickname'),
                        '',
                        [   
                            'barrage' => $request->input('barrage') ?: 0, 
                            'modetype' => $request->input('modetype') ?: 3,
                        ]
                    );
            Log::error('欢拓创建直播间:'.json_encode($res));
            if(!array_key_exists('code', $res) && !$res["code"] == 0){
                return $this->response('直播间创建失败', 500);
            }
            $livechild =  LiveChild::create([
                            'admin_id'    => $user->id,
                            'live_id'     => $request->input('live_id'),
                            'course_name' => $request->input('lesson_name'),
                            'account'    => $request->input('teacher_id'),
                            'start_time' => $request->input('start_time'),
                            'end_time'   => $request->input('end_time'),
                            'nickname'   => $request->input('nickname'),
                            'partner_id' => $res['data']['partner_id'],
                            'bid'        => $res['data']['bid'],
                            'course_id'  => $res['data']['course_id'],
                            'zhubo_key'  => $res['data']['zhubo_key'],
                            'admin_key'  => $res['data']['admin_key'],
                            'user_key'   => $res['data']['user_key'],
                            'add_time'   => $res['data']['add_time'],
                            'status'     => 1,
                        ]);

            LiveClassChild::create([
                'live_child_id' => $livechild->id,
                'lesson_child_id' => $request->input('lesson_child_id'),
                ]);
            LiveTeacher::create([
                'admin_id' => $user->id,
                'live_id' => $request->input('live_id'),
                'live_child_id' => $livechild->id,
                'teacher_id' => $request->input('teacher_id'),
                ]);
        }catch(Exception $e){
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response($livechild);
    }




    /**
     * 启用/禁用课次
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $live = LiveChild::findOrFail($request->input('id'));
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
     * 删除课次
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
        $live = LiveChild::findOrFail($id);
        if($live->is_del == 1){
            $live->is_del = 0;
        }else{
            $live->is_del = 1;
        }
        if (!$live->save()) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }



    /**
     * 启动直播
     * @param 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function startLive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $live = LiveChild::findOrFail($request->input('id'));
        $MTCloud = new MTCloud();
        $res = $MTCloud->courseLaunch($live->course_id);
        Log::error('直播器启动:'.json_encode($res));
        if(!array_key_exists('code', $res) && !$res["code"] == 0){
            return $this->response('直播器启动失败', 500);
        }
        return $this->response($res);
    }

}
