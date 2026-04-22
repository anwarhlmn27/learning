<?php

namespace App\Http\Controllers;

use App\Models\Plo;
use App\Models\Prodi;
use App\Models\Gp;
use Illuminate\Http\Request;
use Exception;

class PloController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('plos')->with('fakultas')->get();
        return view('admin.plo.index', compact('prodis'));
    }

    public function manage(Prodi $prodi)
    {
        $prodi->load(['plos.gp', 'gps']);
        return view('admin.plo.manage', compact('prodi'));
    }

    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'id_gp' => 'required|exists:gps,id',
            'title_plo' => 'required|string',
            'plo' => 'required|string',
            'detail' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ], [
            'id_gp.required' => 'Please select a Graduate Profile.',
            'title_plo.required' => 'PLO Title is required.',
            'plo.required' => 'PLO content is required.',
        ]);

        try {
            Plo::create([
                'id_prodi' => $prodi->id,
                'id_gp' => $request->id_gp,
                'title_plo' => $request->title_plo,
                'plo' => $request->plo,
                'detail' => $request->detail,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->back()->with('success', 'Program Learning Outcome (PLO) added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add PLO: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Plo $plo)
    {
        $request->validate([
            'id_gp' => 'required|exists:gps,id',
            'title_plo' => 'required|string',
            'plo' => 'required|string',
            'detail' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            $plo->update($request->only(['id_gp', 'title_plo', 'plo', 'detail', 'deskripsi']));
            return redirect()->back()->with('success', 'PLO updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update PLO: ' . $e->getMessage()]);
        }
    }

    public function destroy(Plo $plo)
    {
        try {
            $plo->delete();
            return redirect()->back()->with('success', 'PLO deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete PLO: ' . $e->getMessage()]);
        }
    }
}
