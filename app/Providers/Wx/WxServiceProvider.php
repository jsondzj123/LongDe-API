<?php

namespace App\Providers\Wx;

use Illuminate\Support\ServiceProvider;

class WxServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('wx',function(){
            return new WxpayFactory();
        });
    }
}
