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
        Schema::table('logs_trans', function (Blueprint $table) {
            $table->tinyInteger('transaction_type')
                  ->nullable()
                  ->after('slip_id')
                  ->comment('1 = PRESIDENT, 2 = PERSONNEL');
        });
    }

    public function down()
    {
        Schema::table('logs_trans', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
    
};
