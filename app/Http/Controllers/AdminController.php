<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\Subject;
use App\Models\User;

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
        
        return view('admin.dashboard', compact('count'));
    }
}
