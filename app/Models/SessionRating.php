<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SessionRating extends Model
{
    use HasUuids;

    protected $fillable = [
        'class_room_id',
        'session_number',
        'dosen_id',
        'student_id',
        'rating',
        'comments',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
