<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cvlan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('svlan_id')->nullable()->constrained('svlan')->onDelete('set null');
            $table->foreignId('node_id')->nullable()->constrained('node')->onDelete('cascade');
            $table->string('cvlan_slot')->nullable();
            
            // PENAMBAHAN KOLOM NMS DAN PERUBAHAN TIPE DATA
            $table->integer('nms')->nullable(); // <-- DITAMBAHKAN
            $table->integer('metro')->nullable();
            $table->integer('vpn')->nullable();
            $table->integer('inet')->nullable();
            $table->integer('extra')->nullable();
            
            $table->string('no_jaringan')->nullable(); // Dibuat nullable untuk fleksibilitas
            $table->string('nama_pelanggan')->nullable(); // Dibuat nullable untuk fleksibilitas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cvlan');
    }
};