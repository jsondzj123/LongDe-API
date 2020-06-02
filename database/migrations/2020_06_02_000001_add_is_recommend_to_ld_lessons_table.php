<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRecommendToLdLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ld_lessons', function (Blueprint $table) {
            $table->tinyInteger('is_recommend')->default(0)->comment('观看数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ld_lessons', function (Blueprint $table) {
            $table->dropColumn('is_recommend');
        });
    }
}
