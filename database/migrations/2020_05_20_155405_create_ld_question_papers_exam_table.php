<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdQuestionPapersExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_question_papers_exam', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('subject_id')->default(0)->comment('科目ID');
            $table->integer('papers_id')->default(0)->comment('试卷ID');
            $table->integer('exam_id')->default(0)->comment('试题ID');
            $table->smallInteger('type')->default(0)->comment('试题类型');
            $table->smallInteger('is_del')->default(0)->comment('删除');
            $table->integer('chapter_id')->comment('章ID');
            $table->integer('joint_id')->comment('节ID');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->comment('更新时间');

            $table->index('admin_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_question_papers_exam');
    }
}
