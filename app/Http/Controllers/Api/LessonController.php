<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;


class LessonController extends Controller {

    /**
     * @param  课程列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/24 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $subject_id = $request->input('subject_id') ?: 0;
        $method = $request->input('method') ?: 0; 
        $lessons =  Lesson::with('subjects')->select('id', 'admin_id', 'title', 'cover', 'price', 'favorable_price', 'buy_num', 'method', 'status', 'is_del', 'is_forbid')
                ->where(['is_del' => 0, 'is_forbid' => 0, 'status' => 2])

                 ->whereHas('subjects', function ($query) use ($subject_id)
                     {
                             $query->where('id', $subject_id);
                     })
                ->where(function($query) use ($method, $status){
                    if($method == 0){
                        $query->whereIn("method", [1, 2, 3]);
                    }else{
                        $query->where("method", $method);
                    }
                });
        $total = $lessons->count();
        $lesson = $lessons->skip($currentCount)->take($count);
        $data = [
            'page_data' => $lessons,
            'total' => $total,
        ];
        return $this->response($data);
    }
}
