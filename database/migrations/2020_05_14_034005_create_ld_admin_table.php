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
            $table->integer('admin_id')->comment('操作员ID');
            $table->string('username')->nullable()->comment('用户名');
            $table->string('password')->nullable()->comment('密码');
            $table->integer('role_id')s ->comment('角色ID');
            $table->string('realname')->nullable()->comment('真实姓名');
            $table->integer('sex')->nullable()->default(0)->comment('性别');
            $table->integer('mobile')->comment('手机号码');
            $table->integer('email')->comment('邮箱');
            $table->integer('teacher_id')->comment('教师ID');
            $table->smallInteger('school_id')->comment('学校ID');
            $table->smallInteger('school_status')->nullable()->default(0)->comment('学校状态ID');
            $table->smallInteger('is_forbid')->nullable()->default(0)->comment('禁用0是1否');
            $table->smallInteger('is_del')->nullable()->default(0)->comment('删除0是1否');
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
