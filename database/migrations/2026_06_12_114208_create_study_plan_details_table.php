<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_plan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['study_plan_id', 'class_schedule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_details');
    }
};
