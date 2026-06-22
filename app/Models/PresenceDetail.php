<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresenceDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function presence(): BelongsTo
    {
        return $this->belongsTo(Presence::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
