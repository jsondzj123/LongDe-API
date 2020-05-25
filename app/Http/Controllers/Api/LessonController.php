<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller {

    /*
     * @param  科目列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
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
        $total = Lesson::where(['is_del'=> 0, 'is_forbid' => 0, 'status' => 2])->count();
        $lessons = Lesson::select('id', 'title', 'cover', 'method', 'price', 'favorable_price', 'is_del', 'is_forbid', 'status')
            ->where(['is_del'=> 0, 'is_forbid' => 0, 'status' => 2])
            ->orderBy('created_at', 'desc')
            ->skip($currentCount)->take($count)
            ->get();
        $data = [
            'page_data' => $lessons,
            'total' => $total,
        ];
        return response()->json(['code' => 200, 'msg' => 'success', 'data' => $data]);
        //return $this->response($data);
    }
}
