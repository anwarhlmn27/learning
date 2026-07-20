<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Student;
use App\Models\Plo;
use App\Models\StudentGrade;
use App\Models\Gp;
use App\Models\CloPlo;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        
        $selectedFakultasId = $request->input('fakultas_id');
        $selectedProdiId = $request->input('prodi_id');
        $selectedAngkatan = $request->input('angkatan');
        $selectedStudentId = $request->input('student_id');

        $prodi = null;
        $student = null;
        $plos = collect();
        $gps = collect();
        $grades = collect();

        // Data for dropdowns
        $prodis = collect();
        $angkatans = collect();
        $students = collect();

        if ($selectedFakultasId) {
            $prodis = Prodi::where('id_fakultas', $selectedFakultasId)->orderBy('nama_prodi')->get();
        }

        if ($selectedProdiId) {
            $angkatans = Student::where('prodi_id', $selectedProdiId)
                ->whereNotNull('angkatan')
                ->select('angkatan')
                ->distinct()
                ->orderBy('angkatan', 'desc')
                ->pluck('angkatan');
                
            if ($selectedAngkatan) {
                $students = Student::where('prodi_id', $selectedProdiId)
                    ->where('angkatan', $selectedAngkatan)
                    ->orderBy('nama_student')
                    ->get();
            }
        }

        $radarLabels = [];
        $radarData = [];
        $ploAttainments = [];

        if ($selectedProdiId) {
            $prodi = Prodi::find($selectedProdiId);
            $plos = Plo::where('id_prodi', $selectedProdiId)->orderBy('kode_plo')->get();
            $gps = Gp::where('id_prodi', $selectedProdiId)->get();

            if ($selectedStudentId) {
                $student = Student::find($selectedStudentId);
                
                // Get all grades for this student
                $grades = StudentGrade::with(['rpsAssessment.clo.plos', 'rpsAssessment.session.rps.subject'])
                    ->whereHas('enrollment', function($q) use ($selectedStudentId) {
                        $q->where('student_id', $selectedStudentId);
                    })
                    ->get();
                    
                // Calculate PLO Attainment
                $cloScores = []; // to store calculated clo scores
                $cloWeights = [];
                
                foreach ($grades as $grade) {
                    $assessment = $grade->rpsAssessment;
                    if (!$assessment || !$assessment->clo_id) continue;
                    
                    $cloId = $assessment->clo_id;
                    $score = $grade->score;
                    $weight = $assessment->weight ?? 0;
                    
                    if (!isset($cloScores[$cloId])) {
                        $cloScores[$cloId] = 0;
                        $cloWeights[$cloId] = 0;
                    }
                    
                    $cloScores[$cloId] += ($score * $weight);
                    $cloWeights[$cloId] += $weight;
                }
                
                // Average the CLO scores (Score * Weight / Total Weight)
                foreach ($cloScores as $cloId => $totalWeightedScore) {
                    if ($cloWeights[$cloId] > 0) {
                        $cloScores[$cloId] = $totalWeightedScore / $cloWeights[$cloId];
                    } else {
                        $cloScores[$cloId] = 0;
                    }
                }
                
                // Now map CLO scores to PLOs
                foreach ($plos as $plo) {
                    $radarLabels[] = $plo->kode_plo;
                    
                    // Find CLOs that map to this PLO
                    $mappedClos = CloPlo::where('plo_id', $plo->id)->pluck('clo_id')->toArray();
                    
                    $ploScoreTotal = 0;
                    $ploCloCount = 0;
                    
                    foreach ($mappedClos as $mCloId) {
                        if (isset($cloScores[$mCloId])) {
                            $ploScoreTotal += $cloScores[$mCloId];
                            $ploCloCount++;
                        }
                    }
                    
                    $attainment = $ploCloCount > 0 ? ($ploScoreTotal / $ploCloCount) : 0;
                    $radarData[] = $attainment;
                    
                    $ploAttainments[] = [
                        'plo' => $plo,
                        'attainment' => $attainment,
                        'target' => 70, // Standard minimum target
                        'status' => $attainment >= 70 ? 'Achieved' : 'Need Improvement'
                    ];
                }

            } else {
                // If no student selected, maybe show cohort average, for now just 0
                foreach ($plos as $plo) {
                    $radarLabels[] = $plo->kode_plo;
                    $radarData[] = 0;
                    $ploAttainments[] = [
                        'plo' => $plo,
                        'attainment' => 0,
                        'target' => 70,
                        'status' => 'Data Kosong (Pilih Mahasiswa)'
                    ];
                }
            }
        }

        return view('obe.analytics.index', compact(
            'fakultas',
            'selectedFakultasId',
            'prodis', 
            'selectedProdiId', 
            'angkatans',
            'selectedAngkatan',
            'students',
            'selectedStudentId',
            'prodi', 
            'student',
            'plos', 
            'gps', 
            'grades', 
            'radarLabels', 
            'radarData',
            'ploAttainments'
        ));
    }

    // API for Select2 cascading
    public function getProdis(Request $request)
    {
        $fakultasId = $request->fakultas_id;
        $prodis = Prodi::where('id_fakultas', $fakultasId)->orderBy('nama_prodi')->get();
        return response()->json($prodis);
    }

    public function getAngkatans(Request $request)
    {
        $prodiId = $request->prodi_id;
        $angkatans = Student::where('prodi_id', $prodiId)
            ->whereNotNull('angkatan')
            ->select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');
        return response()->json($angkatans);
    }

    public function getStudents(Request $request)
    {
        $prodiId = $request->prodi_id;
        $angkatan = $request->angkatan;
        $students = Student::where('prodi_id', $prodiId)
            ->where('angkatan', $angkatan)
            ->orderBy('nama_student')
            ->get();
        return response()->json($students);
    }
}
