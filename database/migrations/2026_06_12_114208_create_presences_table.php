<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('meeting_number');
            $table->text('material_description')->nullable();
            $table->timestamps();
            
            $table->unique(['class_schedule_id', 'meeting_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
