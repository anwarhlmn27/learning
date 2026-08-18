<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClassRoom extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * Status helpers
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function isDeleted(): bool
    {
        return $this->status === 'deleted';
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeVisible($query)
    {
        // Exclude soft-deleted classrooms
        return $query->whereIn('status', ['active', 'archived']);
    }

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id')->withoutGlobalScopes();
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
        return $this->users()->whereHas('dosen');
    }

    /**
     * Get only Students (Mahasiswa) in this classroom.
     */
    public function students() {
        return $this->users()->whereHas('student');
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

    public function assignments() {
        return $this->hasMany(Assignment::class, 'class_room_id');
    }

    public function ratings() {
        return $this->hasMany(SessionRating::class, 'class_room_id');
    }

    public function lecturerFeedback() {
        return $this->hasOne(ClassLecturerFeedback::class, 'class_room_id');
    }

    public function hasLecturerFeedback(): bool
    {
        return $this->lecturerFeedback()->exists();
    }

    /**
     * Check if classroom has any active content (topics / assignments / ratings)
     */
    public function hasActiveContent(): bool
    {
        // Mengabaikan topik bertipe 'materi' (modul) karena tidak bisa dihapus sembarangan
        $hasInteractiveTopics = $this->topics()->where('type', '!=', 'materi')->exists();
        return $hasInteractiveTopics || $this->assignments()->exists() || $this->ratings()->exists();
    }
}
