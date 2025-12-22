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
         Schema::create('records', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Asal data
            $table->uuid('data_batch_id')->nullable();
            $table->uuid('data_period_id')->nullable(); // denormalisasi utk query cepat

            // Wilayah & OPD
            $table->uuid('rt_id')->nullable();
            $table->uuid('kelurahan_id')->nullable();
            $table->uuid('kecamatan_id')->nullable();
            $table->uuid('opd_id')->nullable();

            // Penginput
            $table->uuid('created_by');

            // Identitas terlindungi
            $table->string('nik_hash', 128)->nullable()->index();
            $table->text('nik_encrypted')->nullable();

            $table->text('nama_encrypted')->nullable();
            $table->text('alamat_encrypted')->nullable();
            $table->text('koordinat_encrypted')->nullable();

            // Data mentah dari Excel atau form
            $table->json('raw')->nullable();

            // Validasi & status
            $table->boolean('is_valid')->default(true);
            $table->json('validation_errors')->nullable();

            // Status sinkronisasi ke SIGA / Sandi Data
            $table->string('sync_status')->default('pending'); // pending, success, failed
            $table->text('sync_error')->nullable();
            $table->timestamp('synced_at')->nullable();

            // Mekanisme current data
            $table->boolean('is_current')->default(true);
            $table->timestamp('effective_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();

            // Index untuk performa query
            $table->index('data_batch_id');
            $table->index('data_period_id');
            $table->index('rt_id');
            $table->index('kelurahan_id');
            $table->index('kecamatan_id');
            $table->index('opd_id');
            $table->index('created_by');
            $table->index('is_current');
            $table->index('sync_status');

            // Foreign keys (yang sudah pasti tabelnya ada)
            $table->foreign('data_batch_id')
                ->references('id')
                ->on('data_batches')
                ->nullOnDelete();

            $table->foreign('data_period_id')
                ->references('id')
                ->on('data_periods')
                ->nullOnDelete();

            $table->foreign('rt_id')
                ->references('id')
                ->on('rts')
                ->nullOnDelete();

            $table->foreign('kelurahan_id')
                ->references('id')
                ->on('kelurahans')
                ->nullOnDelete();

            $table->foreign('kecamatan_id')
                ->references('id')
                ->on('kecamatans')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            // opds table belum ada → FK ini bisa diaktifkan nanti
            // $table->foreign('opd_id')
            //     ->references('id')
            //     ->on('opds')
            //     ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
