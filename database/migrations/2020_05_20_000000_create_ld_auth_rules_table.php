<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdAuthRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_auth_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80)->comment('路由名称');
            $table->string('title', 20)->comment('路由描述');
            $table->smallInteger('parent_id')->default(0)->comment('父级ID');
            $table->string('icon', 50)->nullable()->comment('图标');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->string('condition', 100)->nullable()->comment('身份');
            $table->smallInteger('is_show')->default(0)->comment('状态0否1是');
            $table->smallInteger('is_del')->default(0)->comment('删除0否1是');
            $table->smallInteger('is_forbid')->default(0)->comment('启用0否1是');

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_auth_rules');
    }
}