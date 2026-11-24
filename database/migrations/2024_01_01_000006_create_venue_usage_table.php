<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->date('month');
            $table->integer('visits')->default(0);
            $table->timestamps();

            $table->unique(['venue_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_usage');
    }
};
