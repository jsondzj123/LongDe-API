<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_admin', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->string('username')->comment('用户名');
            $table->string('password')->comment('密码');
            $table->integer('role_id')->comment('角色ID');
            $table->string('realname')->comment('真实姓名');
            $table->integer('sex')->default(0)->comment('性别');
            $table->string('mobile')->comment('手机号码');
            $table->string('email')->nullable()->comment('邮箱');
            $table->integer('teacher_id')->nullable()->comment('教师ID');
            $table->smallInteger('school_id')->comment('学校ID');
            $table->smallInteger('school_status')->default(0)->comment('学校状态ID 0 分校 1是总校');
            $table->smallInteger('is_forbid')->default(1)->comment('禁用0是1否');
            $table->smallInteger('is_del')->default(1)->comment('删除0是1否');
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
        Schema::dropIfExists('ld_admin');
    }
}
