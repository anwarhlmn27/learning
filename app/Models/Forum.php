<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Forum extends Model
{
    use HasUuids;

    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'forum_id');
    }

    public function classTopics()
    {
        return $this->hasMany(ClassTopic::class, 'content_id')->where('type', 'forum');
    }
}
