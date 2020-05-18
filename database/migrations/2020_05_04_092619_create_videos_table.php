<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('subject_id')->unsigned()->comment('科目ID');
            $table->string('name')->comment('视频名称');
            $table->integer('category')->nullable()->default(0)->comment('类型:1视频2音频3课件4文档');
            $table->string('url')->comment('资源地址');
            $table->integer('size')->nullable()->default(0)->comment('大小');
            $table->integer('status')->nullable()->default(0)->comment('状态0禁用1启用');
            $table->integer('is_del')->nullable()->default(0)->comment('是否删除：0否1是');
            $table->integer('is_forbid')->nullable()->default(0)->comment('是否禁用：0否1是');
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
