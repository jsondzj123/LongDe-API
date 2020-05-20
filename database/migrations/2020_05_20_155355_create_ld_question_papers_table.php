<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdQuestionPapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_question_papers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('subject_id')->default(0)->comment('科目ID');
            $table->integer('bank_id')->default(0)->comment('题库ID');
            $table->string('papers_name')->comment('试卷名称');
            $table->smallInteger('diffculty')->default(0)->comment('试题难度(1代表真题,2代表模拟题,3代表其他)');
            $table->integer('papers_time')->default(0)->comment('答题时间');
            $table->integer('area')->default(0)->comment('所属区域');
            $table->string('cover_img')->comment('封面图片');
            $table->text('content')->comment('简介');
            $table->string('type', 60)->default(0)->comment('试题类型');
            $table->integer('signle_score')->default(0)->comment('单选题每题得分');
            $table->integer('more_score')->default(0)->comment('多选题每题得分');
            $table->integer('judge_score')->default(0)->comment('判断题每题得分');
            $table->integer('options_score')->default(0)->comment('不定项选择题每题得分');
            $table->integer('pack_score')->default(0)->comment('填空题每题得分');
            $table->integer('short_score')->default(0)->comment('简答题每题得分');
            $table->integer('material_score')->default(0)->comment('材料题每题得分');
            $table->smallInteger('is_del')->default(0)->comment('删除0否1是');
            $table->smallInteger('is_publish')->default(0)->comment('节ID');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->comment('更新时间');

            $table->index('admin_id');
            $table->index('bank_id');
            $table->index('diffculty');
            $table->index('is_publish');
            $table->index('papers_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_question_papers');
    }
}
