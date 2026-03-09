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
        Schema::create('inter_office_logs', function (Blueprint $table) {
            $table->id();
            $table->string('track_slip');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('user_id');
            $table->text('remarks')->nullable();
            $table->tinyInteger('track_status')->default(0);
            $table->tinyInteger('view_status')->default(0);
            $table->timestamp('view_date')->nullable();
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
        Schema::dropIfExists('inter_office_logs');
    }
};
