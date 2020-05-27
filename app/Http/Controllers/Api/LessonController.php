<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use DB;

class LessonController extends Controller {

    /**
     * @param  公开课列表
     * @param  pagesize   page
     * @param  author  孙晓丽
     * @param  ctime   2020/5/26 
     * @return  array
     */
    public function publicList(Request $request){
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        $lesson =  Lesson::select('id', 'admin_id', 'title', 'cover', 'price', 'favorable_price', 'buy_num', 'method', 'status', 'is_del', 'is_forbid', 'start_at', 'end_at')
                ->where(['is_public' => 1, 'is_del'=> 0, 'is_forbid' => 0, 'status' => 2])
                ->orderBy('start_at', 'desc');
        $total = $lesson->count();
        $lessons = $lesson->skip($offset)->take($pagesize)->get();
        $data = [
            'page_data' => $lessons,
            'total' => $total,
        ];
        return $this->response($data);
    }

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
        $keyWord = $request->input('keyword') ?: 0;
        $method = $request->input('method_id') ?: 0;
        $sort = $request->input('sort_id') ?: 0;
        if($sort == 0){
            $sort_name = 'created_at'; 
        }elseif($sort == 1){
            $sort_name = 'watch_num'; 
        }elseif($sort == 2){
            $sort_name = 'price'; 
        }elseif($sort == 3){
            $sort_name = 'price'; 
        }
        $where = ['is_del'=> 0, 'is_forbid' => 0, 'status' => 2];
        $sort_type = $request->input('sort_type') ?: 'asc';
        $data =  Lesson::with('subjects', 'methods')->select('id', 'admin_id', 'title', 'cover', 'price', 'favorable_price', 'buy_num', 'status', 'is_del', 'is_forbid')
                ->where(['is_del'=> 0, 'is_forbid' => 0, 'status' => 2])
                ->orderBy($sort_name, $sort_type)
                ->whereHas('subjects', function ($query) use ($subjectId)
                {
                    if($subjectId != 0){
                        $query->where('id', $subjectId);
                    }
                })
                ->whereHas('methods', function ($query) use ($method)
                {
                    if($method != 0){
                        $query->where('id', $method);
                    }
                })
                ->where(function($query) use ($keyWord){
                    if(!empty($keyWord)){
                        $query->where('title', 'like', '%'.$keyWord.'%');
                    }
                });
        $lessons = [];
        foreach ($data->get()->toArray() as $value) {
            if($value['is_auth'] == 1 || $value['is_auth'] == 2){
                $lessons[] = $value;   
            }
        }
        $total = collect($lessons)->count();
        $lessons = collect($lessons)->skip($offset)->take($pagesize);
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
