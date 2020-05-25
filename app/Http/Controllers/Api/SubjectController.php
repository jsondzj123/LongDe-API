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
        $subjects = Subject::where('pid', 0)
                ->select('id', 'name', 'pid')
                ->orderBy('created_at', 'desc')
                ->get();
        foreach ($subjects as $value) {
                $value['childs'] = $value->childs();
        }
        $data['subjects'] = $subjects; 
        $data['sort'] = [
            ['sort' => 'created_at', 'name' => '综合', 'type' => ['asc', 'desc']],
            ['sort' => 'watch_num', 'name' => '按热度', 'type' => ['asc', 'desc']],
            ['sort' => 'price', 'name' => '按价格', 'type' => ['asc', 'desc']],
        ];
        $data['method'] = [
            ['id' => 0, 'name' => '综合'],
            ['id' => 1, 'name' => '直播'],
            ['id' => 2, 'name' => '录播'],
            ['id' => 3, 'name' => '直播+录播'],
            ['id' => 4, 'name' => '其他'],
        ]; 
        return $this->response($data);
    }
}
