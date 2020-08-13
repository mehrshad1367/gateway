<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mellat_gateway', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('type');
            $table->string('transId');
            $table->integer('terminal_id');
            $table->string('callback-url');
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
        Schema::dropIfExists('mellat_gateway');
    }
}
