<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonChildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_childs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('lesson_id')->unsigned()->comment('课程ID');
            $table->integer('pid')->default(0)->comment('章/班号ID');
            $table->string('name')->comment('章/节名称');
            $table->text('description')->comment('描述');
            $table->integer('category')->default(0)->comment('类型:1视频2音频3课件4文档');
            $table->string('url')->comment('资源地址');
            $table->integer('size')->default(0)->comment('大小');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');
            $table->integer('status')->default(0)->comment('状态0禁用1启用');
            $table->integer('is_free')->default(0)->comment('是否免费：0免费1收费');
            $table->integer('is_del')->default(0)->comment('是否删除：0否1是');
            $table->integer('is_forbid')->default(0)->comment('是否禁用：0否1是');
            $table->timestamps();


            $table->foreign('lesson_id')->references('id')->on('lessons')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('lesson_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_childs');
    }
}
