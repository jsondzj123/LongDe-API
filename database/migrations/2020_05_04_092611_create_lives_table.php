<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->string('course_name')->comment('课程名称');
            $table->integer('account')->comment('接入方主播账号或ID或手机号');
            $table->timestamp('start_time')->comment('开始时间');
            $table->timestamp('end_time')->comment('结束时间');
            $table->string('nickname')->comment('主播的昵称');
            $table->string('accountIntro')->nullable()->comment('主播的简介');
            $table->string('options')->nullable()->comment('其它可选参数');
            $table->string('url')->nullable()->comment('资源地址');
            
            $table->integer('partner_id')->nullable()->comment('课程ID');
            $table->integer('bid')->nullable()->comment('课程ID');
            $table->integer('course_id')->nullable()->comment('课程ID');
            $table->string('zhubo_key')->nullable()->comment('观看地址');
            $table->string('admin_key')->nullable()->comment('推流地址');
            $table->string('user_key')->nullable()->comment('回放地址');
            $table->integer('add_time')->nullable()->comment('课程创建时间');

            $table->integer('watch_num')->default(0)->comment('观看人数');
            $table->integer('like_num')->default(0)->comment('点赞人数');
            $table->integer('online_num')->default(0)->comment('在线人数');
            
            $table->integer('isPublic')->default(0)->comment('是否公开课：0否1是');
            $table->integer('modetype')->default(0)->comment('模式：1语音云3大班5小班6大班互动');
            $table->integer('barrage')->default(0)->comment('是否开启弹幕：0关闭1开启');
            $table->string('robot')->nullable()->comment('虚拟用户数据');

            $table->integer('status')->default(0)->comment('直播状态');
            $table->integer('is_del')->default(0)->comment('是否删除：0否1是');
            $table->integer('is_forbid')->default(0)->comment('是否禁用：0否1是');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lives');
    }
}
