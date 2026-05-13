<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Prodi;
use App\Models\BahanKajian;
use App\Models\Plo;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SubjectController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('subjects')->with('fakultas')->get();
        return view('admin.subjects.index', compact('prodis'));
    }

    public function prodiSubjects(Prodi $prodi)
    {
        $subjects = Subject::where('id_prodi', $prodi->id)
            ->with(['prerequisite', 'prodi', 'plos'])
            ->orderBy('semester')
            ->orderBy('kode_subject')
            ->get();
            
        $plos = Plo::where('id_prodi', $prodi->id)
            ->orderBy('kode_plo')
            ->get();
            
        return view('admin.subjects.prodi_subjects', compact('subjects', 'prodi', 'plos'));
    }

    public function create(Request $request)
    {
        $subjects = Subject::all();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $bks = BahanKajian::orderBy('kode_bk')->get();
        $plos = Plo::orderBy('kode_plo')->get();
        $selected_prodi_id = $request->query('prodi_id');
        
        return view('admin.subjects.create', compact('subjects', 'prodis', 'bks', 'plos', 'selected_prodi_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_prodi' => 'required|exists:prodis,id',
            'kode_subject' => 'required|string|unique:subjects,kode_subject',
            'nama_subject' => 'required|string|max:255',
            'sks_t' => 'required|integer|min:0',
            'sks_p' => 'required|integer|min:0',
            'sks_pl' => 'required|integer|min:0',
            'total_sks' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:14',
            'jenis_subject' => 'required|in:Wajib Prodi,Wajib Universitas,Pilihan',
            'deskripsi' => 'required|string',
            'prerequisite_id' => 'nullable|exists:subjects,id',
            'status' => 'required|in:Aktif,Revisi,Tidak Aktif',
            'bks' => 'nullable|array',
            'bks.*' => 'exists:bahan_kajians,id',
            'plos' => 'nullable|array',
            'plos.*' => 'exists:plos,id',
            'assessments' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $subject = Subject::create($request->except(['bks', 'plos', 'plo_levels']));
            
            if ($request->has('bks')) {
                $subject->bks()->sync($request->bks);
            }
            if ($request->has('plos')) {
                $syncData = [];
                foreach ($request->plos as $ploId) {
                    $syncData[$ploId] = ['mapping_level' => $request->plo_levels[$ploId] ?? 'I'];
                }
                $subject->plos()->sync($syncData);
            }

            DB::commit();
            return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to create subject: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Subject $subject)
    {
        $subjects = Subject::where('id', '!=', $subject->id)->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $bks = BahanKajian::orderBy('kode_bk')->get();
        $plos = Plo::orderBy('kode_plo')->get();
        $subject->load(['bks', 'plos', 'clos']);

        return view('admin.subjects.edit', compact('subject', 'subjects', 'prodis', 'bks', 'plos'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'id_prodi' => 'required|exists:prodis,id',
            'kode_subject' => 'required|string|unique:subjects,kode_subject,' . $subject->id,
            'nama_subject' => 'required|string|max:255',
            'sks_t' => 'required|integer|min:0',
            'sks_p' => 'required|integer|min:0',
            'sks_pl' => 'required|integer|min:0',
            'total_sks' => 'required|integer|min:1',
            'semester' => 'required|integer|min:1|max:14',
            'jenis_subject' => 'required|in:Wajib Prodi,Wajib Universitas,Pilihan',
            'deskripsi' => 'required|string',
            'prerequisite_id' => 'nullable|exists:subjects,id|different:id',
            'status' => 'required|in:Aktif,Revisi,Tidak Aktif',
            'bks' => 'nullable|array',
            'bks.*' => 'exists:bahan_kajians,id',
            'plos' => 'nullable|array',
            'plos.*' => 'exists:plos,id',
            'plo_levels' => 'nullable|array',
            'assessments' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $subject->update($request->except(['bks', 'plos', 'plo_levels']));
            
            if ($request->has('bks')) {
                $subject->bks()->sync($request->bks);
            } else {
                $subject->bks()->detach();
            }

            if ($request->has('plos')) {
                $syncData = [];
                foreach ($request->plos as $ploId) {
                    $syncData[$ploId] = ['mapping_level' => $request->plo_levels[$ploId] ?? 'I'];
                }
                $subject->plos()->sync($syncData);
            } else {
                $subject->plos()->detach();
            }

            DB::commit();
            return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update subject: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            // Check if this subject is a prerequisite for others
            if ($subject->dependents()->count() > 0) {
                return redirect()->back()->withErrors(['error' => 'Cannot delete subject because it is a prerequisite for other subjects.']);
            }
            
            $subject->delete();
            return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to delete subject.')]);
        }
    }

    public function exportMappingBK(Prodi $prodi)
    {
        $subjects = Subject::where('id_prodi', $prodi->id)
            ->with('bks')
            ->orderBy('semester')
            ->orderBy('kode_subject')
            ->get();
        
        $bks = BahanKajian::where('id_prodi', $prodi->id)
            ->orderBy('kode_bk')
            ->get();

        $pdf = Pdf::loadView('admin.subjects.pdf_mapping_bk', compact('subjects', 'bks', 'prodi'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('Mapping_BK_' . $prodi->short_name . '.pdf');
    }

    public function exportMappingPLO(Prodi $prodi)
    {
        $subjects = Subject::where('id_prodi', $prodi->id)
            ->with('plos')
            ->orderBy('semester')
            ->orderBy('kode_subject')
            ->get();
        
        $plos = Plo::where('id_prodi', $prodi->id)
            ->orderBy('kode_plo')
            ->get();

        $pdf = Pdf::loadView('admin.subjects.pdf_mapping_plo', compact('subjects', 'plos', 'prodi'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Mapping_PLO_' . $prodi->short_name . '.pdf');
    }

}
