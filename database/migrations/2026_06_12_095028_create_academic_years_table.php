<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('year', 9)->comment('Contoh: 2026/2027');
            $table->enum('semester', ['ganjil', 'genap', 'pendek'])->default('ganjil');
            $table->string('label')->comment('Contoh: Ganjil 2026/2027');
            $table->boolean('is_active')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('krs_start')->nullable()->comment('Mulai pengisian KRS');
            $table->date('krs_end')->nullable()->comment('Akhir pengisian KRS');
            $table->date('mid_exam_start')->nullable()->comment('Mulai UTS');
            $table->date('mid_exam_end')->nullable()->comment('Akhir UTS');
            $table->date('final_exam_start')->nullable()->comment('Mulai UAS');
            $table->date('final_exam_end')->nullable()->comment('Akhir UAS');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
