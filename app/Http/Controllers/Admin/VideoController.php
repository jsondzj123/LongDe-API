<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Subject;
use Illuminate\Http\Request;
use  App\Tools\CurrentAdmin;
use Validator;

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
        $video = Video::with('subject')
            ->where('is_del', 0)
            ->orderBy('id', 'desc')
            ->skip($offset)->take($pagesize)
            ->get();
        foreach ($video as $value) {
            $value->subject->parent = Subject::find($value->subject->pid);
        }
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
    public function show($id) {
        $video = Video::with('subject')->findOrFail($id);
        return $this->response($video);
    }


    /**
     * 添加资源.
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
        $user = CurrentAdmin::user();
        try {
            Video::create([
                'admin_id' => intval($user->id),
                'name' => $request->input('name'),
                'subject_id' => $request->input('subject_id'),
                'category' => $request->input('category'),
                'url' => $request->input('url'),
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
    public function update(Request $request) {
        $video = new Video();
        $video->name = $request->input('name') ?: $video->name;
        $video->subject_id = $request->input('subject_id') ?: $video->subject_id;
        $video->category = $request->input('category') ?: $video->category;
        $video->url = $request->input('url') ?: $video->url;
        try {
            $video->save();
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
        $video = Video::findOrFail($id);
        if($video->status == 1){
            $video->status = 0;
        }else{
            $video->status = 1;
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
    public function destroy($id) {
        $video = Video::findOrFail($id);
        $video->id_del = 1;
        if (!$video->save()) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
