<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ClassTopic extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'content_id');
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'content_id');
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'content_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'content_id');
    }

    public function getContentAttribute()
    {
        switch ($this->type) {
            case 'materi':
                return $this->material;
            case 'assignment':
                return $this->assignment;
            case 'forum':
                return $this->forum;
            case 'quiz':
                return $this->quiz;
            default:
                return null;
        }
    }
}
