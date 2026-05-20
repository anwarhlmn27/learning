<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Quiz extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function rpsAssessment()
    {
        return $this->belongsTo(RpsAssessment::class, 'rps_assessment_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    public function attempts()
    {
        return $this->hasMany(StudentQuizAttempt::class, 'quiz_id');
    }

    public function classTopics()
    {
        return $this->hasMany(ClassTopic::class, 'content_id')->where('type', 'quiz');
    }
}
