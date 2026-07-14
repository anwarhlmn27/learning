<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Dosen extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function sessionRatings()
    {
        return $this->hasMany(SessionRating::class, 'dosen_id');
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->sessionRatings()->avg('rating') ?? 0.0, 1);
    }
}
