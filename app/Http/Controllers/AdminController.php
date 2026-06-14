<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use App\Models\Dosen;
use App\Models\Rps;

class AdminController extends Controller
{
    public function dashboard()
    {
        $count = [
            'fakultas' => Fakultas::count(),
            'prodi' => Prodi::count(),
            'kurikulum' => Kurikulum::count(),
            'subject' => Subject::count(),
            'user' => User::count(),
        ];

        $faculties = Fakultas::all()->map(function($f) {
            $prodiIds = Prodi::where('id_fakultas', $f->id)->pluck('id');
            
            $f->jumlah_prodi = $prodiIds->count();
            
            $f->jumlah_rps = Rps::whereIn('subject_id', function($query) use ($prodiIds) {
                $query->select('id')->from('subjects')->whereIn('id_prodi', $prodiIds);
            })->count();
            
            $f->jumlah_mahasiswa = Student::whereIn('prodi_id', $prodiIds)->count();
            
            $f->jumlah_dosen = Dosen::whereIn('prodi_id', $prodiIds)->count();
            
            $f->jumlah_kurikulum = Kurikulum::whereIn('id_prodi', $prodiIds)->count();
            
            return $f;
        });
        
        return view('admin.dashboard', compact('count', 'faculties'));
    }
}

