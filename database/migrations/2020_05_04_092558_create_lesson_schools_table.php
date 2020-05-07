<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_schools', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('lesson_id')->default(0)->comment('课程ID');
            $table->string('title')->comment('课程表题');
            $table->string('keyword')->comment('关键词');
            $table->string('cover')->comment('封面');
            $table->text('description')->comment('描述');
            $table->text('introduction')->comment('简介');
            $table->string('url')->comment('课程资料');
            $table->float('price', 12, 2)->comment('定价');
            $table->float('favorable_price', 12, 2)->comment('优惠价');
            $table->integer('is_public')->default(0)->comment('是否公开:0否1是');
            $table->integer('status')->default(0)->comment('课程状态:0未上架1已上架');
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
        Schema::dropIfExists('lesson_schools');
    }
}