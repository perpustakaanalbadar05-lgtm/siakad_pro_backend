<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->cascadeOnDelete();
            $table->string('code', 10)->unique()->comment('Kode Prodi');
            $table->string('name')->comment('Nama Program Studi');
            $table->string('short_name', 20)->nullable();
            $table->enum('level', ['D3', 'S1', 'S2', 'S3'])->default('S1');
            $table->string('accreditation', 5)->nullable()->default('B');
            $table->string('head_name')->nullable()->comment('Nama Kaprodi');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
