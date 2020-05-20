<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdStudentDoTitleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_student_do_title', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->default(0)->comment('学员ID');
            $table->integer('bank_id')->default(0)->comment('题库ID');
            $table->integer('papers_id')->default(0)->comment('试卷ID');
            $table->integer('exam_id')->default(0)->comment('试题ID');
            $table->string('answer')->comment('学员答案');
            $table->smallInteger('is_right')->default(0)->comment('对错类型0错误1正确');
            $table->dateTime('create_at')->comment('创建时间');

            $table->index('student_id');
            $table->index('bank_id');
            $table->index('exam_id');
            $table->index('papers_id');
            $table->index('create_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_student_do_title');
    }
}
