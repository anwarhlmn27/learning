<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Enrollment extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function classRoom() {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function student() {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
