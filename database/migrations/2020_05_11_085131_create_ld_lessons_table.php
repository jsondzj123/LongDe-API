<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->comment('操作员ID');
            $table->string('title')->nullable()->comment('课程名称');
            $table->string('keyword')->nullable()->comment('关键词');
            $table->string('cover')->nullable()->comment('封面');
            $table->text('description')->nullable()->comment('描述');
            $table->text('introduction')->nullable()->comment('介绍');
            $table->text('url')->nullable()->comment('课程资料');
            $table->smallInteger('is_public')->nullable()->default(0)->comment('公开0否1是');
            $table->decimal('price', 10, 2)->nullable()->comment('定价');
            $table->decimal('favorable_price', 10, 2)->nullable()->comment('优惠价格');
            $table->smallInteger('method')->nullable()->default(0)->comment('授课方式1录播2直播3其他');
            $table->integer('ttl')->nullable()->default(0)->comment('有效期');
            $table->integer('buy_num')->nullable()->default(0)->comment('购买基数');
            $table->smallInteger('status')->nullable()->default(0)->comment('上架0否1是');
            $table->smallInteger('is_del')->nullable()->default(0)->comment('删除0否1是');
            $table->smallInteger('is_forbid')->nullable()->default(0)->comment('禁用0否1是');
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
        Schema::dropIfExists('lessons');
    }
}
