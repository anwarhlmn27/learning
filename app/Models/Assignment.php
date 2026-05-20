<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Assignment extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function rpsAssessment()
    {
        return $this->belongsTo(RpsAssessment::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
