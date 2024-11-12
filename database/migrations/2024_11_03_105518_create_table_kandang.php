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
        Schema::create('kandangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // Pastikan kode unik
            $table->integer('jumlah_unggas');
            $table->string('jenis_unggas');
            $table->enum('status', ['aktif', 'tidak aktif']);
            $table->timestamp('deactivated_at')->nullable(); // Tanggal dinonaktifkan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kandangs');
    }
};
