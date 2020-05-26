<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use DB;

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
        $child_id = $request->input('child_id') ?: 0;
        if($subject_id == 0 && $child_id == 0){
            $subjectId = 0;
        }elseif($subject_id != 0 && $child_id == 0){
            $subjectId = $subject_id;
        }elseif($subject_id != 0 && $child_id != 0){
            $subjectId = $child_id;
        }elseif($subject_id == 0 && $child_id != 0){
            $subjectId = $subject_id;
        }
        $method = $request->input('method') ?: 0;
        $sort = $request->input('sort') ?: 'created_at';
        $sort_type = $request->input('sort_type') ?: 'asc';
        $data =  Lesson::with('subjects')->select('id', 'admin_id', 'title', 'cover', 'price', 'favorable_price', 'buy_num', 'method', 'status', 'is_del', 'is_forbid')
                ->where(['is_del'=> 0, 'is_forbid' => 0, 'status' => 2])
                ->orderBy($sort, $sort_type)
                ->whereHas('subjects', function ($query) use ($subjectId)
                      {
                          if($subjectId != 0){
                              $query->where('id', $subjectId);
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


    /**
     * @param  课程详情
     * @param  课程id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function show($id) {
        $lesson = Lesson::with(['teachers' => function ($query) {
                $query->select('id', 'real_name');
            }])
        ->with(['subjects' => function ($query) {
                $query->select('id', 'name');
            }])
        ->find($id);
        if(empty($lesson)){
            return $this->response('课程不存在', 404);
        }
        Lesson::where('id', $id)->update(['watch_num' => DB::raw('watch_num + 1')]);
        return $this->response($lesson);
    }
}
