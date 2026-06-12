<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('faculty_id')->constrained('faculties')->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained('study_programs')->cascadeOnDelete();
            $table->string('nidn', 20)->unique()->nullable()->comment('Nomor Induk Dosen Nasional');
            $table->string('nidk', 20)->unique()->nullable()->comment('Nomor Induk Dosen Khusus');
            $table->string('nip', 30)->nullable()->comment('Nomor Induk Pegawai');
            $table->string('full_name')->comment('Nama Lengkap');
            $table->string('front_title')->nullable()->comment('Gelar Depan');
            $table->string('back_title')->nullable()->comment('Gelar Belakang');
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->enum('employment_status', ['tetap', 'luar_biasa', 'kontrak'])->default('tetap');
            $table->enum('status', ['active', 'cuti', 'tugas_belajar', 'pensiun', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
