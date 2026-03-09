<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('logs_route', function (Blueprint $table) {
            $table->id();

            // Foreign reference to routing_slip.id
            $table->unsignedBigInteger('slip_id');

            // Copy of routing_slip.rslip_id (control number)
            $table->string('rslip_id');

            // User who performed the action
            $table->unsignedBigInteger('log_creator');

            // Action description (e.g. Routed, Re-routed, Returned)
            $table->string('log_action');

            // File name (optional)
            $table->string('file')->nullable();

            // Comma-separated routed user IDs
            $table->text('routed_users')->nullable();

            $table->timestamps();

            // Optional but recommended constraints
            $table->foreign('slip_id')
                  ->references('id')
                  ->on('routing_slip')
                  ->onDelete('cascade');

            $table->foreign('log_creator')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('logs_route');
    }
};
