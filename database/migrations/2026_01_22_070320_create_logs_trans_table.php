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
        Schema::create('logs_trans', function (Blueprint $table) {
            $table->id();

            // Reference to routing_slip
            $table->unsignedBigInteger('slip_id');
            $table->string('rslip_id');

            // User who created the log
            $table->unsignedBigInteger('creator_id');

            // Transaction details
            $table->string('source')->nullable();
            $table->text('subject')->nullable();
            $table->string('trans_remarks')->nullable();
            $table->text('other_remarks')->nullable();
            $table->text('ass_comment')->nullable();

            // Routing-related
            $table->text('r_users')->nullable();         // routed users (comma-separated IDs)
            $table->text('reassigned_to')->nullable();  // reassigned users/groups

            // File & signature
            $table->string('file')->nullable();
            $table->string('purge_status')->nullable();

            // Status & dates
            $table->integer('trans_status')->default(0);
            $table->date('date_received')->nullable();

            $table->timestamps();

            /* ================= Foreign Keys ================= */

            $table->foreign('slip_id')
                  ->references('id')
                  ->on('routing_slip')
                  ->onDelete('cascade');

            $table->foreign('creator_id')
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
        Schema::dropIfExists('logs_trans');
    }
};
