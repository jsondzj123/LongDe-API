<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_student', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('school_id')->default(0)->comment('分校ID');
            $table->char('phone', 11)->comment('手机号');
            $table->string('real_name')->comment('姓名');
            $table->smallInteger('sex')->default(1)->comment('手机号');
            $table->smallInteger('papers_type')->default(0)->comment('证件类型');
            $table->string('papers_num')->comment('证件号码');
            $table->string('birthday')->comment('出生日期');
            $table->string('address_locus')->comment('户口所在地');
            $table->integer('age')->default(0)->comment('年龄');
            $table->smallInteger('educational')->default(0)->comment('学历');
            $table->string('family_phone', 60)->comment('家庭电话');
            $table->string('office_phone', 60)->comment('办公电话');
            $table->string('contact_people', 60)->comment('紧急联系人');
            $table->string('contact_phone', 60)->comment('紧急联系电话');
            $table->string('email', 60)->comment('邮箱');
            $table->string('qq', 30)->comment('qq号码');
            $table->string('wechat', 30)->comment('微信号');
            $table->string('address')->comment('住址');
            $table->text('remark')->nullable()->comment('备注');
            $table->smallInteger('is_forbid')->default(1)->comment('是否禁用：1否2是');
            $table->smallInteger('enroll_status')->default(0)->comment('报名状态0未报名1已报名');
            $table->smallInteger('state_status')->default(0)->comment('开课状态0未开课1部分未开课2全部未开课');
            $table->smallInteger('reg_source')->default(0)->comment('来源：0官网注册1手机端线下录入');
            $table->dateTime('create_time')->comment('创建时间');
            $table->dateTime('update_time')->nullable()->comment('更新时间');

            $table->index('school_id');
            $table->index('is_forbid');
            $table->index('state_status');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_student');
    }
}
