<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medal_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->cascadeOnDelete();
            $table->string('event');
            $table->smallInteger('year');
            $table->integer('gold')->default(0);
            $table->integer('silver')->default(0);
            $table->integer('bronze')->default(0);
            $table->enum('state', ['draft', 'validated', 'published'])->default('draft');
            $table->boolean('is_dummy')->default(true);
            $table->string('data_source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medal_history');
    }
};
