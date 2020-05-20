<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdLecturerEducationaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_lecturer_educationa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('school_id')->default(0)->comment('网校ID');
            $table->string('head_icon')->comment('头像');
            $table->char('phone', 11)->comment('手机号');
            $table->string('real_name')->comment('讲师姓名');
            $table->smallInteger('sex')->default(1)->comment('1男2女');
            $table->string('qq')->comment('QQ号');
            $table->string('wechat')->comment('微信号');
            $table->integer('parent_id')->default(0)->comment('学科一级分类');
            $table->integer('child_id')->default(0)->comment('学科二级分类');
            $table->text('describe')->nullable()->comment('描述');
            $table->text('content')->nullable()->comment('详情');
            $table->integer('number')->default(0)->comment('开课数量');
            $table->smallInteger('is_del')->default(0)->comment('是否删除：0否1是');
            $table->smallInteger('is_forbid')->default(0)->comment('是否禁用：0否1是');
            $table->smallInteger('is_recommend')->default(0)->comment('是否推荐1是2否');
            $table->smallInteger('type')->default(1)->comment('老师类型1教务2讲师');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->nullable()->comment('更新时间');

            $table->index('school_id');
            $table->index('parent_id');
            $table->index('type');
            $table->index('is_forbid');
            $table->index('real_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_lecturer_educationa');
    }
}
