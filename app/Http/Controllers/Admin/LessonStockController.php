<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonStock;
use Illuminate\Http\Request;
use App\Tools\CurrentAdmin;
use DB;
use Validator;
use App\Models\Teacher;

class LessonStockController extends Controller {


    /**
     * 授权课程.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required',
            'school_pid' => 'required',
            'school_id' => 'required',
            'add_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $user = CurrentAdmin::user();

        try {
            $lesson = LessonStock::create([
                    'admin_id' => intval($user->id),
                    'lesson_id' => $request->input('lesson_id'),
                    'school_pid' => $request->input('school_pid'),
                    'school_id' => $request->input('school_id'),
                    'current_number' => 0,
                    'add_number' => $request->input('add_number'),
                ]);

        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }


    /**
     * 添加库存
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required',
            'school_pid' => 'required',
            'school_id' => 'required',
            'add_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 422);
        }
        $lesson = LessonStock::findOrFail($id);
        $lesson->admin_id => intval($user->id),
        $lesson->lesson_id => $request->input('lesson_id') ?: $lesson->lesson_id,
        $lesson->school_pid => $request->input('school_pid') ?: $lesson->school_pid,
        $lesson->school_id => $request->input('school_id') ?: $lesson->school_id,
        $lesson->current_number => 10,
        $lesson->add_number => $request->input('add_number') ?: $lesson->add_number,
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
        $lesson = LessonStock::findOrFail($id);
        $lesson->is_forbid = 1;
        if (!$lesson->save()) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
