<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->default(0)->comment('操作员ID');
            $table->string('order_number', 50)->comment('订单号');
            $table->string('third_party_number', 50)->comment('第三方订单号');
            $table->smallInteger('order_type')->comment('1后台录入2在线支付');
            $table->integer('student_id')->comment('学员ID');
            $table->decimal('price', 10, 2)->comment('金额');
            $table->decimal('lession_price', 10, 2)->comment('原价');
            $table->smallInteger('pay_status')->comment('1定金2尾款3最后一笔款4全款');
            $table->smallInteger('pay_type')->comment('1微信2支付宝');
            $table->smallInteger('status')->default(1)->comment('0未支付1支付待审核2审核成功3审核失败4已退款   ');
            $table->dateTime('pay_time')->comment('支付时间');
            $table->smallInteger('oa_status')->default(0)->comment('OA状态1成功0失败');
            $table->integer('class_id')->comment('课程ID');
            $table->integer('school_id')->comment('学员答案');
            $table->dateTime('refund_time')->comment('退款时间');
            $table->timestamp('create_at')->comment('创建时间');
            $table->dateTime('update_at')->comment('修改时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_order');
    }
}
