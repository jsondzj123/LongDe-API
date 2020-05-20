<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdQuestionExamOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_question_exam_option', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('exam_id')->default(0)->comment('试题ID');
            $table->text('option_content')->comment('选项内容');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->comment('更新时间');

            $table->index('exam_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_question_exam_option');
    }
}
