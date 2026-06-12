<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecturer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'faculty_id',
        'study_program_id',
        'nidn',
        'nidk',
        'nip',
        'full_name',
        'front_title',
        'back_title',
        'gender',
        'birth_place',
        'birth_date',
        'phone',
        'email',
        'address',
        'photo',
        'employment_status',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function getFullNameWithTitleAttribute(): string
    {
        $name = trim(($this->front_title ? $this->front_title . ' ' : '') . $this->full_name);
        if ($this->back_title) {
            $name .= ', ' . $this->back_title;
        }
        return $name;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
