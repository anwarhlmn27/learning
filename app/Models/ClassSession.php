<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClassSession extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function rpsSession()
    {
        return $this->belongsTo(RpsSession::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
