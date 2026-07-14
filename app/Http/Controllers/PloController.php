<?php

namespace App\Http\Controllers;

use App\Models\Plo;
use App\Models\Prodi;
use App\Models\Gp;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Models\PloTerm;
use App\Models\PloIndicator;

class PloController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('plos')->with('fakultas')->get();
        return view('admin.plo.index', compact('prodis'));
    }

    public function manage(Prodi $prodi)
    {
        $prodi->load(['plos.gps', 'gps']);
        return view('admin.plo.manage', compact('prodi'));
    }

    public function exportMappingPdf(Prodi $prodi)
    {
        $prodi->load(['plos.gps', 'gps']);
        $pdf = Pdf::loadView('admin.plo.pdf_mapping', compact('prodi'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('pemetaan_cpl_pl_' . strtolower(str_replace(' ', '_', $prodi->short_name)) . '.pdf');
    }

    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'id_gps' => 'required|array|min:1',
            'id_gps.*' => 'exists:gps,id',
            'kode_plo' => 'required|string',
            'plo_title' => 'required|string',
            'rumusan_plo' => 'required|string',
            'domain' => 'required|in:Knowledge,Skill,Attitude,General Competency',
            'bloom_level' => 'required|in:C1,C2,C3,C4,C5,C6',
            'kko' => 'required|array|min:1',
            'kko.*' => 'string',
            'indikator_ketercapaian' => 'required|string',
            'target_capaian' => 'required|string',
            'metode_pengukuran' => 'required|in:Direct,Indirect,Both',
            'status' => 'required|in:Draft,Aktif,Revisi',
        ]);

        try {
            $plo = Plo::create([
                'id_prodi' => $prodi->id,
                'kode_plo' => $request->kode_plo,
                'plo_title' => $request->plo_title,
                'rumusan_plo' => $request->rumusan_plo,
                'domain' => $request->domain,
                'bloom_level' => $request->bloom_level,
                'kko' => implode(', ', $request->kko),
                'indikator_ketercapaian' => $request->indikator_ketercapaian,
                'target_capaian' => $request->target_capaian,
                'metode_pengukuran' => $request->metode_pengukuran,
                'status' => $request->status,
            ]);

            $plo->gps()->attach($request->id_gps);

            return redirect()->back()->with('success', 'Program Learning Outcome (PLO) added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add PLO: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Plo $plo)
    {
        $request->validate([
            'id_gps' => 'required|array|min:1',
            'id_gps.*' => 'exists:gps,id',
            'kode_plo' => 'required|string',
            'plo_title' => 'required|string',
            'rumusan_plo' => 'required|string',
            'domain' => 'required|in:Knowledge,Skill,Attitude,General Competency',
            'bloom_level' => 'required|in:C1,C2,C3,C4,C5,C6',
            'kko' => 'required|array|min:1',
            'kko.*' => 'string',
            'indikator_ketercapaian' => 'required|string',
            'target_capaian' => 'required|string',
            'metode_pengukuran' => 'required|in:Direct,Indirect,Both',
            'status' => 'required|in:Draft,Aktif,Revisi',
        ]);

        try {
            $data = $request->only([
                'kode_plo', 'plo_title', 'rumusan_plo', 'domain', 'bloom_level', 
                'indikator_ketercapaian', 'target_capaian', 'metode_pengukuran', 'status'
            ]);
            $data['kko'] = implode(', ', $request->kko);
            
            $plo->update($data);

            $plo->gps()->sync($request->id_gps);

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
            return redirect()->back()->withErrors(['error' => $this->handleException($e, 'Failed to delete PLO.')]);
        }
    }

    public function show(Plo $plo)
    {
        $plo->load(['terms', 'indicators']);
        return view('admin.plo.show', compact('plo'));
    }

    // --- PLO TERMS ---
    public function storeTerm(Request $request, Plo $plo)
    {
        $request->validate(['description' => 'required|string']);
        $plo->terms()->create(['description' => $request->description]);
        return redirect()->back()->with('success', 'Key Term added successfully.');
    }

    public function updateTerm(Request $request, PloTerm $term)
    {
        $request->validate(['description' => 'required|string']);
        $term->update(['description' => $request->description]);
        return redirect()->back()->with('success', 'Key Term updated successfully.');
    }

    public function destroyTerm(PloTerm $term)
    {
        $term->delete();
        return redirect()->back()->with('success', 'Key Term deleted successfully.');
    }

    // --- PLO INDICATORS ---
    public function storeIndicator(Request $request, Plo $plo)
    {
        $request->validate([
            'indicator_code' => 'required|string',
            'indicator_description' => 'required|string'
        ]);
        $plo->indicators()->create([
            'indicator_code' => $request->indicator_code,
            'indicator_description' => $request->indicator_description
        ]);
        return redirect()->back()->with('success', 'Performance Indicator added successfully.');
    }

    public function updateIndicator(Request $request, PloIndicator $indicator)
    {
        $request->validate([
            'indicator_code' => 'required|string',
            'indicator_description' => 'required|string'
        ]);
        $indicator->update([
            'indicator_code' => $request->indicator_code,
            'indicator_description' => $request->indicator_description
        ]);
        return redirect()->back()->with('success', 'Performance Indicator updated successfully.');
    }

    public function destroyIndicator(PloIndicator $indicator)
    {
        $indicator->delete();
        return redirect()->back()->with('success', 'Performance Indicator deleted successfully.');
    }
}
