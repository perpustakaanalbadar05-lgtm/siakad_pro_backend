<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->constrained()->cascadeOnDelete();
            
            $table->decimal('presence_score', 5, 2)->default(0);
            $table->decimal('assignment_score', 5, 2)->default(0);
            $table->decimal('mid_exam_score', 5, 2)->default(0);
            $table->decimal('final_exam_score', 5, 2)->default(0);
            
            $table->decimal('final_grade_number', 5, 2)->default(0);
            $table->string('final_grade_letter', 2)->nullable();
            
            $table->enum('status', ['passed', 'failed'])->nullable();
            
            $table->timestamps();
            
            $table->unique(['student_id', 'class_schedule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_grades');
    }
};
