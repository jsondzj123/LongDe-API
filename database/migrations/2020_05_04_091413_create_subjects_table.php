<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('pid')->null()->default(0)->comment('科目上级ID');
            $table->string('name')->comment('科目标题');
            $table->string('cover')->comment('科目封面');
            $table->text('description')->comment('科目描述');
            $table->integer('status')->null()->default(0)->comment('科目状态:0未上架1已上架');
            $table->integer('is_del')->null()->default(0)->comment('是否删除：0否1是');
            $table->integer('is_forbid')->null()->default(0)->comment('是否禁用：0否1是');
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
        Schema::dropIfExists('subjects');
    }
}
