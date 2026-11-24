<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youth_orgs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['OKP', 'KarangTaruna']);
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->integer('members')->default(0);
            $table->enum('activity_level', ['low', 'medium', 'high'])->default('medium');
            $table->enum('state', ['draft', 'validated', 'published'])->default('draft');
            $table->boolean('is_dummy')->default(true);
            $table->string('data_source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youth_orgs');
    }
};
