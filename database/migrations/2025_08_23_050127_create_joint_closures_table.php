<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2024_01_05_000000_create_joint_closures_table.php
return new class extends Migration {
    public function up()
    {
        Schema::create('joint_closures', function (Blueprint $table) {
            $table->id();
            $table->string('closure_id')->unique();
            $table->string('name');
            $table->string('location');
            $table->string('region');
            $table->integer('capacity');
            $table->integer('used_capacity')->default(0);
            $table->enum('status', ['ok', 'not_ok'])->default('ok');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
