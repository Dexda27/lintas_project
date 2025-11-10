<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poles', function (Blueprint $table) {
            $table->id();
            $table->string('pole_id')->unique();
            $table->string('name');
            $table->string('location');
            $table->string('region');
            $table->enum('type', ['besi', 'beton']);
            $table->enum('height', ['7', '9']);
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['ok', 'not_ok'])->default('ok');
            $table->timestamps();
        });

        // Tabel pivot untuk relasi many-to-many antara poles dan joint_closures
        Schema::create('pole_joint_closure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pole_id')->constrained('poles')->onDelete('cascade');
            $table->foreignId('joint_closure_id')->constrained('joint_closures')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['pole_id', 'joint_closure_id']);
        });

        // Tabel pivot untuk relasi many-to-many antara poles dan splitters
        Schema::create('pole_splitter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pole_id')->constrained('poles')->onDelete('cascade');
            $table->foreignId('splitter_id')->constrained('splitters')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['pole_id', 'splitter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pole_splitter');
        Schema::dropIfExists('pole_joint_closure');
        Schema::dropIfExists('poles');
    }
};
