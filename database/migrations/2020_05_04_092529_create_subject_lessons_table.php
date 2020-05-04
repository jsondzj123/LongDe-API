<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_lessons', function (Blueprint $table) {
            $table->integer('lesson_id')->unsigned()->comment('课程ID');
            $table->integer('subject_id')->unsigned()->comment('科目ID');
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('lessons')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('lesson_id');
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
        Schema::dropIfExists('subject_lessons');
    }
}
