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
        Schema::create('laporan_harians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kandang')->constrained('kandangs')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_pakan')->constrained('pakans')->onDelete('cascade');
            $table->decimal('jumlah_pakan', 8, 2); // /kg
            $table->integer('telur')->default(0);
            $table->integer('kematian')->default(0);
            $table->integer('jumlah_sakit')->default(0);
            $table->foreignId('id_penyakit')->nullable()->constrained('penyakits')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_harians');
    }
};
