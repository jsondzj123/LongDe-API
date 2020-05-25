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
        $subject_id = $request->input('subject_id') ?: 0;
        $method = $request->input('method') ?: 0;
        $sort = $request->input('sort') ?: 'created_at';
        $sort_type = $request->input('sort_type') ?: 'asc';
        $data =  Lesson::with('subjects')->select('id', 'admin_id', 'title', 'cover', 'price', 'favorable_price', 'buy_num', 'method', 'status', 'is_del', 'is_forbid')
                ->where(['is_del'=> 0, 'is_forbid' => 0, 'status' => 2])
                ->orderBy($sort, $sort_type)
                ->whereHas('subjects', function ($query) use ($subject_id)
                      {
                          if($subject_id != 0){
                              $query->where('id', $subject_id);
                          }
                      })
                ->where(function($query) use ($method){
                    if($method == 0){
                        $query->whereIn("method", [1, 2, 3]);
                    }else{
                        $query->where("method", $method);
                    }
                });
        $total = $data->count();
        
        $lessons = $data->skip($offset)->take($pagesize)->get();
        $data = [
            'page_data' => $lessons,
            'total' => $total,
        ];
        return $this->response($data);
    }
}
