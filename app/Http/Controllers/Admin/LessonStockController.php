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
     * @param  库存列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/15 
     * return  array
     */
    public function index(Request $request){
        $pagesize = $request->input('pagesize') ?: 15;
        $page     = $request->input('page') ?: 1;
        $offset   = ($page - 1) * $pagesize;
        $lesson_id = $request->input('lesson_id');
        $school_id = $request->input('school_id');
        $data =  LessonStock::where(['lesson_id' => $lesson_id, 'school_id' => $school_id]);
        $total = $data->count();
        $stock = $data->orderBy('created_at', 'desc')->skip($offset)->take($pagesize)->get();
        $data = [
            'page_data' => $stock,
            'total' => $total,
        ];
        return $this->response($data);
    }

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
            'type' => 'required',
            'add_number' => 'required_if:type,2',

        ]);
        if ($validator->fails()) {
            return $this->response($validator->errors()->first(), 202);
        }
        $data = $request->all();
        if($request->input('type') == 2){
            $data['current_number'] = '10';
        }else{
            $data['current_number'] = '0';
            $data['add_number'] = '0';
        }
        try {
            $this->create($data);
        } catch (Exception $e) {
            Log::error('创建失败:'.$e->getMessage());
            return $this->response($e->getMessage(), 500);
        }
        return $this->response('创建成功');
    }


    public function create($data)
    {
        $user = CurrentAdmin::user();
        return LessonStock::create([
            'admin_id' => intval($user->id),
            'lesson_id' => $data['lesson_id'],
            'school_pid' =>$data['school_pid'],
            'school_id' => $data['school_id'],
            'current_number' => $data['current_number'],
            'add_number' => $data['add_number'],
        ]);
    }
}
