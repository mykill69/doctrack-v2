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
        
    Schema::create('esig', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('user_id');
                $table->string('esig_file');           // encrypted filename (.enc)
                $table->string('esig_mime')->nullable(); // image/png, application/pdf
                $table->string('esig_ext', 10)->nullable(); // png, jpg, pdf

                $table->timestamps();

                // Foreign key (recommended)
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('esig');
        }
    };

