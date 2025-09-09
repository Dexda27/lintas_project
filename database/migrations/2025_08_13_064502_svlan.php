<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('svlan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('node_id')->nullable()->constrained('node')->onDelete('cascade');
            $table->string('svlan_nms');
            $table->string('svlan_me')->nullable();
            $table->string('svlan_vpn');
            $table->string('svlan_inet');
            $table->string('extra')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('svlan');
    }
};
