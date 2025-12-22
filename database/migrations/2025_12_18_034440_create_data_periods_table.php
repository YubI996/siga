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
        Schema::create('data_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('code')->unique(); // contoh: 2025-TW1
            $table->string('name');           // contoh: Data Keluarga TW1 2025
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('type')->default('survey'); // survey, sinkron_siga, sinkron_sandi, dll

            $table->timestamps();

            $table->index('type');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_periods');
    }
};
