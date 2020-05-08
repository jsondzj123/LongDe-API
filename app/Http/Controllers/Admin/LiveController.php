<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Live;
use App\Models\Subject;
use Illuminate\Http\Request;
use  App\Tools\CurrentAdmin;
use Validator;

class LiveController extends Controller {

    /*
     * @param  录播列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $total = Live::where('is_del', 0)->count();
        $live = Live::with('subject')
            ->where('is_del', 0)
            ->orderBy('id', 'desc')
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
     * @param  录播详情
     * @param  录播id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function show($id) {
        $live = Live::with('subject')->findOrFail($id);
        return $this->response($live);
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
            'cover' => 'required',
            'describe' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $user = CurrentAdmin::user();
        try {
            Live::create([
                'admin_id' => intval($user->id),
                'name' => $request->input('name'),
                'cover' => $request->input('cover'),
                'describe' => $request->input('describe'),
                'url' => $request->input('url') ?: '',
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
        $live = new Live();
        $live->name = $request->input('name') ?: $live->name;
        $live->subject_id = $request->input('subject_id') ?: $live->subject_id;
        $live->category = $request->input('category') ?: $live->category;
        $live->url = $request->input('url') ?: $live->url;
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
        if($live->status == 1){
            $live->status = 0;
        }else{
            $live->status = 1;
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