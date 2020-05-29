<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdStudentAccountlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_student_account_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->decimal('price', 10, 2)->comment('金额');
            $table->decimal('endprice', 10, 2)->comment('修改后金额');
            $table->tinyInteger('status')->default(0)->comment('1充值2消费');
            $table->integer('class_id')->default(0)->comment('课程ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_student_account_logs');
    }
}
