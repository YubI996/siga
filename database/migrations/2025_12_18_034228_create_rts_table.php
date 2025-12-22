<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('rts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('kelurahan_id');
            $table->string('kode_rt', 5);

            $table->timestamps();

            $table->index('kelurahan_id');
            $table->index(['kelurahan_id', 'kode_rt']);

            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('kelurahans')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rts');
    }
};
