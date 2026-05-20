<?php

namespace App\Http\Controllers;

use App\Models\Clo;
use App\Models\Subject;
use App\Models\Plo;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class CloController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('subjects')->with('fakultas')->get();
        return view('admin.clo.index', compact('prodis'));
    }

    public function prodiSubjects(Prodi $prodi)
    {
        $subjects = Subject::where('id_prodi', $prodi->id)->withCount('clos')->with(['prerequisites'])->get();
        return view('admin.clo.prodi_subjects', compact('subjects', 'prodi'));
    }

    public function manage(Subject $subject)
    {
        $subject->load(['clos.plos', 'plos']);
        // Only show PLOs that are mapped to this subject
        $plos = $subject->plos;
        return view('admin.clo.manage', compact('subject', 'plos'));
    }

    public function store(Request $request, Subject $subject)
    {
        $request->validate([
            'kode_clo'    => 'required|string|max:50',
            'deskripsi'   => 'required|string',
            'bloom_level' => 'required|string|max:50',
            'plos'        => 'nullable|array',
            'plos.*'      => 'exists:plos,id',
        ]);

        DB::beginTransaction();
        try {
            $clo = Clo::create([
                'subject_id'  => $subject->id,
                'kode_clo'    => $request->kode_clo,
                'deskripsi'   => $request->deskripsi,
                'bloom_level' => $request->bloom_level,
            ]);

            if ($request->has('plos')) {
                $clo->plos()->sync($request->plos);
            }

            DB::commit();
            return redirect()->back()->with('success', 'CLO added successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to add CLO: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Clo $clo)
    {
        $request->validate([
            'kode_clo'    => 'required|string|max:50',
            'deskripsi'   => 'required|string',
            'bloom_level' => 'required|string|max:50',
            'plos'        => 'nullable|array',
            'plos.*'      => 'exists:plos,id',
        ]);

        DB::beginTransaction();
        try {
            $clo->update([
                'kode_clo'    => $request->kode_clo,
                'deskripsi'   => $request->deskripsi,
                'bloom_level' => $request->bloom_level,
            ]);
            
            if ($request->has('plos')) {
                $clo->plos()->sync($request->plos);
            } else {
                $clo->plos()->detach();
            }

            DB::commit();
            return redirect()->back()->with('success', 'CLO updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update CLO: ' . $e->getMessage()]);
        }
    }

    public function destroy(Clo $clo)
    {
        try {
            $clo->delete();
            return redirect()->back()->with('success', 'CLO deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to delete CLO.')]);
        }
    }
}
