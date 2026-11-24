<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediction_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('run_id')->constrained('prediction_runs')->cascadeOnDelete();
            $table->enum('kind', ['medal', 'maintenance', 'levy']);
            $table->string('subject_ref');
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_results');
    }
};
