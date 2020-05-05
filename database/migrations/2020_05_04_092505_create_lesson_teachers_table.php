<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_teachers', function (Blueprint $table) {
            $table->integer('lesson_id')->unsigned()->comment('课程ID');
            $table->integer('teacher_id')->unsigned()->comment('教师ID');
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('lessons')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('lesson_id');
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_teachers');
    }
}
