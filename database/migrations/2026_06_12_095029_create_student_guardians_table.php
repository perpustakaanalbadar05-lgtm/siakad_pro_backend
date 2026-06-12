<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('father_name')->nullable()->comment('Nama Ayah');
            $table->string('father_phone', 20)->nullable();
            $table->string('father_job')->nullable()->comment('Pekerjaan Ayah');
            $table->string('mother_name')->nullable()->comment('Nama Ibu');
            $table->string('mother_phone', 20)->nullable();
            $table->string('mother_job')->nullable()->comment('Pekerjaan Ibu');
            $table->string('guardian_name')->nullable()->comment('Nama Wali');
            $table->string('guardian_phone', 20)->nullable();
            $table->string('guardian_relation')->nullable()->comment('Hubungan Wali');
            $table->text('guardian_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_guardians');
    }
};
