<?php

namespace App\Http\Middleware;
use App\Providers\Rsa\RsaFactory;
use Closure;
use Illuminate\Support\Facades\App;

class ApidateToken {
    public function handle($request, Closure $next){
        //解密
        $data = $request->post();
        $rsa =  new RsaFactory();
        $a = $rsa->Servicersadecrypt($data);
        echo $a;
    }
}
