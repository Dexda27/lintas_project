<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2024_01_06_000000_create_core_connections_table.php
return new class extends Migration {
    public function up()
    {
        Schema::create('core_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('closure_id')->constrained('joint_closures');
            $table->foreignId('core_a_id')->constrained('fiber_cores');
            $table->foreignId('core_b_id')->constrained('fiber_cores');
            $table->decimal('splice_loss', 5, 3)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['core_a_id', 'core_b_id']);
        });
    }
};
