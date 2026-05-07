<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Rps extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rps';

    protected $fillable = [
        'subject_id',
        'kurikulum_id',
        'nomor_rps',
        'tanggal_penyusunan',
        'referensi',
        'media_pembelajaran',
        'pengembang_rps',
        'dosen_pengampu',
        'versi',
        'status',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function sessions()
    {
        return $this->hasMany(RpsSession::class, 'rps_id')->orderBy('session_number');
    }
}
