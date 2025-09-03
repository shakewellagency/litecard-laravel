<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_lite_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email');
            $table->string('card_id')->unique();
            $table->string('template_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('email');
            $table->index('user_id');
            $table->index('card_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_lite_cards');
    }
};