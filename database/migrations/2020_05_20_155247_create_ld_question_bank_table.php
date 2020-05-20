<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdQuestionBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_question_bank', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->string('topic_name')->comment('题库名称');
            $table->string('subject_id')->default(0)->comment('题库科目ID');
            $table->integer('parent_id')->default(0)->comment('学科一级ID');
            $table->integer('child_id')->default(0)->comment('学科二级ID');
            $table->text('describe')->comment('描述');
            $table->smallInteger('is_del')->default(0)->comment('删除0否1是');
            $table->smallInteger('is_open')->default(1)->comment('开启状态');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->nullable()->comment('更新时间');

            $table->index('admin_id');
            $table->index('parent_id');
            $table->index('child_id');
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
        Schema::dropIfExists('ld_question_bank');
    }
}
