<?php

namespace App\Http\Controllers;

use App\Models\Visi;
use App\Models\Univ;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisiController extends Controller
{
    public function index()
    {
        $visis = Visi::with(['visible', 'details'])->get();
        return view('admin.visi.index', compact('visis'));
    }

    public function create()
    {
        $univs = Univ::all();
        $fakultas = Fakultas::with('univ')->get();
        $prodis = Prodi::with('fakultas.univ')->get();
        
        return view('admin.visi.create', compact('univs', 'fakultas', 'prodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity_type' => 'required|in:Univ,Fakultas,Prodi',
            'entity_id' => 'required',
            'visi' => 'required',
            'misi' => 'required|array|min:1',
            'misi.*' => 'required|string',
            'tujuan' => 'nullable|array',
            'tujuan.*' => 'required|string',
            'strategi' => 'nullable|array',
            'strategi.*' => 'required|string',
            'doc_penyusunan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_pengesahan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_sosialisasi' => 'nullable|file|mimes:pdf|max:2048',
            'doc_hasil_survey' => 'nullable|file|mimes:pdf|max:2048',
        ],
        [
            'entity_type.required' => 'Please select an entity type.',
            'entity_id.required' => 'Please select an entity.',
            'visi.required' => 'Vision is required.',
            'misi.required' => 'At least one Mission is required.',
            'misi.*.required' => 'Mission content cannot be empty.',
            'tujuan.*.required' => 'Objective content cannot be empty.',
            'strategi.*.required' => 'Strategy content cannot be empty.',
            'doc_penyusunan.max' => 'Doc Penyusunan must be less than 2MB.',
            'doc_pengesahan.max' => 'Doc Pengesahan must be less than 2MB.',
            'doc_sosialisasi.max' => 'Doc Sosialisasi must be less than 2MB.',
            'doc_hasil_survey.max' => 'Doc Hasil Survey must be less than 2MB.',
            'doc_penyusunan.mimes' => 'Doc Penyusunan must be a PDF file.',
            'doc_pengesahan.mimes' => 'Doc Pengesahan must be a PDF file.',
            'doc_sosialisasi.mimes' => 'Doc Sosialisasi must be a PDF file.',
            'doc_hasil_survey.mimes' => 'Doc Hasil Survey must be a PDF file.',
        ]);

        $data = $request->only(['visi']);
        
        $data['visible_id'] = $request->entity_id;
        $data['visible_type'] = "App\\Models\\" . $request->entity_type;

        $existing = Visi::where('visible_id', $data['visible_id'])
                        ->where('visible_type', $data['visible_type'])
                        ->first();
        
        if ($existing) {
            return redirect()->back()->withErrors(['entity_id' => 'This entity already has a Vision & Mission recorded.'])->withInput();
        }

        $fileFields = ['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('docs/visi', 'public');
            }
        }

        $visi = Visi::create($data);

        // Save Details
        if ($request->has('misi') && is_array($request->misi)) {
            $urutan = 1;
            foreach ($request->misi as $konten) {
                $visi->details()->create(['type' => 'misi', 'urutan' => $urutan++, 'konten' => $konten]);
            }
        }
        if ($request->has('tujuan') && is_array($request->tujuan)) {
            $urutan = 1;
            foreach ($request->tujuan as $konten) {
                $visi->details()->create(['type' => 'tujuan', 'urutan' => $urutan++, 'konten' => $konten]);
            }
        }
        if ($request->has('strategi') && is_array($request->strategi)) {
            $urutan = 1;
            foreach ($request->strategi as $konten) {
                $visi->details()->create(['type' => 'strategi', 'urutan' => $urutan++, 'konten' => $konten]);
            }
        }

        return redirect()->route('visi.index')->with('success', 'Vision & Mission added successfully.');
    }

    public function edit(Visi $visi)
    {
        $visi->load('details');
        $univs = Univ::all();
        $fakultas = Fakultas::with('univ')->get();
        $prodis = Prodi::with('fakultas.univ')->get();
        
        return view('admin.visi.edit', compact('visi', 'univs', 'fakultas', 'prodis'));
    }

    public function update(Request $request, Visi $visi)
    {
        $request->validate([
            'visi' => 'required',
            'misi' => 'required|array|min:1',
            'misi.*' => 'required|string',
            'tujuan' => 'nullable|array',
            'tujuan.*' => 'required|string',
            'strategi' => 'nullable|array',
            'strategi.*' => 'required|string',
            'doc_penyusunan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_pengesahan' => 'nullable|file|mimes:pdf|max:2048',
            'doc_sosialisasi' => 'nullable|file|mimes:pdf|max:2048',
            'doc_hasil_survey' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'visi.required' => 'Vision is required.',
            'misi.required' => 'At least one Mission is required.',
            'misi.*.required' => 'Mission content cannot be empty.',
            'tujuan.*.required' => 'Objective content cannot be empty.',
            'strategi.*.required' => 'Strategy content cannot be empty.',
            'doc_penyusunan.max' => 'Doc Penyusunan must be less than 2MB.',
            'doc_pengesahan.max' => 'Doc Pengesahan must be less than 2MB.',
            'doc_sosialisasi.max' => 'Doc Sosialisasi must be less than 2MB.',
            'doc_hasil_survey.max' => 'Doc Hasil Survey must be less than 2MB.',
            'doc_penyusunan.mimes' => 'Doc Penyusunan must be a PDF file.',
            'doc_pengesahan.mimes' => 'Doc Pengesahan must be a PDF file.',
            'doc_sosialisasi.mimes' => 'Doc Sosialisasi must be a PDF file.',
            'doc_hasil_survey.mimes' => 'Doc Hasil Survey must be a PDF file.',
        ]);

        $data = $request->only(['visi']);

        // Handle File Uploads
        $fileFields = ['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($visi->$field) {
                    Storage::disk('public')->delete($visi->$field);
                }
                $data[$field] = $request->file($field)->store('docs/visi', 'public');
            }
        }

        $visi->update($data);

        // Delete old details
        $visi->details()->delete();

        // Save New Details
        if ($request->has('misi') && is_array($request->misi)) {
            $urutan = 1;
            foreach ($request->misi as $konten) {
                $visi->details()->create(['type' => 'misi', 'urutan' => $urutan++, 'konten' => $konten]);
            }
        }
        if ($request->has('tujuan') && is_array($request->tujuan)) {
            $urutan = 1;
            foreach ($request->tujuan as $konten) {
                $visi->details()->create(['type' => 'tujuan', 'urutan' => $urutan++, 'konten' => $konten]);
            }
        }
        if ($request->has('strategi') && is_array($request->strategi)) {
            $urutan = 1;
            foreach ($request->strategi as $konten) {
                $visi->details()->create(['type' => 'strategi', 'urutan' => $urutan++, 'konten' => $konten]);
            }
        }

        return redirect()->route('visi.index')->with('success', 'Vision & Mission updated successfully.');
    }

    public function destroy(Visi $visi)
    {
        try {
            $filesToDelete = [];
            $fileFields = ['doc_penyusunan', 'doc_pengesahan', 'doc_sosialisasi', 'doc_hasil_survey'];
            foreach ($fileFields as $field) {
                if ($visi->$field) {
                    $filesToDelete[] = $visi->$field;
                }
            }
            
            // Delete related details first
            $visi->details()->delete();

            $visi->delete();
            
            foreach ($filesToDelete as $file) {
                Storage::disk('public')->delete($file);
            }
            
            return redirect()->route('visi.index')->with('success', 'Vision & Mission deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('visi.index')->withErrors(['error' => $this->handleException($e, 'Failed to delete Vision & Mission data.')]);
        }
    }
}
