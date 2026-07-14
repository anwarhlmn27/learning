<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpsAssessment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'rps_session_id',
        'clo_id',
                'assessment_type',
        'assignment_activities',
        'assessment_scope',
        'how_worked',
        'time_worked',
        'assessment_output',
        'weight',
    ];

    public function session()
    {
        return $this->belongsTo(RpsSession::class, 'rps_session_id');
    }

    public function clo()
    {
        return $this->belongsTo(Clo::class, 'clo_id');
    }
}
