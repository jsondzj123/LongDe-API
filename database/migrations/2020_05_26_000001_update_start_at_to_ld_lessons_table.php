<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStartAtToLdLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ld_lessons', function (Blueprint $table) {
            $table->timestamp('start_at')->nullable()->comment('公开课开始时间')->change();
            $table->timestamp('end_at')->nullable()->comment('公开课结束时间')->change();
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
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
        });
    }
}
