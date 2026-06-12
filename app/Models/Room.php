<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['building_id', 'code', 'name', 'capacity', 'type', 'status'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
