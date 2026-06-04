<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpsSession extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rps_sessions';

    protected $fillable = [
        'rps_id',
        'session_number',
        'topic_name',
        'sub_clo',
        'assessment_indicators',
        'evaluation_criteria',
        'learning_materials',
    ];

    public function rps()
    {
        return $this->belongsTo(Rps::class, 'rps_id');
    }

    public function activities()
    {
        return $this->hasMany(RpsActivity::class, 'rps_session_id');
    }

    public function clos()
    {
        return $this->belongsToMany(Clo::class, 'session_clo', 'rps_session_id', 'clo_id');
    }

    public function assessments()
    {
        return $this->hasMany(RpsAssessment::class, 'rps_session_id');
    }

    public function resources()
    {
        return $this->hasMany(SessionResource::class, 'rps_session_id');
    }
}
