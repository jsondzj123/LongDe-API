<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lessons;
use Illuminate\Http\Request;
use CurrentAdmin;
use DB;

class LessonController extends Controller {

    /*
     * @param  课程列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $total = Lesson::where(['status' => 1, 'is_del' => 0])->count();
        $lesson = Lesson::with(['user' => function ($query) {
            $query->select('id', 'name', 'head_pic');
        }])
            ->where(['status' => 1, 'is_del' => 0])
            ->orderBy('top_id', 'desc')
            ->orderBy('id', 'desc')
            ->skip($currentCount)->take($count)
            ->get();

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
        $lesson = Lessons::with(['user' => function ($query) {
            $query->select('id', 'name', 'head_pic');
        }])
            ->where('is_del', 0)
            ->findOrFail($id);
        Lessons::where('id', $id)->update(['watch_num' => DB::raw('watch_num + 1')]);
        return $this->response($lessons);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $lesson = new Lessons();
        $lesson->name = $request->input('name') ?: $user->name;
        $lesson->cover = $request->input('cover') ?: $user->cover;
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
        $lesson = Lessons::findOrFail($id);
        if (!$lesson->destroy($id)) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
