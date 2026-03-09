<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('routing_pdf', function (Blueprint $table) {
            $table->id();

            // Proper FK to routing_slip
            $table->unsignedBigInteger('routing_slip_id')->index();

            // Business identifier (NO FK)
            $table->string('rslip_id')->index();

            $table->string('op_ctrl')->nullable();

            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('pres_id')->nullable();

            $table->string('pres_dept')->nullable();

            $table->text('trans_remarks')->nullable();
            $table->text('other_remarks')->nullable();

            $table->text('routed_users')->nullable();
            $table->text('reassigned_to')->nullable();
            
            $table->string('routing_action')->nullable();

            $table->dateTime('date_received')->nullable();

            $table->timestamps();

            /* ================= Foreign Keys ================= */

            $table->foreign('routing_slip_id')
                  ->references('id')
                  ->on('routing_slip')
                  ->onDelete('cascade');

           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routing_pdf');
    }
};
