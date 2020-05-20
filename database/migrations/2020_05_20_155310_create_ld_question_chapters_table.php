<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdQuestionChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_question_chapters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->integer('parent_id')->default(0)->comment('材料父级ID');
            $table->integer('subject_id')->default(0)->comment('科目ID');
            $table->integer('bank_id')->default(0)->comment('题库ID');
            $table->string('name')->comment('名称');
            $table->smallInteger('type')->default(0)->comment('试题类型');
            $table->smallInteger('is_del')->default(0)->comment('删除0否1是');
            $table->dateTime('create_at')->comment('创建时间');
            $table->dateTime('update_at')->nullable()->comment('更新时间');

            $table->index('admin_id');
            $table->index('parent_id');
            $table->index('bank_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_question_chapters');
    }
}
