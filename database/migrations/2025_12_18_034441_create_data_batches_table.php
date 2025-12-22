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
        Schema::create('data_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('data_period_id')->nullable();

            $table->string('source_type');           // survey_form, excel_import_siga, dll
            $table->string('source_name')->nullable();
            $table->text('description')->nullable();

            $table->string('status')->default('open'); // open, closed, archived

            $table->uuid('created_by')->nullable();
            $table->timestamp('collected_at')->nullable();

            $table->timestamps();

            $table->index('data_period_id');
            $table->index('source_type');
            $table->index('status');
            $table->index('created_by');

            $table->foreign('data_period_id')
                ->references('id')
                ->on('data_periods')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_batches');
    }
};
