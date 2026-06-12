<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year',
        'semester',
        'label',
        'is_active',
        'start_date',
        'end_date',
        'krs_start',
        'krs_end',
        'mid_exam_start',
        'mid_exam_end',
        'final_exam_start',
        'final_exam_end',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'krs_start' => 'date',
        'krs_end' => 'date',
        'mid_exam_start' => 'date',
        'mid_exam_end' => 'date',
        'final_exam_start' => 'date',
        'final_exam_end' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
