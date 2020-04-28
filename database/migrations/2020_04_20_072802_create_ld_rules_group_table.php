<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdRulesGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ld_rules_group', function (Blueprint $table) {
            $table->id();
            $table->id();
            $table->timestamps();
        });
        Schema::create('ld_rules_group', function (Blueprint $table) {
            $table->id();
            $table->id();
            $table->timestamps();
        });
        Schema::create('ld_rules_group', function (Blueprint $table) {
            $table->id();
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ld_rules_group');
        Schema::dropIfExists('ld_rules_group');
        Schema::dropIfExists('ld_rules_group');
    }
}
