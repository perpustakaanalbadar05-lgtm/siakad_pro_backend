<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained('study_programs')->cascadeOnDelete();
            $table->string('code', 20)->unique()->comment('Kode Mata Kuliah');
            $table->string('name')->comment('Nama Mata Kuliah');
            $table->unsignedTinyInteger('credits')->default(3)->comment('SKS');
            $table->unsignedTinyInteger('semester')->comment('Semester ke-');
            $table->enum('type', ['wajib', 'pilihan', 'praktikum'])->default('wajib');
            $table->unsignedBigInteger('prerequisite_id')->nullable()->comment('Prasyarat MK');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
