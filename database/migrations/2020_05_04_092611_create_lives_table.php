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
            $table->string('name')->comment('直播名称');
            $table->string('cover')->comment('直播封面');
            $table->string('describe')->comment('直播描述');
            $table->string('url')->comment('资源地址');
            $table->integer('watch_num')->default(0)->comment('观看人数');
            $table->integer('like_num')->default(0)->comment('点赞人数');
            $table->integer('online_num')->default(0)->comment('在线人数');
            $table->string('pull_url')->comment('观看地址');
            $table->string('push_url')->comment('推流地址');
            $table->string('replay_url')->comment('回放地址');
            $table->integer('status')->default(0)->comment('直播状态');
            $table->timestamp('end_at')->comment('结束时间');
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
