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
}
