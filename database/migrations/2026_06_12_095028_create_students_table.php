<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('study_program_id')->constrained('study_programs')->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained('faculties')->cascadeOnDelete();
            $table->string('nim', 20)->unique()->comment('Nomor Induk Mahasiswa');
            $table->string('nik', 20)->nullable()->comment('Nomor Induk Kependudukan');
            $table->string('full_name')->comment('Nama Lengkap');
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('religion', ['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu'])->default('islam');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->year('batch')->comment('Tahun Angkatan');
            $table->enum('status', ['aktif', 'cuti', 'nonaktif', 'lulus', 'drop_out', 'mengundurkan_diri'])->default('aktif');
            $table->unsignedTinyInteger('current_semester')->default(1);
            $table->decimal('gpa', 3, 2)->default(0)->comment('IPK');
            $table->unsignedSmallInteger('total_credits_passed')->default(0)->comment('Total SKS Lulus');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
