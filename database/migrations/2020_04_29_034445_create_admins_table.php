<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->string('username')->unique()->comment('用户名');
            $table->string('password')->comment('密码');
            $table->string('realname')->nullable()->comment('真实姓名');
            $table->integer('sex')->default(0)->comment('性别:0男1女');
            $table->string('mobile')->nullable()->comment('手机号码');
            $table->string('email')->nullable()->comment('邮箱');
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
        Schema::dropIfExists('admins');
    }
}
