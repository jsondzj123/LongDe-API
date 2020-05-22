<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;


class SubjectController extends Controller {

    /*
     * @param  科目列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $subject = Subject::where('pid', 0)
                ->select('id', 'name', 'pid')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($subject as $value) {
                $value['childs'] = $value->childs();
            }
        return $this->response($subject);
    }
}
