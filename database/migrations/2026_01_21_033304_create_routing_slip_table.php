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
        Schema::create('routing_slip', function (Blueprint $table) {
    $table->id(); // primary key, unique

    // Identifiers
    $table->string('rslip_id'); // remove ->unique()
    $table->string('op_ctrl')->nullable();

    // Creator
    $table->unsignedBigInteger('creator_id');

    // Routing / metadata
    $table->string('pres_dept')->nullable();
    $table->string('source')->nullable();
    $table->text('subject');

    // Remarks
    $table->text('trans_remarks')->nullable();
    $table->text('other_remarks')->nullable();
    $table->text('ass_comment')->nullable();

    // Routing users
    $table->text('set_users_to')->nullable();
    $table->text('routed_users')->nullable();
    $table->unsignedBigInteger('reassigned_to')->nullable();

    // File & signature
    $table->string('file');
    $table->string('purge_status')->nullable();

    // Status & dates
    $table->tinyInteger('routing_status')->default(1);
    $table->dateTime('date_received');
    $table->year('validity');
    $table->tinyInteger('transaction_type')->comment('1 = PRESIDENT, 2 = PERSONNEL');

    $table->timestamps();

    // Indexes
    $table->index('creator_id');
    $table->index('routing_status');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routing_slip');
    }
};
