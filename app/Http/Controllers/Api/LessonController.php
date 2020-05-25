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
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        if($request->input('sort') == 3){
            $sort = 'price';
        }
        $sort_type = $request->input('sort_type') ?: 'asc';
        $total = Lesson::where(['is_del'=> 0, 'is_forbid' => 0, 'status' => 2])->count();
        $lessons = Lesson::select('id', 'title', 'cover', 'method', 'price', 'favorable_price', 'is_del', 'is_forbid', 'status')
            ->where(['is_del'=> 0, 'is_forbid' => 0, 'status' => 2])
            ->orderBy($sort, $sort_type)
            ->skip($offset)->take($pagesize)
            ->get();
        $data = [
            'page_data' => $lessons,
            'total' => $total,
        ];
        return $this->response($data);
    }
}
