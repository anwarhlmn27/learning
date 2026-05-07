<?php

namespace App\Http\Controllers;

use App\Models\BahanKajian;
use App\Models\Prodi;
use App\Models\Plo;
use App\Models\KategoriBK;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class BahanKajianController extends Controller
{
    public function index()
    {
        $prodis = Prodi::withCount('bahanKajians')->with('fakultas')->get();
        return view('admin.bahan_kajian.index', compact('prodis'));
    }

    public function manage(Prodi $prodi)
    {
        $prodi->load(['bahanKajians.plos', 'bahanKajians.kategoriBK', 'plos', 'kategoriBks']);
        return view('admin.bahan_kajian.manage', compact('prodi'));
    }

    public function exportMappingPdf(Prodi $prodi)
    {
        $prodi->load(['bahanKajians.plos', 'plos']);
        $pdf = Pdf::loadView('admin.bahan_kajian.pdf_mapping', compact('prodi'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('pemetaan_cpl_bk_' . strtolower(str_replace(' ', '_', $prodi->short_name)) . '.pdf');
    }

    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'id_plos' => 'required|array|min:1',
            'id_plos.*' => 'exists:plos,id',
            'kode_bk' => 'required|string|unique:bahan_kajians,kode_bk',
            'nm_bahan_kajian' => 'required|string',
            'deskripsi' => 'required|string',
            'sub_bk' => 'required|string',
            'id_kategori_bk' => 'required|exists:kategori_bk,id',
            'tingkat_kedalaman' => 'required|in:Introductory,Intermediate,Advanced',
            'sumber_acuan' => 'required|string',
            'status' => 'required|in:Aktif,Revisi,Tidak Aktif',
        ]);

        try {
            $bahanKajian = BahanKajian::create([
                'id_prodi' => $prodi->id,
                'kode_bk' => $request->kode_bk,
                'nm_bahan_kajian' => $request->nm_bahan_kajian,
                'deskripsi' => $request->deskripsi,
                'sub_bk' => $request->sub_bk,
                'id_kategori_bk' => $request->id_kategori_bk,
                'tingkat_kedalaman' => $request->tingkat_kedalaman,
                'sumber_acuan' => $request->sumber_acuan,
                'status' => $request->status,
            ]);

            $bahanKajian->plos()->attach($request->id_plos);

            return redirect()->back()->with('success', 'Bahan Kajian (BK) added successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to add Bahan Kajian: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, BahanKajian $bahanKajian)
    {
        $request->validate([
            'id_plos' => 'required|array|min:1',
            'id_plos.*' => 'exists:plos,id',
            'kode_bk' => 'required|string|unique:bahan_kajians,kode_bk,' . $bahanKajian->id,
            'nm_bahan_kajian' => 'required|string',
            'deskripsi' => 'required|string',
            'sub_bk' => 'required|string',
            'id_kategori_bk' => 'required|exists:kategori_bk,id',
            'tingkat_kedalaman' => 'required|in:Introductory,Intermediate,Advanced',
            'sumber_acuan' => 'required|string',
            'status' => 'required|in:Aktif,Revisi,Tidak Aktif',
        ]);

        try {
            $bahanKajian->update($request->only([
                'kode_bk', 'nm_bahan_kajian', 'deskripsi', 'sub_bk', 'id_kategori_bk', 
                'tingkat_kedalaman', 'sumber_acuan', 'status'
            ]));

            $bahanKajian->plos()->sync($request->id_plos);

            return redirect()->back()->with('success', 'Bahan Kajian (BK) updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update Bahan Kajian: ' . $e->getMessage()]);
        }
    }

    public function destroy(BahanKajian $bahanKajian)
    {
        try {
            $bahanKajian->delete();
            return redirect()->back()->with('success', 'Bahan Kajian (BK) deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete Bahan Kajian. Constraint error might have occurred.']);
        }
    }

    public function manageKategori(Prodi $prodi)
    {
        $prodi->load('kategoriBks');
        return view('admin.bahan_kajian.kategori', compact('prodi'));
    }

    public function storeKategori(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nm_kategori' => 'required|string|max:255',
        ]);

        KategoriBK::create([
            'id_prodi' => $prodi->id,
            'nm_kategori' => $request->nm_kategori,
        ]);

        return redirect()->back()->with('success', 'Kategori Bahan Kajian added successfully.');
    }

    public function updateKategori(Request $request, KategoriBK $kategori)
    {
        $request->validate([
            'nm_kategori' => 'required|string|max:255',
        ]);

        $kategori->update($request->only('nm_kategori'));

        return redirect()->back()->with('success', 'Kategori Bahan Kajian updated successfully.');
    }

    public function destroyKategori(KategoriBK $kategori)
    {
        try {
            $kategori->delete();
            return redirect()->back()->with('success', 'Kategori Bahan Kajian deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete Kategori. It may be used by existing Bahan Kajian.']);
        }
    }
}
