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
        Schema::create('reassigned_users', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('rslip_id');
    $table->unsignedBigInteger('slip_id');
    $table->unsignedBigInteger('creator_id');
    $table->unsignedBigInteger('reassigned_id');
    $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('reassigned_user');
    }
};
