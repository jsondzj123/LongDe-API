<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Subject;
use Illuminate\Http\Request;
use  App\Tools\CurrentAdmin;
use Validator;
use App\Tools\MTCloud;

class VideoController extends Controller {

    /*
     * @param  录播列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        $total = Video::where('is_del', 0)->count();
        $video = Video::where('is_del', 0)
            ->orderBy('created_at', 'desc')
            ->skip($offset)->take($pagesize)
            ->get();
        $data = [
            'page_data' => $video,
            'total' => $total,
        ];
        return $this->response($data);
    }


    /*
     * @param  录播详情
     * @param  录播id
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
        $video = Video::with('subjects')->findOrFail($request->input('id'));
        return $this->response($video);
    }


    /**
     * 添加录播资源.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'subject_id' => 'required',
            'category' => 'required',
            'url' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $subjectIds = json_decode($request->input('subject_id'), true);
        $user = CurrentAdmin::user();
        try {
            $video = Video::create([
                        'admin_id' => intval($user->id),
                        'name' => $request->input('name'),
                        'category' => $request->input('category'),
                        'url' => $request->input('url'),
                        'size' => $request->input('size') ?: 0,
                    ]);
            if(!empty($subjectIds)){
                $video->subjects()->attach($subjectIds);
            }
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }

    /**
     * @param 修改录播资源
     *
     * @param  Request  $request
     * @param  int  $id
     * @return json
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
            $video = Video::findOrFail($request->input('id'));
            $video->name = $request->input('name') ?: $video->name;
            $video->category = $request->input('category') ?: $video->category;
            $video->url = $request->input('url') ?: $video->url;
            $video->size = $request->input('size') ?: $video->size;
            $video->save();
            $video->subjects()->sync($subjectIds);
        } catch (Exception $e) {
            Log::error('修改失败' . $e->getMessage());
            return $this->response("修改成功");
        }
        return $this->response("修改成功");
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
        $video = Video::findOrFail($request->input('id'));
        if($video->is_forbid == 1){
            $video->is_forbid = 0;
        }else{
            $video->is_forbid = 1;
        }
        if (!$video->save()) {
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
        $video = Video::findOrFail($request->input('id'));
        if($video->is_del == 1){
           $video->is_del = 0;
        }else{
           $video->is_del = 1;
        }
        if (!$video->save()) {
            return $this->response("操作失败", 500);
        }
        return $this->response("操作成功");
    }



    //获取欢拓录播资源上传地址
    public function uploadUrl(Request $request)
    {
        $MTCloud = new MTCloud();
        $res = $MTCloud->videoGetUploadUrl(1, 2, '测试上传录播', 'videoUpload');
        if(!array_key_exists('code', $res) && !$res['code'] == 0){
            Log::error('进入直播间失败:'.json_encode($res));
            return $this->response('进入直播间失败', 500);
        }
        return $this->response($res['data']);
    }
}
