<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;



class AdminController extends Controller {
            

    /*
     * @param 根据用户ID获取用户信息
     * @param $user_id
     */
    public function show($id){
        $admin = Admin::findOrFail($id);
        return $this->response($admin);
    }
    public function info(Request $request){
    	dd($request->all());
        $admin = Admin::findOrFail($id);
        return $this->response($admin);
    }
}
