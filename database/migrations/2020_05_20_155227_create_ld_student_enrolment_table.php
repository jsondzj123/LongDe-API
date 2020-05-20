<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdStudentEnrolmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_student_enrolment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->string('student_id')->default(0)->comment('学员ID');
            $table->integer('parent_id')->default(0)->comment('学科分类ID');
            $table->integer('lession_id')->default(0)->comment('课程ID');
            $table->decimal('lession_price', 10, 2)->comment('课程原价');
            $table->decimal('student_price', 10, 2)->comment('学员价格');
            $table->smallInteger('payment_type')->default(0)->comment('付款类型');
            $table->smallInteger('payment_method')->default(0)->comment('付款方式');
            $table->decimal('payment_fee', 10, 2)->comment('付款金额');
            $table->dateTime('payment_time')->comment('付款时间');
            $table->smallInteger('status')->default(0)->comment('报名状态');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->nullable()->comment('更新时间');

            $table->index('student_id');
            $table->index('parent_id');
            $table->index('lession_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_student_enrolment');
    }
}
