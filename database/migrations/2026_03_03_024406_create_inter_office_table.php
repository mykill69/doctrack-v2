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
        Schema::create('inter_office', function (Blueprint $table) {
            $table->id();
            $table->string('track_slip')->unique();
            $table->unsignedBigInteger('creator_id');
            $table->string('user_id');
            $table->string('trans_type');
            $table->string('subject');
            $table->string('file')->nullable();
            $table->tinyInteger('track_status')->default(0);
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inter_office');
    }
};
