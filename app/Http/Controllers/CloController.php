<?php

namespace App\Http\Controllers;

use App\Models\Clo;
use App\Models\Subject;
use App\Models\Plo;
use Illuminate\Http\Request;
use Exception;

class CloController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('clos')->with('prerequisite')->get();
        return view('admin.clo.index', compact('subjects'));
    }

    public function manage(Subject $subject)
    {
        $subject->load(['clos.plo']);
        $plos = Plo::with('prodi', 'gp')->orderBy('title_plo')->get();
        return view('admin.clo.manage', compact('subject', 'plos'));
    }

    public function store(Request $request, Subject $subject)
    {
        $request->validate([
            'id_plo'    => 'nullable|exists:plos,id',
            'clo'       => 'required|string|max:50',
            'deskripsi' => 'required|string',
        ], [
            'clo.required'       => 'CLO code is required.',
            'deskripsi.required' => 'Description is required.',
        ]);

        try {
            Clo::create([
                'id_subject' => $subject->id,
                'id_plo'     => $request->id_plo ?: null,
                'clo'        => $request->clo,
                'deskripsi'  => $request->deskripsi,
            ]);

            return redirect()->back()->with('success', 'CLO added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add CLO: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Clo $clo)
    {
        $request->validate([
            'id_plo'    => 'nullable|exists:plos,id',
            'clo'       => 'required|string|max:50',
            'deskripsi' => 'required|string',
        ]);

        try {
            $clo->update([
                'id_plo'    => $request->id_plo ?: null,
                'clo'       => $request->clo,
                'deskripsi' => $request->deskripsi,
            ]);
            return redirect()->back()->with('success', 'CLO updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update CLO: ' . $e->getMessage()]);
        }
    }

    public function destroy(Clo $clo)
    {
        try {
            $clo->delete();
            return redirect()->back()->with('success', 'CLO deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete CLO: ' . $e->getMessage()]);
        }
    }
}
