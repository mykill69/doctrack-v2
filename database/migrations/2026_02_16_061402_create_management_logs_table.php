<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('management_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');      // Who made the change
            $table->string('model_type');               // e.g., Office or Group
            $table->unsignedBigInteger('model_id');    // ID of the model
            $table->string('action');                   // e.g., created, updated
            $table->json('changes')->nullable();       // Store updated fields
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('management_logs');
    }
};
