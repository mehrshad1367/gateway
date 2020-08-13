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
        Schema::create('zarinpal_gateway', function (Blueprint $table) {
            $table->id();
            $table->integer('merchant-id');
            $table->string('type');
            $table->string('callback-url');
            $table->string('server');
            $table->string('mobile');
            $table->string('description');
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
        Schema::dropIfExists('zarinpal_gateway');
    }
}
