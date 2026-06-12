<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'study_program_id',
        'faculty_id',
        'nim',
        'nik',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'phone',
        'email',
        'photo',
        'batch',
        'status',
        'current_semester',
        'gpa',
        'total_credits_passed',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'gpa' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(StudentGuardian::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByBatch($query, $batch)
    {
        return $query->where('batch', $batch);
    }
}
