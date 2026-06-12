<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'name', 'location', 'status'];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
