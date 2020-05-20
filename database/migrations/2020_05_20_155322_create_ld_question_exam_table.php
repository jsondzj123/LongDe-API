<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdQuestionExamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_question_exam', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('parent_id')->default(0)->comment('材料父级ID');
            $table->integer('subject_id')->default(0)->comment('科目ID');
            $table->integer('bank_id')->default(0)->comment('题库ID');
            $table->text('exam_content')->comment('试题内容');
            $table->text('answer')->nullable()->comment('正确答案');
            $table->text('text_analysis')->nullable()->comment('文字解析');
            $table->string('audio_analysis')->comment('音频解析');
            $table->string('video_analysis')->comment('视频解析');
            $table->integer('chapter_id')->default(0)->comment('章ID');
            $table->integer('joint_id')->default(0)->comment('节ID');
            $table->integer('point_id')->default(0)->comment('考点ID');
            $table->smallInteger('type')->default(0)->comment('试题类型');
            $table->smallInteger('item_diffculty')->default(0)->comment('试题难度');
            $table->smallInteger('is_del')->default(0)->comment('删除0否1是');
            $table->smallInteger('is_publish')->default(0)->comment('发布0未发布1已发布');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->nullable()->comment('更新时间');

            $table->index('admin_id');
            $table->index('bank_id');
            $table->index('chapter_id');
            $table->index('joint_id');
            $table->index('point_id');
            $table->index('item_diffculty');
            $table->index('is_publish');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_question_exam');
    }
}
