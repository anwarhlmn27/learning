<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpsActivity extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rps_activities';

    protected $fillable = [
        'rps_session_id',
        'type',
        'duration',
        'content',
    ];

    public function session()
    {
        return $this->belongsTo(RpsSession::class, 'rps_session_id');
    }
}
