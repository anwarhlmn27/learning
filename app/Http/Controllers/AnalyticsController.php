<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodi;
use App\Models\Plo;
use App\Models\StudentGrade;
use App\Models\Gp;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $prodis = Prodi::all();
        $selectedProdiId = $request->input('prodi_id');
        $prodi = null;
        $plos = collect();
        $gps = collect();
        $grades = collect();

        if ($selectedProdiId) {
            $prodi = Prodi::find($selectedProdiId);
            $plos = Plo::where('id_prodi', $selectedProdiId)->orderBy('kode_plo')->get();
            $gps = Gp::where('id_prodi', $selectedProdiId)->get();
            $grades = StudentGrade::with(['enrollment.student', 'rpsAssessment.session.rps.subject'])->latest()->take(20)->get();
        } else {
            // Default fallback if no prodi selected, load something to show
            $prodi = $prodis->first();
            if ($prodi) {
                $selectedProdiId = $prodi->id;
                $plos = Plo::where('id_prodi', $selectedProdiId)->orderBy('kode_plo')->get();
                $gps = Gp::where('id_prodi', $selectedProdiId)->get();
            }
            $grades = StudentGrade::with(['enrollment.student', 'rpsAssessment.session.rps.subject'])->latest()->take(10)->get();
        }

        // Mocking Attainment Data (To be replaced with actual complex queries CLO->PLO)
        $radarLabels = [];
        $radarData = [];
        $ploAttainments = [];

        foreach ($plos as $plo) {
            $radarLabels[] = $plo->kode_plo;
            $attainment = rand(60, 95); // TODO: Calculate from average (StudentGrade * AssessmentWeight * CLO_PLO_Weight)
            $radarData[] = $attainment;
            
            $ploAttainments[] = [
                'plo' => $plo,
                'attainment' => $attainment,
                'target' => 70, // Standard minimum target
                'status' => $attainment >= 70 ? 'Achieved' : 'Need Improvement'
            ];
        }

        return view('obe.analytics.index', compact(
            'prodis', 
            'selectedProdiId', 
            'prodi', 
            'plos', 
            'gps', 
            'grades', 
            'radarLabels', 
            'radarData',
            'ploAttainments'
        ));
    }
}
