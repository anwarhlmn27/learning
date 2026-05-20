<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClassRoom extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Get all users (Lecturers, Students, and BAAK staff) enrolled in this classroom.
     */
    public function users() {
        return $this->belongsToMany(User::class, 'class_users', 'class_room_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Get only Dosen (Lecturers) in this classroom.
     */
    public function dosens() {
        return $this->users()->whereHas('roles', function($q) {
            $q->where('name', 'dosen');
        });
    }

    /**
     * Get only Students (Mahasiswa) in this classroom.
     */
    public function students() {
        return $this->users()->whereHas('roles', function($q) {
            $q->whereIn('name', ['student', 'mahasiswa']);
        });
    }

    /**
     * Get only BAAK staff in this classroom.
     */
    public function baaks() {
        return $this->users()->whereHas('roles', function($q) {
            $q->where('name', 'baak');
        });
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'class_room_id');
    }

    public function topics() {
        return $this->hasMany(ClassTopic::class, 'class_room_id')->orderBy('session_number');
    }
}
