<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StudentGrade extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function rpsAssessment()
    {
        return $this->belongsTo(RpsAssessment::class);
    }
}
