<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoIdToLdVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ld_videos', function (Blueprint $table) {
            $table->tinyInteger('mt_video_id')->default(0)->comment('欢拓视频ID');
            $table->string('mt_video_name')->nullable()->comment('欢拓视频标题');
            $table->string('mt_url')->nullable()->comment('欢拓视频临时观看地址'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ld_videos', function (Blueprint $table) {
            $table->dropColumn('mt_video_id');
            $table->dropColumn('mt_video_name');
            $table->dropColumn('mt_url');
        });
    }
}
