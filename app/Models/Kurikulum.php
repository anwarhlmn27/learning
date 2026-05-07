<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\ScopesByProdi;

class Kurikulum extends Model
{
    use HasFactory, HasUuids, ScopesByProdi;

    protected $table = 'kurikulums';

    protected $fillable = [
        'nm_kurikulum',
        'id_prodi',
        'tahun_akademik',
        'berita_acara_fgd',
        'daftar_hadir',
        'notulensi_diskusi',
        'laporan_penyusunan',
        'laporan_sosialisasi',
        'dokumentasi',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'id_prodi');
    }

    public function subjects()
    {
        return $this->hasMany(KurikulumSubject::class, 'id_kurikulum');
    }
}
