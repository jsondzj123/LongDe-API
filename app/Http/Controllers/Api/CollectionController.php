<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Collection;
use App\Models\Student;

class CollectionController extends Controller {

    //收藏列表
    public function index(Request $request){
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        $student = Student::find(self::$accept_data['user_info']['user_id'])->collectionLessons->skip($offset)->take($pagesize);
        $total = $student->count();
        $data = [
            'page_data' => $student,
            'total' => $total,
        ];
        return $this->response($data);
    }

     /**
     * @param 收藏课程.
     * @param
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        try {
            Collection::create([
                'student_id' => intval(self::$accept_data['user_info']['user_id']),
                'lesson_id' => $request->input('lesson_id'),
            ]);
        } catch (Exception $e) {
            Log::error('收藏失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('收藏成功');
    }
    
}
