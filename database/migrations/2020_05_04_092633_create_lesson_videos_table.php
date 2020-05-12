<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_videos', function (Blueprint $table) {
            $table->integer('child_id')->unsigned()->comment('课程小节ID');
            $table->integer('video_id')->unsigned()->comment('录播资源ID');
            $table->timestamps();

            $table->foreign('child_id')->references('id')->on('lesson_childs')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('video_id')->references('id')->on('videos')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->index('child_id');
            $table->index('video_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_videos');
    }
}
