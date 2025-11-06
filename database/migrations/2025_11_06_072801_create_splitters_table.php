<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('splitters', function (Blueprint $table) {
            $table->id();
            $table->string('splitter_id')->unique();
            $table->string('name');
            $table->string('location');
            $table->string('region');
            $table->integer('capacity');
            $table->integer('used_capacity')->default(0);
            $table->enum('status', ['ok', 'not_ok'])->default('ok');
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('region');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('splitters');
    }
};