<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2024_01_03_000000_create_cables_table.php
return new class extends Migration {
    public function up()
    {
        Schema::create('cables', function (Blueprint $table) {
            $table->id();
            $table->string('cable_id')->unique();
            $table->string('name');
            $table->string('region');
            $table->integer('total_tubes');
            $table->integer('total_cores');
            $table->integer('cores_per_tube');
            $table->enum('status', ['ok', 'not_ok'])->default('ok');
            $table->enum('usage', ['active', 'inactive'])->default('inactive');
            $table->decimal('otdr_length', 10, 2)->nullable();
            $table->foreignId('source_site_id')->constrained('sites');
            $table->foreignId('destination_site_id')->constrained('sites');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
