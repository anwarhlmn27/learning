<?php

namespace App\Http\Controllers;

use App\Models\CourseMaping;
use App\Models\Prodi;
use App\Models\Subject;
use App\Models\Plo;
use Illuminate\Http\Request;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class CourseMapingController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('courseMapings')->with('fakultas')->get();
        return view('admin.course_mapping.index', compact('prodis'));
    }

    public function manage(Prodi $prodi)
    {
        $prodi->load(['courseMapings.subject', 'courseMapings.plo', 'plos']);
        
        // Get all subjects
        $subjects = Subject::all(); 

        $plos = Plo::where('id_prodi', $prodi->id)->get();
        
        return view('admin.course_mapping.manage', compact('prodi', 'subjects', 'plos'));
    }

    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'id_subject' => 'required|exists:subjects,id',
            'id_plo' => 'required|exists:plos,id',
            'level_maping' => 'required|in:I,R,M',
        ], [
            'id_subject.required' => 'Mata Kuliah wajib dipilih.',
            'id_plo.required' => 'PLO wajib dipilih.',
            'level_maping.required' => 'Level mapping wajib diisi.',
            'level_maping.in' => 'Level mapping harus bernilai I, R, atau M.',
        ]);

        try {
            CourseMaping::create([
                'id_prodi' => $prodi->id,
                'id_subject' => $request->id_subject,
                'id_plo' => $request->id_plo,
                'level_maping' => $request->level_maping,
            ]);

            return redirect()->back()->with('success', 'Curriculum Mapping berhasil ditambahkan.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menambahkan Mapping: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, CourseMaping $courseMaping)
    {
        $request->validate([
            'id_subject' => 'required|exists:subjects,id',
            'id_plo' => 'required|exists:plos,id',
            'level_maping' => 'required|in:I,R,M',
        ], [
            'id_subject.required' => 'Mata Kuliah wajib dipilih.',
            'id_plo.required' => 'PLO wajib dipilih.',
            'level_maping.required' => 'Level mapping wajib diisi.',
            'level_maping.in' => 'Level mapping harus bernilai I, R, atau M.',
        ]);

        try {
            $courseMaping->update($request->only(['id_subject', 'id_plo', 'level_maping']));
            return redirect()->back()->with('success', 'Curriculum Mapping berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui Mapping: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(CourseMaping $courseMaping)
    {
        try {
            $courseMaping->delete();
            return redirect()->back()->with('success', 'Curriculum Mapping berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Gagal menghapus Mapping.')]);
        }
    }

    public function exportPdf(Prodi $prodi)
    {
        $prodi->load(['plos']);
        
        $mappings = CourseMaping::with('subject')->where('id_prodi', $prodi->id)->get();
        
        $subjects = Subject::whereIn('id', $mappings->pluck('id_subject')->unique())
                           ->orderBy('semester')
                           ->get();
                           
        $plos = $prodi->plos;
        
        $stages = [
            'FOUNDATION STAGE (Semester 1-2)' => [],
            'CORE STAGE (Semester 3-4)' => [],
            'SPECIALIZATION STAGE (Semester 5-6)' => [],
            'PROFESSIONAL STAGE (Semester 7-8)' => [],
            'OTHER STAGE' => [],
        ];
        
        foreach ($subjects as $subject) {
            $sem = (int) $subject->semester;
            if ($sem == 1 || $sem == 2) {
                $stages['FOUNDATION STAGE (Semester 1-2)']['courses'][] = $subject;
            } elseif ($sem == 3 || $sem == 4) {
                $stages['CORE STAGE (Semester 3-4)']['courses'][] = $subject;
            } elseif ($sem == 5 || $sem == 6) {
                $stages['SPECIALIZATION STAGE (Semester 5-6)']['courses'][] = $subject;
            } elseif ($sem == 7 || $sem == 8) {
                $stages['PROFESSIONAL STAGE (Semester 7-8)']['courses'][] = $subject;
            } else {
                $stages['OTHER STAGE']['courses'][] = $subject;
            }
        }
        
        foreach ($stages as $key => $stage) {
            if (empty($stage)) {
                unset($stages[$key]);
            }
        }
        
        $matrix = [];
        foreach ($mappings as $map) {
            $matrix[$map->id_subject][$map->id_plo] = $map->level_maping;
        }

        $pdf = Pdf::loadView('admin.course_mapping.pdf', compact('prodi', 'plos', 'stages', 'matrix'))
                  ->setPaper('a4', 'landscape');
                  
        return $pdf->download('Course_Mapping_' . $prodi->kode_prodi . '.pdf');
    }
}
