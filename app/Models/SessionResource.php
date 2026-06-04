<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SessionResource extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'session_resources';

    protected $fillable = [
        'rps_session_id',
        'nm_resource',
        'type',
        'file_path',
    ];

    public function rpsSession()
    {
        return $this->belongsTo(RpsSession::class, 'rps_session_id');
    }
}
