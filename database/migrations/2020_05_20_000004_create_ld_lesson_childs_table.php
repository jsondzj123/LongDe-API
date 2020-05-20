<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdLessonChildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_lesson_childs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('lesson_id')->unsigned()->comment('课程ID');
            $table->string('name')->comment('章/节名称');
            $table->integer('pid')->nullable()->default(0)->comment('章/班号ID');
            $table->text('description')->nullable()->comment('描述');
            $table->integer('category')->nullable()->default(0)->comment('类型:1视频2音频3课件4文档');
            $table->string('url')->nullable()->comment('资源地址');
            $table->integer('size')->nullable()->default(0)->comment('大小');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');
            $table->integer('is_free')->nullable()->default(0)->comment('是否免费：0免费1收费');
            $table->integer('is_del')->default(0)->comment('是否删除：0否1是');
            $table->integer('is_forbid')->default(0)->comment('是否禁用：0否1是');
            $table->timestamps();


            $table->foreign('lesson_id')->references('id')->on('ld_lessons')
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
        Schema::dropIfExists('ld_lesson_childs');
    }
}