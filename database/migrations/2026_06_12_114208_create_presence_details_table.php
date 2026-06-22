<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presence_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alfa'])->default('alfa');
            $table->timestamps();
            
            $table->unique(['presence_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presence_details');
    }
};
