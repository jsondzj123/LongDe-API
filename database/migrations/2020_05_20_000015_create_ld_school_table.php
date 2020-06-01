<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_school', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->comment('操作员ID');
            $table->string('name', 100)->comment('分校名称');
            $table->string('logo_url')->comment('分校LOGO');
            $table->text('introduce')->comment('分校介绍');
            $table->string('dns', 100)->comment('分校域名');
            $table->smallInteger('is_del')->default(1)->comment('是否删除：0是1否');
            $table->smallInteger('is_forbid')->default(1)->comment('是否禁用：0是1否');
            $table->dateTime('create_time')->comment('创建时间');
            $table->dateTime('update_time')->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_school');
    }
}
