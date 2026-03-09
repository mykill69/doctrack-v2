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
        Schema::table('routing_slip', function (Blueprint $table) {
            $table->tinyInteger('validity_status')->nullable()->after('validity');
        });
    }

    public function down(): void
    {
        Schema::table('routing_slip', function (Blueprint $table) {
            $table->dropColumn('validity_status');
        });
    }
};
