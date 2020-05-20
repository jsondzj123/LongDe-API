<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Tools\CurrentAdmin;
use DB;
use Validator;

class SubjectController extends Controller {

    /*
     * @param  科目列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $total = Subject::where('pid', 0)->count();
        $subject = Subject::where('pid', 0)->orderBy('status', 'desc')
            ->skip($currentCount)->take($count)
            ->get();
        foreach ($subject as $value) {
            $value['childs'] = $value->childs();
        }
        return $this->response($subject);
    }


    /*
     * @param  科目详情
     * @param  科目id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function show($id) {
        $subject = Subject::find($id);
        $subject['childs'] = $subject->childs(); 
        if(empty($subject)){
            return $this->response('科目不存在', 404);
        }
        return $this->response($subject);
    }


    /**
     * 添加科目.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'cover' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $user = CurrentAdmin::user();

        try {
            $subject = Subject::create([
                    'admin_id' => intval($user->id),
                    'pid' => $request->input('pid') ?: 0,
                    'name' => $request->input('name'),
                    'cover' => $request->input('cover'),
                    'description' => $request->input('description'),
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
            'name' => 'required',
            'cover' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $subject = Subject::findOrFail($id);;
        $subject->name = $request->input('name') ?: $subject->name;
        $subject->cover = $request->input('cover') ?: $subject->cover;
        $subject->description = $request->input('description') ?: $subject->description;
        try {
            $subject->save();
            return $this->response("修改成功");
        } catch (Exception $e) {
            Log::error('修改科目信息失败' . $e->getMessage());
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
        $subject = Subject::findOrFail($id);
        $subject->is_del = 1;
        if (!$subject->save()) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
