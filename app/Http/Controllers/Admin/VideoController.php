<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use CurrentUser;
use DB;

class VideoController extends Controller {

    /*
     * @param  录播列表
     * @param  current_count   count
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function index(Request $request){
        $currentCount = $request->input('current_count') ?: 0;
        $count = $request->input('count') ?: 15;
        $total = Video::where(['status' => 1, 'is_del' => 0])->count();
        $video = Video::with(['user' => function ($query) {
            $query->select('id', 'name', 'head_pic');
        }])
            ->where(['status' => 1, 'is_del' => 0])
            ->orderBy('top_id', 'desc')
            ->orderBy('id', 'desc')
            ->skip($currentCount)->take($count)
            ->get();

        $data = [
            'page_data' => $video,
            'total' => $total,
        ];
        return $this->response($data);
    }


    /*
     * @param  录播详情
     * @param  录播id
     * @param  author  孙晓丽
     * @param  ctime   2020/5/1 
     * return  array
     */
    public function show($id) {
        $video = Video::with(['user' => function ($query) {
            $query->select('id', 'name', 'head_pic');
        }])
            ->where('is_del', 0)
            ->findOrFail($id);
        Video::where('id', $id)->update(['watch_num' => DB::raw('watch_num + 1')]);
        return $this->response($video);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request) {
        $video = new Video();
        $video->name = $request->input('name') ?: $user->name;
        $video->cover = $request->input('cover') ?: $user->cover;
        try {
            $video->save();
            return $this->response("修改成功");
        } catch (Exception $e) {
            Log::error('修改课程信息失败' . $e->getMessage());
            return $this->response("修改成功");
        }
    }


    /**
     * 删除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $video = Video::findOrFail($id);
        if (!$video->destroy($id)) {
            return $this->response("删除失败", 500);
        }
        return $this->response("删除成功");
    }
}
