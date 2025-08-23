<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2024_01_02_000000_create_sites_table.php
return new class extends Migration {
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
