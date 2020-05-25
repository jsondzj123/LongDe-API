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
            ['id' => 1, 'name' => '综合', 'type' => []],
            ['id' => 2, 'name' => '按热度', 'type' => ['asc', 'desc']],
            ['id' => 3, 'name' => '按价格', 'type' => ['asc', 'desc']],
        ];
        $data['method'] = [
            ['id' => 1, 'name' => '综合'],
            ['id' => 2, 'name' => '直播'],
            ['id' => 3, 'name' => '录播'],
            ['id' => 4, 'name' => '直播+录播'],
            ['id' => 5, 'name' => '其他'],
        ]; 
        return $this->response($data);
    }
}
