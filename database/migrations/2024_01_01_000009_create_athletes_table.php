<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('name_encrypted');
            $table->smallInteger('birth_year');
            $table->enum('gender', ['m', 'f']);
            $table->foreignId('sport_id')->constrained('sports')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->enum('level', ['club', 'district', 'province', 'national']);
            $table->boolean('is_minor');
            $table->boolean('consent_recorded')->default(false);
            $table->boolean('active')->default(true);
            $table->enum('state', ['draft', 'validated', 'published'])->default('draft');
            $table->boolean('is_dummy')->default(true);
            $table->string('data_source')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
