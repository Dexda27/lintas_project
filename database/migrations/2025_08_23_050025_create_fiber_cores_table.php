<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// database/migrations/2024_01_04_000000_create_fiber_cores_table.php
return new class extends Migration {
    public function up()
    {
        Schema::create('fiber_cores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cable_id')->constrained('cables');
            $table->integer('tube_number');
            $table->integer('core_number');
            $table->enum('status', ['ok', 'not_ok'])->default('ok');
            $table->enum('usage', ['active', 'inactive'])->default('inactive');
            $table->decimal('attenuation', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['cable_id', 'tube_number', 'core_number']);
        });
    }
};
