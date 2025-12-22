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
         Schema::create('kelurahans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('kecamatan_id');
            $table->string('kode', 20)->unique();
            $table->string('nama');

            $table->timestamps();

            $table->index('kecamatan_id');
            $table->index('nama');

            $table->foreign('kecamatan_id')
                ->references('id')
                ->on('kecamatans')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelurahans');
    }
};
