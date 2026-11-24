<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('venue_type_id')->constrained('venue_types')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->text('address')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->decimal('area_m2', 10, 2)->default(0);
            $table->integer('capacity')->default(0);
            $table->smallInteger('year_built')->nullable();
            $table->string('manager')->nullable();
            $table->enum('state', ['draft', 'validated', 'published'])->default('draft');
            $table->boolean('is_dummy')->default(true);
            $table->string('data_source')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
