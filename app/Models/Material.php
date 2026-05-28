<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Material extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function classTopics()
    {
        return $this->hasMany(ClassTopic::class, 'content_id')->where('type', 'materi');
    }

    public function getFilePathsAttribute()
    {
        $value = $this->attributes['file_path'] ?? null;
        if (empty($value)) return [];
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return array_filter([$value]);
    }

    public function getOriginalFilenamesAttribute()
    {
        $value = $this->attributes['original_filename'] ?? null;
        if (empty($value)) return [];
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return array_filter([$value]);
    }

    public function getLinkUrlsAttribute()
    {
        $value = $this->attributes['link_url'] ?? null;
        if (empty($value)) return [];
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return array_filter([$value]);
    }
}
